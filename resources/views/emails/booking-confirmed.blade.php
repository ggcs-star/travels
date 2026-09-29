@php
    $subtotal = (float) $booking->subtotal;
    $tax = (float) $booking->tax_amount;
    $total = (float) $booking->total_amount;
    $pointsRedeemed = (int) ($booking->points_redeemed ?? 0);
    $pointsDiscount = (float) ($booking->points_discount ?? 0);
    $payableAmount = (float) $booking->payableAmount();
    $customerName = $booking->user->name ?? $booking->contact_name;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Confirmed — {{ $booking->booking_number }}</title>
</head>
<body style="margin:0; padding:0; background:#f3f5f8; font-family: Arial, Helvetica, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f5f8; padding:32px 16px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 18px rgba(20,30,50,0.08);">

                    <tr>
                        <td style="background:#0f3d2e; padding:24px 32px;">
                            <span style="color:#ffffff; font-size:18px; font-weight:700; letter-spacing:.02em;">
                                {{ config('travels.brand.name', 'Travels') }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 32px 8px;">

                            <p style="margin:0 0 4px; font-size:12px; font-weight:700; letter-spacing:.08em; color:#15803d; text-transform:uppercase;">
                                ✓ Booking Confirmed
                            </p>

                            <p style="margin:0 0 16px; font-size:15px; color:#202b3e;">
                                Hi {{ $customerName ?: 'there' }},
                            </p>

                            <p style="margin:0 0 8px; font-size:14.5px; line-height:1.6; color:#4b5666;">
                                Your booking for <strong>{{ $booking->tripName() }}</strong> is confirmed! Here are
                                your booking details.
                            </p>

                            <p style="margin:0 0 20px; font-size:13px; color:#9aa2ae;">
                                Booking reference: <strong style="color:#202b3e;">{{ $booking->booking_number }}</strong>
                            </p>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px;">
                            <p style="margin:0 0 8px; font-size:11px; font-weight:700; letter-spacing:.08em; color:#d97706; text-transform:uppercase;">
                                Trip
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px; font-size:14px; color:#202b3e;">
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Tour</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $booking->tripName() }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Departure date</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $booking->tripDepartureDate()?->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Return date</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $booking->tripReturnDate()?->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Travellers</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">
                                        {{ $booking->traveller_count }}
                                        ({{ $booking->travellers->pluck('full_name')->join(', ') }})
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px;">
                            <p style="margin:0 0 8px; font-size:11px; font-weight:700; letter-spacing:.08em; color:#d97706; text-transform:uppercase;">
                                Amount summary
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px; font-size:14px; color:#202b3e; border-top:1px solid #eef1f5;">
                                <tr>
                                    <td style="padding:8px 0 4px; color:#77817f;">Subtotal</td>
                                    <td style="padding:8px 0 4px; text-align:right; font-weight:700;">{{ $booking->currency }} {{ number_format($subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Taxes</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $booking->currency }} {{ number_format($tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Booking total</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $booking->currency }} {{ number_format($total, 2) }}</td>
                                </tr>
                                @if($pointsRedeemed > 0)
                                    <tr>
                                        <td style="padding:4px 0; color:#77817f;">Points discount ({{ number_format($pointsRedeemed) }} pts)</td>
                                        <td style="padding:4px 0; text-align:right; font-weight:700; color:#b42318;">&minus;{{ $booking->currency }} {{ number_format($pointsDiscount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:10px 0 0; border-top:1px solid #eef1f5; font-weight:700;">Amount paid</td>
                                    <td style="padding:10px 0 0; border-top:1px solid #eef1f5; text-align:right; font-weight:700;">{{ $booking->currency }} {{ number_format($payableAmount, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px;">
                            <p style="margin:0 0 8px; font-size:11px; font-weight:700; letter-spacing:.08em; color:#d97706; text-transform:uppercase;">
                                Payment details
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px; font-size:14px; color:#202b3e;">
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Paid via</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">{{ Str::headline($payment->provider) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px 0; color:#77817f;">Paid on</td>
                                    <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $payment->paid_at?->format('d M Y, h:i A') }}</td>
                                </tr>
                                @if($payment->provider_payment_id)
                                    <tr>
                                        <td style="padding:4px 0; color:#77817f;">Reference ID</td>
                                        <td style="padding:4px 0; text-align:right; font-weight:700;">{{ $payment->provider_payment_id }}</td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:22px 32px; background:#fafbfc; border-top:1px solid #eef1f5;">
                            <p style="margin:0 0 10px; font-size:13px; line-height:1.6; color:#4b5666;">
                                Thank you for choosing {{ config('travels.brand.name', 'Travels') }}.
                                We look forward to helping you plan your journey.
                            </p>

                            <p style="margin:0 0 10px; font-size:13px; color:#202b3e;">
                                Warm regards,<br>
                                {{ config('travels.brand.name', 'Travels') }} Support Team
                            </p>

                            <p style="margin:0 0 10px; font-size:12px; color:#9aa2ae;">
                                📧 {{ config('travels.contact.email', 'support@travels.com') }}
                                &nbsp;·&nbsp;
                                📞 {{ config('travels.contact.phone_primary', '') }}
                            </p>

                            <p style="margin:0; font-size:12px;">
                                <a href="{{ url('/') }}" style="color:#0f766e; font-weight:700; text-decoration:none;">
                                    Visit {{ config('travels.brand.name', 'Travels') }} →
                                </a>
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
