{{--
    Shared payment method picker used by both the admin booking
    "create" (optional, payment already collected) and "checkout"
    (confirm & mark as paid) screens.

    Expects to be rendered inside an existing <form> that posts:
    payment_method, upi_id, bank_name, account_number, ifsc_code,
    razorpay_payment_id, payment_note.
--}}

<div class="payment-method-picker" data-payment-method-picker>

    <div class="payment-method-cards">

        @foreach([
            'cash' => [
                'label' => 'Cash',
                'description' => 'Pay in cash at the time of booking.',
                'icon' => 'cash',
            ],
            'upi' => [
                'label' => 'UPI',
                'description' => 'Pay using any UPI app.',
                'icon' => 'upi',
            ],
            'bank_transfer' => [
                'label' => 'Bank Transfer',
                'description' => 'Transfer via net banking.',
                'icon' => 'bank_transfer',
            ],
            'razorpay' => [
                'label' => 'Razorpay',
                'description' => 'Pay securely online.',
                'icon' => 'razorpay',
            ],
        ] as $method => $option)

            <label class="payment-method-card">

                <input
                    type="radio"
                    name="payment_method"
                    value="{{ $method }}"
                    class="payment-method-card__radio"
                    data-payment-method-radio
                    {{ old('payment_method', 'cash') === $method ? 'checked' : '' }}
                >

                <span class="payment-method-card__check">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </span>

                <span class="payment-method-card__icon payment-method-card__icon--{{ $option['icon'] }}">
                    @switch($option['icon'])
                        @case('cash')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M6 9v.01M18 15v.01"/></svg>
                            @break

                        @case('upi')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h11M18 7l-3-3M18 7l-3 3"/><path d="M17 17H6M6 17l3 3M6 17l3-3"/></svg>
                            @break

                        @case('bank_transfer')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10h18M4 10v9M8 10v9M12 10v9M16 10v9M20 10v9M2 21h20M12 3 3 8h18Z"/></svg>
                            @break

                        @case('razorpay')
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13 2 3 14h7l-1 8 11-14h-8l1-6Z"/></svg>
                            @break
                    @endswitch
                </span>

                <span class="payment-method-card__body">
                    <strong>{{ $option['label'] }}</strong>
                    <small>{{ $option['description'] }}</small>
                </span>

            </label>

        @endforeach

    </div>


    <div class="payment-method-fields">

        <div class="payment-method-fields__inputs">

            <div class="payment-field" data-payment-fields="upi" hidden>
                <label for="upi_id">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h11M18 7l-3-3M18 7l-3 3"/><path d="M17 17H6M6 17l3 3M6 17l3-3"/></svg>
                    UPI ID
                </label>
                <input
                    id="upi_id"
                    type="text"
                    name="upi_id"
                    maxlength="100"
                    value="{{ old('upi_id') }}"
                    placeholder="customer@upi"
                >
                @error('upi_id')
                    <small class="admin-form-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="payment-field" data-payment-fields="bank_transfer" hidden>
                <label for="bank_name">Bank name</label>
                <input
                    id="bank_name"
                    type="text"
                    name="bank_name"
                    maxlength="150"
                    value="{{ old('bank_name') }}"
                >
                @error('bank_name')
                    <small class="admin-form-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="payment-field" data-payment-fields="bank_transfer" hidden>
                <label for="account_number">Account number</label>
                <input
                    id="account_number"
                    type="text"
                    name="account_number"
                    maxlength="34"
                    value="{{ old('account_number') }}"
                >
                @error('account_number')
                    <small class="admin-form-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="payment-field" data-payment-fields="bank_transfer" hidden>
                <label for="ifsc_code">IFSC code</label>
                <input
                    id="ifsc_code"
                    type="text"
                    name="ifsc_code"
                    maxlength="11"
                    style="text-transform:uppercase;"
                    value="{{ old('ifsc_code') }}"
                >
                @error('ifsc_code')
                    <small class="admin-form-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="payment-field" data-payment-fields="razorpay" hidden>
                <label for="razorpay_payment_id">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    Razorpay Payment ID
                </label>
                <input
                    id="razorpay_payment_id"
                    type="text"
                    name="razorpay_payment_id"
                    maxlength="100"
                    value="{{ old('razorpay_payment_id') }}"
                    placeholder="Enter Razorpay payment ID"
                >
                @error('razorpay_payment_id')
                    <small class="admin-form-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="payment-field">
                <label for="payment_note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/></svg>
                    Note (optional)
                </label>
                <textarea
                    id="payment_note"
                    name="payment_note"
                    maxlength="255"
                    rows="3"
                    placeholder="Any reference note for this payment"
                >{{ old('payment_note') }}</textarea>
                @error('payment_note')
                    <small class="admin-form-error">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <div class="payment-method-secure">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/></svg>
            <div>
                <strong>Secure Payment</strong>
                <small>Your payment information is encrypted and secure.</small>
            </div>
        </div>

    </div>

    @error('payment_method')
        <small class="admin-form-error">{{ $message }}</small>
    @enderror

