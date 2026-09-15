<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminPointAdjustmentRequest;
use App\Models\User;
use App\Services\Points\AdminPointWalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AdminPointWalletController extends Controller
{
    public function __construct(
        private readonly AdminPointWalletService $walletService
    ) {}

    /**
     * Display all user point wallets.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $users = $this->walletService->users(
            search: $search !== '' ? $search : null,
            perPage: 20
        );

        return view(
            'admin.point-wallets.index',
            compact('users', 'search')
        );
    }

    /**
     * Display a user's wallet and transaction history.
     */
 public function show(User $user): View
{
    $data = $this->walletService->walletDetails(
        user: $user,
        perPage: 20
    );

    return view('admin.point-wallets.show', $data);
}
    /**
     * Add or deduct points from a user's wallet.
     */
    public function adjust(
        AdminPointAdjustmentRequest $request,
        User $user
    ): RedirectResponse {
        $data = $request->validated();

        try {
            if ($data['action'] === 'add') {
                $this->walletService->addPoints(
                    user: $user,
                    points: (int) $data['points'],
                    reason: $data['reason'],
                    metadata: [
                        'admin_user_id' => $request->user()->id,
                        'admin_user_name' => $request->user()->name,
                        'action' => 'add',
                    ]
                );

                return back()->with(
                    'success',
                    "{$data['points']} points added successfully."
                );
            }

            $this->walletService->deductPoints(
                user: $user,
                points: (int) $data['points'],
                reason: $data['reason'],
                metadata: [
                    'admin_user_id' => $request->user()->id,
                    'admin_user_name' => $request->user()->name,
                    'action' => 'deduct',
                ]
            );

            return back()->with(
                'success',
                "{$data['points']} points deducted successfully."
            );
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'points' => $exception->getMessage(),
                ]);
        }
    }
}