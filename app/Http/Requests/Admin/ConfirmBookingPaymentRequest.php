<?php

namespace App\Http\Requests\Admin;

use App\Models\Payment;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ConfirmBookingPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && (
                $user->isAdmin()
                || $user->isSuperAdmin()
            );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | Payment Method (optional)
            |--------------------------------------------------------------------------
            |
            | Left empty, the admin is explicitly leaving this booking
            | unpaid for now (e.g. from the checkout screen, with the
            | "payment collected" checkbox unticked). Provided, the
            | booking is confirmed as paid via that method.
            */

            'payment_method' => [
                'nullable',
                Rule::in(Payment::OFFLINE_METHODS),
            ],

            'upi_id' => [
                'nullable',
                'required_if:payment_method,upi',
                'string',
                'max:100',
            ],

            'bank_name' => [
                'nullable',
                'required_if:payment_method,bank_transfer',
                'string',
                'max:150',
            ],

            'account_number' => [
                'nullable',
                'required_if:payment_method,bank_transfer',
                'string',
                'max:34',
            ],

            'ifsc_code' => [
                'nullable',
                'required_if:payment_method,bank_transfer',
                'string',
                'max:11',
            ],

            'razorpay_payment_id' => [
                'nullable',
                'required_if:payment_method,razorpay',
                'string',
                'max:100',
            ],

            'payment_note' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'upi_id.required_if' =>
                'Please enter the UPI ID used for payment.',

            'bank_name.required_if' =>
                'Please enter the bank name.',

            'account_number.required_if' =>
                'Please enter the account number.',

            'ifsc_code.required_if' =>
                'Please enter the IFSC code.',

            'razorpay_payment_id.required_if' =>
                'Please enter the Razorpay payment ID.',
        ];
    }

    /**
     * Where validation failures should redirect to.
     *
     * This request is submitted from two places: the full checkout
     * screen, and the "mark as paid" popup on the bookings list. The
     * popup is a closed <dialog> by default, so redirecting back to
     * "previous URL" (the bookings list) would leave the errors
     * invisible inside it. Always send failures to the full checkout
     * screen instead, where they're guaranteed to be visible.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo(
                route('admin.bookings.checkout', $this->route('booking'))
            );
    }
}
