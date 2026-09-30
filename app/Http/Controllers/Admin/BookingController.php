<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStoreBookingRequest;
use App\Http\Requests\Admin\ConfirmBookingPaymentRequest;
use App\Models\Booking;
use App\Models\BookingTraveller;
use App\Models\Payment;
use App\Models\TourDeparture;
use App\Models\TourPackage;
use App\Models\User;
use App\Services\Bookings\BookingService;
use App\Services\Points\PointSettingService;
use App\Services\Points\PointsRedemptionOtpService;
use App\Services\Points\PointWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly PointWalletService $pointWalletService,
        private readonly PointSettingService $pointSettingService,
        private readonly PointsRedemptionOtpService $pointsRedemptionOtpService
    ) {}

    /**
     * Show the form to create a booking on behalf of a customer.
     */
    public function create(): View
    {
        $users = User::query()
            ->where('role', 'user')
            ->with('pointWallet')
            ->orderBy('name')
            ->get();

        $tours = TourPackage::query()
            ->where('status', TourPackage::STATUS_PUBLISHED)
            ->with([
                'departures' => fn ($query) => $query
                    ->bookable()
                    ->withSum([
                        'bookings as reserved_seats' =>
                            fn ($query) => $query->reserving(),
                    ], 'traveller_count')
                    ->orderBy('departure_date'),
            ])
            ->orderBy('name')
            ->get()
            ->map(function (TourPackage $tour) {
                $tour->setRelation(
                    'departures',
                    $tour->departures
                        ->filter(
                            fn ($departure) => $departure->available_seats > 0
                        )
                        ->values()
                );

                return $tour;
            })
            ->filter(fn (TourPackage $tour) => $tour->departures->isNotEmpty())
            ->values();

        $redemptionSetting = $this->pointSettingService->getBookingRedemption();

        return view(
            'admin.bookings.create',
            compact('users', 'tours', 'redemptionSetting')
        );
    }

    /**
     * Store a booking created by the admin on behalf of a customer.
     *
     * Everything happens atomically in a single request: the seat
     * reservation, an optional points redemption, and an optional
     * payment confirmation. There is no intermediate "checkout" step
     * to abandon, so a booking never sits half-finished with points
     * left unreachable.
     *
     * Real-life scenario: the customer sometimes pays the admin
     * (cash, UPI, bank transfer, Razorpay) before this booking is
     * even entered into the system. In that case the admin ticks
     * "payment already collected" and the booking is created
     * already confirmed & paid. Otherwise it's created pending
     * payment, and can be completed later from the bookings list.
     */
    public function store(
        AdminStoreBookingRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $departure = TourDeparture::query()
            ->findOrFail($data['departure_id']);

        $tour = $departure->tourPackage;

        $customer = User::query()
            ->findOrFail($data['user_id']);

        try {
            return DB::transaction(function () use ($data, $tour, $customer, $request) {

                $booking = $this->bookingService->create(
                    $tour,
                    $customer,
                    $data,
                    bookedByAdmin: true
                );

                if (! empty($data['points'])) {
                    $this->pointsRedemptionOtpService->consume(
                        customer: $customer,
                        points: (int) $data['points'],
                        token: $data['points_otp_token'] ?? null,
                    );

                    $booking = $this->bookingService->applyPoints(
                        booking: $booking,
                        user: $customer,
                        points: (int) $data['points'],
                    );
                }

                if (! empty($data['payment_collected'])) {
                    $details = $this->offlinePaymentDetails(
                        $data,
                        $request->user()
                    );

                    $this->bookingService->confirmOfflinePayment(
                        booking: $booking,
                        paymentMethod: $data['payment_method'],
                        metadata: $details['metadata'],
                        providerPaymentId: $details['provider_payment_id'],
                    );

                    return redirect()
                        ->route('admin.bookings.show', $booking)
                        ->with(
                            'success',
                            'Booking created and payment confirmed via '
                                . Str::headline($data['payment_method'])
                                . '.'
                        );
                }

                return redirect()
                    ->route('admin.bookings.show', $booking)
                    ->with(
                        'success',
                        'Booking created. Payment is still pending.'
                    );
            });
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        }
    }

    /**
     * Email a one-time OTP to the customer, confirming they agree
     * to have the given number of points redeemed on their behalf.
     */
    public function sendPointsOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'points' => [
                'required',
                'integer',
                'min:1',
            ],
            'booking_id' => [
                'nullable',
                'integer',
                'exists:bookings,id',
            ],
            'departure_id' => [
                'nullable',
                'integer',
                'exists:tour_departures,id',
            ],
            'traveller_count' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        $customer = User::query()->findOrFail($data['user_id']);

        try {
            $otp = $this->pointsRedemptionOtpService->send(
                customer: $customer,
                points: (int) $data['points'],
                admin: $request->user(),
                bookingContext: $this->resolvePointsOtpBookingContext($data),
            );
        } catch (ValidationException $exception) {
            return response()->json([
                'ok' => false,
                'message' => collect($exception->errors())->flatten()->first()
                    ?? 'Could not send OTP.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'otp_id' => $otp->id,
            'expires_in_seconds' => (int) now()->diffInSeconds($otp->expires_at),
        ]);
    }

    /**
     * Verify a submitted OTP and, on success, hand back a
     * verification token the form can submit to redeem points.
     */
    public function verifyPointsOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'otp_id' => [
                'required',
                'integer',
            ],
            'otp' => [
                'required',
                'digits:6',
            ],
        ]);

        try {
            $record = $this->pointsRedemptionOtpService->verify(
                otpId: (int) $data['otp_id'],
                otp: $data['otp'],
                admin: $request->user(),
            );
        } catch (ValidationException $exception) {
            return response()->json([
                'ok' => false,
                'message' => collect($exception->errors())->flatten()->first()
                    ?? 'Incorrect OTP.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'verification_token' => $record->verification_token,
            'points' => $record->points,
        ]);
    }

    /**
     * Resolve the trip + amount details to show in the points OTP
     * email, from either an existing booking (checkout page) or a
     * selected departure + traveller count (create page, where no
     * booking exists yet). Pricing is recomputed here rather than
     * trusted from the client.
     */
    private function resolvePointsOtpBookingContext(array $data): ?array
    {
        if (! empty($data['booking_id'])) {
            $booking = Booking::query()
                ->with(['tourPackage', 'departure'])
                ->find($data['booking_id']);

            if (! $booking) {
                return null;
            }

            return [
                'tour_name' => $booking->tripName(),
                'departure_date' => $booking->tripDepartureDate(),
                'return_date' => $booking->tripReturnDate(),
                'traveller_count' => (int) $booking->traveller_count,
                'subtotal' => (float) $booking->subtotal,
                'tax' => (float) $booking->tax_amount,
                'total' => (float) $booking->total_amount,
                'currency' => $booking->currency,
            ];
        }

        if (! empty($data['departure_id'])) {
            $departure = TourDeparture::query()
                ->with('tourPackage')
                ->find($data['departure_id']);

            if (! $departure) {
                return null;
            }

            $travellerCount = max(1, (int) ($data['traveller_count'] ?? 1));
            $subtotal = round((float) $departure->effective_price * $travellerCount, 2);
            $taxPercent = (float) config('travels.booking.tax_percent', 0);
            $tax = round($subtotal * ($taxPercent / 100), 2);
            $total = round($subtotal + $tax, 2);

            return [
                'tour_name' => $departure->tourPackage?->name,
                'departure_date' => $departure->departure_date,
                'return_date' => $departure->return_date,
                'traveller_count' => $travellerCount,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'currency' => $departure->currency,
            ];
        }

        return null;
    }

    /**
     * Show the payment confirmation screen for an admin-created
     * booking that is still awaiting payment. Points redemption is
     * only available at booking-creation time (create.blade.php) —
     * this screen is payment-only.
     */
    public function checkout(Booking $booking): View|RedirectResponse
    {
        $booking = $this->bookingService->expireIfPastDue($booking);

        if (! $booking->isPayable()) {
            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with(
                    'error',
                    'This booking hold is no longer payable.'
                );
        }

        $booking->load([
            'user',
            'tourPackage',
            'departure',
            'travellers',
        ]);

        return view(
            'admin.bookings.checkout',
            compact('booking')
        );
    }

    /**
     * Apply the customer's points to the booking on their behalf.
     */
    public function applyPoints(
        Booking $booking,
        Request $request
    ): RedirectResponse {
        $data = $request->validate([
            'points' => [
                'required',
                'integer',
                'min:1',
            ],
            'points_otp_token' => [
                'nullable',
                'string',
            ],
        ]);

        if (! $booking->user) {
            return back()->with(
                'error',
                'This booking has no linked customer account.'
            );
        }

        try {
            $this->pointsRedemptionOtpService->consume(
                customer: $booking->user,
                points: (int) $data['points'],
                token: $data['points_otp_token'] ?? null,
            );

            $this->bookingService->applyPoints(
                booking: $booking,
                user: $booking->user,
                points: (int) $data['points'],
            );
        } catch (ValidationException $exception) {
            return back()
                ->withErrors($exception->errors())
                ->withInput();
        }

        return redirect()
            ->route('admin.bookings.checkout', $booking)
            ->with('success', 'Points applied successfully.');
    }

    /**
     * Remove previously applied points from the booking.
     */
    public function removePoints(Booking $booking): RedirectResponse
    {
        if ($booking->user) {
            $this->bookingService->removePoints(
                booking: $booking,
                user: $booking->user,
            );
        }

        return redirect()
            ->route('admin.bookings.checkout', $booking)
            ->with('success', 'Applied points have been removed.');
    }

    /**
     * Confirm the booking as paid, or explicitly leave it unpaid.
     *
     * The admin records exactly how the customer paid (cash, UPI,
     * bank transfer, or Razorpay collected outside the automated
     * checkout) so the payment record carries the real method and
     * reference, not a generic placeholder.
     *
     * If no payment method was submitted (the "payment collected"
     * checkbox was left unticked on the checkout screen), the
     * booking is simply saved as-is — still pending_payment/unpaid.
     * Nothing needs updating for that case; the booking already
     * defaults to that state.
     *
     * Confirming finalizes any applied points discount (debiting
     * the customer's wallet) and awards the booking's reward points.
     */
    public function confirm(
        Booking $booking,
        ConfirmBookingPaymentRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        if (empty($data['payment_method'])) {
            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with(
                    'success',
                    'Booking saved. Payment is still pending.'
                );
        }

        $details = $this->offlinePaymentDetails(
            $data,
            $request->user()
        );

        try {
            $this->bookingService->confirmOfflinePayment(
                booking: $booking,
                paymentMethod: $data['payment_method'],
                metadata: $details['metadata'],
                providerPaymentId: $details['provider_payment_id'],
            );
        } catch (ValidationException $exception) {
            return redirect()
                ->route('admin.bookings.checkout', $booking)
                ->with(
                    'error',
                    collect($exception->errors())->flatten()->first()
                        ?? 'This booking could not be confirmed.'
                );
        }

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with(
                'success',
                'Booking confirmed and marked as paid via '
                    . Str::headline($data['payment_method'])
                    . '.'
            );
    }

    /**
     * Build the Payment metadata + provider reference for an
     * offline (admin-recorded) payment, based on the selected
     * method.
     *
     * @return array{metadata: array<string, mixed>, provider_payment_id: ?string}
     */
    private function offlinePaymentDetails(
        array $data,
        User $admin
    ): array {
        $metadata = [
            'admin_user_id' => $admin->id,
            'admin_user_name' => $admin->name,
        ];

        $providerPaymentId = null;

        match ($data['payment_method']) {
            Payment::METHOD_UPI => $metadata['upi_id'] =
                $data['upi_id'],

            Payment::METHOD_BANK_TRANSFER => $metadata = $metadata + [
                'bank_name' => $data['bank_name'],
                'account_number' => $data['account_number'],
                'ifsc_code' => $data['ifsc_code'],
            ],

            Payment::METHOD_RAZORPAY => $providerPaymentId =
                $data['razorpay_payment_id'],

            default => null,
        };

        if (! empty($data['payment_note'])) {
            $metadata['note'] = $data['payment_note'];
        }

        return [
            'metadata' => $metadata,
            'provider_payment_id' => $providerPaymentId,
        ];
    }

    /**
     * Soft delete a booking.
     *
     * The underlying payment and traveller records are left intact;
     * only the booking is hidden from default queries and can be
     * restored later if needed.
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    /**
     * Manually correct a booking's payment status.
     *
     * This only updates the flag itself — it does not create a
     * Payment record, touch the booking's confirmation status, or
     * process points. Use the checkout/confirm flow for a proper
     * offline payment; use this for corrections (e.g. marking a
     * booking refunded or failed after the fact).
     */
    public function updatePaymentStatus(
        Booking $booking,
        Request $request
    ): RedirectResponse {
        $data = $request->validate([
            'payment_status' => [
                'required',
                Rule::in([
                    Booking::PAYMENT_UNPAID,
                    Booking::PAYMENT_PAID,
                    Booking::PAYMENT_FAILED,
                    Booking::PAYMENT_REFUNDED,
                ]),
            ],
        ]);

        $wasAlreadyRefunded = $booking->payment_status === Booking::PAYMENT_REFUNDED;

        DB::transaction(function () use ($booking, $data, $wasAlreadyRefunded) {
            $updates = [
                'payment_status' => $data['payment_status'],
            ];

            /*
            |--------------------------------------------------------------------------
            | Release The Seat
            |--------------------------------------------------------------------------
            |
            | Seat availability is computed from `status`, not
            | `payment_status` (see Booking::scopeReserving()). A
            | refunded booking left at status=confirmed would keep
            | occupying its seat forever — nobody else could ever
            | book it. Cancelling it here is what actually frees the
            | seat back up for new bookings.
            */

            if (
                $data['payment_status'] === Booking::PAYMENT_REFUNDED
                && $booking->status !== Booking::STATUS_CANCELLED
            ) {
                $updates['status'] = Booking::STATUS_CANCELLED;
            }

            $booking->update($updates);

            /*
            |--------------------------------------------------------------------------
            | Refund Redeemed Points
            |--------------------------------------------------------------------------
            |
            | We don't process the real-money refund here (that
            | happens outside this system) — but any points the
            | customer spent on this booking are credited back so
            | they aren't left permanently out of pocket on points.
            | Reward points already earned are left alone.
            */

            if (
                $data['payment_status'] === Booking::PAYMENT_REFUNDED
                && ! $wasAlreadyRefunded
            ) {
                $this->bookingService->refundBookingPoints($booking);
            }
        });

        return back()->with(
            'success',
            'Payment status updated to '
                . Str::headline($data['payment_status'])
                . '.'
        );
    }

    /**
     * Display all bookings.
     */
    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | Expire Stale Holds
        |--------------------------------------------------------------------------
        |
        | A booking's `status` only flips to expired lazily, whenever
        | its own checkout page is visited. Without this, the table
        | can show "Pending Payment" for a booking whose 15-minute
        | hold has already lapsed — the admin then hits "This booking
        | hold is no longer payable" when they try to mark it paid,
        | with nothing in the table warning them first.
        */

        Booking::query()
            ->where('status', Booking::STATUS_PENDING_PAYMENT)
            ->where('expires_at', '<', now())
            ->update(['status' => Booking::STATUS_EXPIRED]);

        $bookings = Booking::query()
            ->with([
                'user:id,username,email',
                'tourPackage:id,name,slug',
                'departure:id,tour_package_id,departure_date,return_date',
            ])

            ->when(
                $request->filled('search'),
                function ($query) use ($request) {

                    $search = trim(
                        (string) $request->input('search')
                    );

                    $query->where(function ($query) use ($search) {

                        $query
                            ->where(
                                'booking_number',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'contact_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'contact_email',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'contact_phone',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'trip_snapshot->tour_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhereHas(
                                'tourPackage',
                                fn ($query) => $query->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                            );

                    });
                }
            )

            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->input('status')
                )
            )

            ->when(
                $request->filled('payment_status'),
                fn ($query) => $query->where(
                    'payment_status',
                    $request->input('payment_status')
                )
            )

            ->latest('id')
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Booking Statistics
        |--------------------------------------------------------------------------
        */

        $stats = [
            'total' => Booking::query()->count(),

            'pending_payment' => Booking::query()
                ->where(
                    'status',
                    Booking::STATUS_PENDING_PAYMENT
                )
                ->count(),

            'confirmed' => Booking::query()
                ->where(
                    'status',
                    Booking::STATUS_CONFIRMED
                )
                ->count(),

            'cancelled' => Booking::query()
                ->where(
                    'status',
                    Booking::STATUS_CANCELLED
                )
                ->count(),

            'expired' => Booking::query()
                ->where(
                    'status',
                    Booking::STATUS_EXPIRED
                )
                ->count(),

            'paid' => Booking::query()
                ->where(
                    'payment_status',
                    Booking::PAYMENT_PAID
                )
                ->count(),

            'unpaid' => Booking::query()
                ->where(
                    'payment_status',
                    Booking::PAYMENT_UNPAID
                )
                ->count(),
        ];


        return view(
            'admin.bookings.index',
            compact(
                'bookings',
                'stats'
            )
        );
    }


    /**
     * Display a single booking.
     */
    public function show(
        Booking $booking
    ): View {

        $booking = $this->bookingService->expireIfPastDue($booking);

        $booking->load([
            'user',
            'tourPackage',
            'departure',

            'travellers',

            'payments' => fn ($query) =>
                $query->latest('id'),
        ]);


        /*
        |--------------------------------------------------------------------------
        | Booking Statistics
        |--------------------------------------------------------------------------
        */

        $reservedSeats = Booking::query()
            ->reserving()
            ->where(
                'tour_departure_id',
                $booking->tour_departure_id
            )
            ->sum('traveller_count');


        /*
        |--------------------------------------------------------------------------
        | Available Seats
        |--------------------------------------------------------------------------
        */

        $departureCapacity =
            (int) $booking->departure->capacity;

        $availableSeats = max(
            0,
            $departureCapacity - $reservedSeats
        );


        return view(
            'admin.bookings.show',
            compact(
                'booking',
                'reservedSeats',
                'availableSeats'
            )
        );
    }


    /**
     * Securely view an individual traveller's ID proof.
     *
     * ID proof files must never be exposed through
     * a public storage URL.
     */
    public function idProof(
        Booking $booking,
        BookingTraveller $traveller
    ): StreamedResponse {

        /*
        |--------------------------------------------------------------------------
        | Make sure traveller belongs to this booking
        |--------------------------------------------------------------------------
        */

        abort_unless(
            (int) $traveller->booking_id === (int) $booking->id,
            404
        );


        /*
        |--------------------------------------------------------------------------
        | Make sure document exists
        |--------------------------------------------------------------------------
        */

        abort_unless(
            filled($traveller->id_proof_document),
            404
        );


        /*
        |--------------------------------------------------------------------------
        | Make sure private file exists
        |--------------------------------------------------------------------------
        */

        $disk = Storage::disk('private');

        abort_unless(
            $disk->exists(
                $traveller->id_proof_document
            ),
            404
        );


        /*
        |--------------------------------------------------------------------------
        | Stream File
        |--------------------------------------------------------------------------
        */

        return $disk->response(
            $traveller->id_proof_document
        );
    }
}