</div>


<style>
    .payment-method-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    @media (max-width: 900px) {
        .payment-method-cards {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .payment-method-card {
        position: relative;

        display: flex;
        align-items: flex-start;
        gap: 10px;

        padding: 14px;

        border: 1.5px solid var(--admin-border);
        border-radius: var(--admin-radius-md);

        cursor: pointer;

        transition:
            border-color var(--admin-transition),
            background var(--admin-transition);
    }

    .payment-method-card:hover {
        border-color: var(--admin-green);
    }

    .payment-method-card__radio {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .payment-method-card__check {
        position: absolute;
        top: 8px;
        right: 8px;

        display: none;
        align-items: center;
        justify-content: center;

        width: 18px;
        height: 18px;

        border-radius: 50%;

        background: var(--admin-green);
        color: #fff;
    }

    .payment-method-card__check svg {
        width: 11px;
        height: 11px;
    }

    .payment-method-card:has(.payment-method-card__radio:checked) {
        border-color: var(--admin-green);
        background: var(--admin-green-soft);
    }

    .payment-method-card:has(.payment-method-card__radio:checked) .payment-method-card__check {
        display: flex;
    }

    .payment-method-card__icon {
        flex-shrink: 0;

        display: flex;
        align-items: center;
        justify-content: center;

        width: 34px;
        height: 34px;

        border-radius: var(--admin-radius-sm);
    }

    .payment-method-card__icon svg {
        width: 18px;
        height: 18px;
    }

    .payment-method-card__icon--cash {
        background: var(--admin-green-soft);
        color: var(--admin-green);
    }

    .payment-method-card__icon--upi {
        background: #fff4ec;
        color: #ea580c;
    }

    .payment-method-card__icon--bank_transfer {
        background: #eef2ff;
        color: #4f46e5;
    }

    .payment-method-card__icon--razorpay {
        background: #eaf2ff;
        color: #2563eb;
    }

    .payment-method-card__body {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .payment-method-card__body strong {
        font-size: 13.5px;
        color: var(--admin-text);
    }

    .payment-method-card__body small {
        font-size: 11.5px;
        color: var(--admin-text-muted);
        line-height: 1.35;
    }

    .payment-method-fields {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 16px;

        margin-top: 18px;
    }

    @media (max-width: 780px) {
        .payment-method-fields {
            grid-template-columns: 1fr;
        }
    }

    .payment-method-fields__inputs {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .payment-field label {
        display: flex;
        align-items: center;
        gap: 6px;

        margin-bottom: 6px;

        font-size: 12.5px;
        font-weight: 700;
        color: var(--admin-text);
    }

    .payment-field label svg {
        width: 14px;
        height: 14px;
        color: var(--admin-text-muted);
    }

    .payment-field input,
    .payment-field textarea {
        width: 100%;

        padding: 10px 12px;

        border: 1px solid var(--admin-border);
        border-radius: var(--admin-radius-sm);

        font-size: 13px;

        font-family: inherit;

        resize: vertical;
    }

    .payment-field input:focus,
    .payment-field textarea:focus {
        outline: none;
        border-color: var(--admin-green);
    }

    .payment-method-secure {
        display: flex;
        align-items: flex-start;
        gap: 10px;

        align-self: flex-start;

        padding: 14px;

        border-radius: var(--admin-radius-md);

        background: var(--admin-background);
    }

    .payment-method-secure svg {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        margin-top: 2px;
        color: var(--admin-green);
    }

    .payment-method-secure strong {
        display: block;
        margin-bottom: 2px;
        font-size: 12.5px;
        color: var(--admin-text);
    }

    .payment-method-secure small {
        font-size: 11.5px;
        color: var(--admin-text-muted);
        line-height: 1.4;
    }
</style>

<script>
    document.querySelectorAll('[data-payment-method-picker]').forEach(function (picker) {
        const radios = picker.querySelectorAll('[data-payment-method-radio]');
        const fieldGroups = picker.querySelectorAll('[data-payment-fields]');

        function updateVisibility() {
            const checked = picker.querySelector('[data-payment-method-radio]:checked');
            const method = checked ? checked.value : null;

            fieldGroups.forEach(function (group) {
                const show = group.getAttribute('data-payment-fields') === method;
                group.hidden = !show;

                group.querySelectorAll('input, textarea').forEach(function (input) {
                    input.disabled = !show;
                });
            });
        }

        radios.forEach(function (radio) {
            radio.addEventListener('change', updateVisibility);
        });

        updateVisibility();
    });
</script>
