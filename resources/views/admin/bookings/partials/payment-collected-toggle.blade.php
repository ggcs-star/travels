{{--
    Properly laid-out checkbox + description row used to gate the
    payment method picker behind an explicit "payment collected?"
    decision. Expects:

    $toggleId       (string)  id for the checkbox input, unique per page
    $toggleTitle    (string)  bold title text
    $toggleHint     (string)  small description text below the title
--}}

<label for="{{ $toggleId }}" class="payment-collected-toggle">

    <input
        type="checkbox"
        name="payment_collected"
        value="1"
        id="{{ $toggleId }}"
        data-payment-collected-checkbox
        {{ old('payment_collected') ? 'checked' : '' }}
    >

    <span class="payment-collected-toggle__text">
        <strong>{{ $toggleTitle }}</strong>
        <small>{{ $toggleHint }}</small>
    </span>

</label>

<style>
    .payment-collected-toggle {
        display: flex;
        align-items: flex-start;
        gap: 12px;

        padding: 14px 16px;

        border: 1.5px solid var(--admin-border);
        border-radius: var(--admin-radius-md);

        cursor: pointer;

        transition:
            border-color var(--admin-transition),
            background var(--admin-transition);
    }

    .payment-collected-toggle:has(input:checked) {
        border-color: var(--admin-green);
        background: var(--admin-green-soft);
    }

    .payment-collected-toggle input[type="checkbox"] {
        flex-shrink: 0;

        width: 19px;
        height: 19px;
        margin-top: 1px;

        accent-color: var(--admin-green);

        cursor: pointer;
    }

    .payment-collected-toggle__text {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .payment-collected-toggle__text strong {
        font-size: 13.5px;
        color: var(--admin-text);
    }

    .payment-collected-toggle__text small {
        font-size: 11.5px;
        line-height: 1.4;
        color: var(--admin-text-muted);
    }
</style>
