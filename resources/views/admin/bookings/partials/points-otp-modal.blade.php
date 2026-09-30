{{--
    Shared "verify points redemption via OTP" popup, used by both the
    admin booking create page and the checkout page.

    The including page is responsible for rendering, next to its own
    points input:
      - a "Redeem points" button with id="redeem-points-btn"
      - a hidden input id="points_otp_token" name="points_otp_token"
      - a status line: <small data-points-otp-status hidden></small>

    This partial reads the customer id from [data-customer-select] if
    present on the page (create.blade.php), otherwise falls back to
    the data-customer-id attribute below (checkout.blade.php, where
    the customer is fixed to the booking's own user).

    Pass $otpBookingId on checkout.blade.php (an existing booking) so
    the OTP email can include a real trip + amount summary; on
    create.blade.php it's derived instead from the selected departure
    and traveller count (see getBookingContextPayload() below).
--}}

<div
    id="points-otp-config"
    data-customer-id="{{ $otpCustomerId ?? '' }}"
    data-booking-id="{{ $otpBookingId ?? '' }}"
    hidden
></div>

<div
    class="points-otp-modal"
    id="pointsOtpModal"
    aria-hidden="true"
    style="display:none;"
>
    <div class="points-otp-modal__overlay" data-points-otp-close></div>

    <div
        class="points-otp-modal__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="pointsOtpModalTitle"
    >
        <div class="points-otp-modal__header">
            <div>
                <span class="points-otp-modal__eyebrow">VERIFY WITH CUSTOMER</span>
                <h2 id="pointsOtpModalTitle">Enter the OTP</h2>
                <p>We emailed a 6-digit code to the customer's registered email. Ask them for it before redeeming their points.</p>
            </div>

            <button
                type="button"
                class="points-otp-modal__close"
                data-points-otp-close
                aria-label="Close"
            >
                &times;
            </button>
        </div>

        <div class="points-otp-modal__body">

            <div class="points-otp-modal__error" data-points-otp-error hidden></div>

            <label for="points_otp_input">6-digit code</label>
            <input
                type="text"
                id="points_otp_input"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                minlength="6"
                autocomplete="one-time-code"
                placeholder="000000"
            >

            <button
                type="button"
                class="points-otp-modal__resend"
                data-points-otp-resend
            >
                Resend OTP
            </button>

        </div>

        <div class="points-otp-modal__footer">
            <button type="button" class="admin-button" data-points-otp-close>
                Cancel
            </button>

            <button
                type="button"
                class="admin-button admin-button--primary"
                data-points-otp-verify
            >
                <span class="points-otp-submit-text">Verify</span>
                <span class="points-otp-submit-loading" hidden>Verifying…</span>
            </button>
        </div>

    </div>
</div>


<style>

.points-otp-modal {
    position: fixed !important;
    top: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    margin: 0 !important;
    padding: 20px !important;
    box-sizing: border-box !important;
    z-index: 2147483000 !important;
    display: none !important;
    align-items: center !important;
    justify-content: center !important;
}

.points-otp-modal.is-open {
    display: flex !important;
}

.points-otp-modal__overlay {
    position: absolute !important;
    inset: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background: rgba(7, 18, 20, .62) !important;
    backdrop-filter: blur(4px);
}

.points-otp-modal__dialog {
    position: relative !important;
    z-index: 2 !important;
    width: min(420px, 100%) !important;
    max-height: calc(100vh - 40px) !important;
    margin: 0 !important;
    overflow: auto !important;
    box-sizing: border-box !important;
    background: #fff !important;
    border: 1px solid rgba(15, 23, 42, .08) !important;
    border-radius: 18px !important;
    box-shadow: 0 30px 90px rgba(0, 0, 0, .28) !important;
}

.points-otp-modal__header {
    display: flex !important;
    align-items: flex-start !important;
    justify-content: space-between !important;
    gap: 18px !important;
    padding: 22px 24px 18px !important;
    border-bottom: 1px solid #edf0f2 !important;
}

.points-otp-modal__eyebrow {
    display: block !important;
    margin-bottom: 5px !important;
    color: #d97706 !important;
    font-size: 9px !important;
    font-weight: 800 !important;
    letter-spacing: .14em !important;
}

.points-otp-modal__header h2 {
    margin: 0 !important;
    color: #142c2a !important;
    font-size: 19px !important;
    line-height: 1.25 !important;
    font-weight: 800 !important;
}

.points-otp-modal__header p {
    margin: 6px 0 0 !important;
    color: #77817f !important;
    font-size: 11.5px !important;
    line-height: 1.5 !important;
}

.points-otp-modal__close {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 0 0 34px !important;
    width: 34px !important;
    height: 34px !important;
    padding: 0 !important;
    border: 1px solid #e4e9e8 !important;
    border-radius: 9px !important;
    background: #fff !important;
    color: #60706d !important;
    font-size: 20px !important;
    line-height: 1 !important;
    cursor: pointer !important;
}

.points-otp-modal__close:hover {
    background: #f5f8f7 !important;
    color: #17302d !important;
}

.points-otp-modal__body {
    padding: 22px 24px !important;
}

.points-otp-modal__body label {
    display: block !important;
    margin: 0 0 8px !important;
    color: #34423f !important;
    font-size: 11px !important;
    font-weight: 750 !important;
}

.points-otp-modal__body input {
    display: block !important;
    width: 100% !important;
    box-sizing: border-box !important;
    padding: 14px !important;
    border: 1px solid #dfe6e4 !important;
    border-radius: 10px !important;
    outline: none !important;
    background: #fff !important;
    color: #142c2a !important;
    font-family: inherit !important;
    font-size: 24px !important;
    font-weight: 700 !important;
    letter-spacing: .4em !important;
    text-align: center !important;
}

.points-otp-modal__body input:focus {
    border-color: #d97706 !important;
    box-shadow: 0 0 0 3px rgba(217, 119, 6, .12) !important;
}

.points-otp-modal__resend {
    display: block !important;
    margin: 12px auto 0 !important;
    padding: 0 !important;
    border: 0 !important;
    background: none !important;
    color: #0f766e !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
}

.points-otp-modal__resend:disabled {
    color: #9aa2ae !important;
    cursor: default !important;
}

.points-otp-modal__error {
    margin: 0 0 15px !important;
    padding: 9px 11px !important;
    border: 1px solid #f2c7c3 !important;
    border-radius: 8px !important;
    background: #fff5f4 !important;
    color: #a63d35 !important;
    font-size: 11.5px !important;
    line-height: 1.45 !important;
}

.points-otp-modal__footer {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 8px !important;
    padding: 14px 24px 20px !important;
    border-top: 1px solid #edf0f2 !important;
}

body.points-otp-modal-open {
    overflow: hidden !important;
}

.points-otp-status {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    padding: 8px 12px;
    border-radius: 8px;
    background: var(--admin-green-soft, #ecfdf3);
    color: var(--admin-green, #15803d);
    font-size: 12px;
    font-weight: 650;
}

.points-input--locked {
    background: #f3f5f8 !important;
    color: #586273 !important;
    cursor: not-allowed !important;
}

.points-otp-status a {
    color: inherit;
    text-decoration: underline;
    font-weight: 700;
}

</style>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('pointsOtpModal');

    if (!modal) {
        return;
    }

    const config = document.getElementById('points-otp-config');
    const otpInput = document.getElementById('points_otp_input');
    const errorBox = document.querySelector('[data-points-otp-error]');
    const verifyButton = document.querySelector('[data-points-otp-verify]');
    const verifySubmitText = verifyButton?.querySelector('.points-otp-submit-text');
    const verifySubmitLoading = verifyButton?.querySelector('.points-otp-submit-loading');
    const resendButton = document.querySelector('[data-points-otp-resend]');
    const closeButtons = document.querySelectorAll('[data-points-otp-close]');

    const redeemButton = document.getElementById('redeem-points-btn');
    const pointsInput = document.getElementById('points');
    const tokenInput = document.getElementById('points_otp_token');
    const statusBox = document.querySelector('[data-points-otp-status]');

    if (!redeemButton || !pointsInput || !tokenInput) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    let currentOtpId = null;
    let currentPoints = null;
    let resendTimer = null;

    function getCustomerId() {
        const select = document.querySelector('[data-customer-select]');
        if (select) {
            return select.value || null;
        }
        return config?.dataset.customerId || null;
    }

    // Lets the email include a trip + amount summary. On the checkout
    // page a booking already exists (booking_id); on the create page
    // nothing is persisted yet, so we send the currently selected
    // departure + traveller count instead and the server prices it.
    function getBookingContextPayload() {
        const bookingId = config?.dataset.bookingId;
        if (bookingId) {
            return { booking_id: bookingId };
        }

        const departureSelect = document.querySelector('[data-departure-select]');
        const travellerCards = document.querySelectorAll('[data-traveller-card]');

        if (departureSelect && departureSelect.value) {
            return {
                departure_id: departureSelect.value,
                traveller_count: Math.max(1, travellerCards.length),
            };
        }

        return {};
    }

    function showError(message) {
        if (errorBox) {
            errorBox.textContent = message;
            errorBox.hidden = false;
        }
    }

    function clearError() {
        if (errorBox) {
            errorBox.hidden = true;
            errorBox.textContent = '';
        }
    }

    function setVerifyBusy(busy) {
        if (!verifyButton) return;
        verifyButton.disabled = busy;
        if (verifySubmitText) verifySubmitText.hidden = busy;
        if (verifySubmitLoading) verifySubmitLoading.hidden = !busy;
    }

    function openModal() {
        modal.setAttribute('aria-hidden', 'false');
        modal.classList.add('is-open');
        modal.style.display = 'flex';
        document.body.classList.add('points-otp-modal-open');
        clearError();
        if (otpInput) otpInput.value = '';
        setTimeout(() => otpInput?.focus(), 80);
    }

    function closeModal() {
        modal.setAttribute('aria-hidden', 'true');
        modal.classList.remove('is-open');
        modal.style.display = 'none';
        document.body.classList.remove('points-otp-modal-open');
        if (resendTimer) {
            clearInterval(resendTimer);
            resendTimer = null;
        }
    }

    function startResendCooldown(seconds) {
        if (!resendButton) return;
        let remaining = seconds;
        resendButton.disabled = true;
        resendButton.textContent = `Resend OTP (${remaining}s)`;

        if (resendTimer) clearInterval(resendTimer);

        resendTimer = setInterval(() => {
            remaining -= 1;
            if (remaining <= 0) {
                clearInterval(resendTimer);
                resendTimer = null;
                resendButton.disabled = false;
                resendButton.textContent = 'Resend OTP';
            } else {
                resendButton.textContent = `Resend OTP (${remaining}s)`;
            }
        }, 1000);
    }

    function lockPointsField() {
        // readOnly (not disabled) — a disabled field is excluded from
        // form submission entirely, which would silently drop `points`.
        pointsInput.readOnly = true;
        pointsInput.classList.add('points-input--locked');
        pointsInput.dataset.otpLocked = '1';
    }

    function unlockPointsField() {
        pointsInput.readOnly = false;
        pointsInput.classList.remove('points-input--locked');
        delete pointsInput.dataset.otpLocked;
    }

    function setVerifiedStatus(points) {
        if (statusBox) {
            statusBox.innerHTML =
                `✓ Verified — ${Number(points).toLocaleString('en-IN')} point(s) will be redeemed. `
                + `<a href="#" data-points-otp-change>Change</a>`;
            statusBox.hidden = false;

            statusBox.querySelector('[data-points-otp-change]')?.addEventListener('click', function (event) {
                event.preventDefault();
                invalidate();
            });
        }
        redeemButton.hidden = true;
    }

    function invalidate() {
        tokenInput.value = '';
        currentOtpId = null;
        currentPoints = null;
        unlockPointsField();
        redeemButton.hidden = false;
        if (statusBox) {
            statusBox.hidden = true;
            statusBox.innerHTML = '';
        }
    }

    // Exposed so the page's own points-recalculation script can
    // invalidate a verified/pending OTP when the customer changes.
    window.pointsOtpModal = { invalidate };

    function sendOtp() {
        const customerId = getCustomerId();
        const points = Number(pointsInput.value || 0);

        if (!customerId) {
            window.alert('Please select a customer first.');
            return;
        }

        if (!points || points < 1) {
            window.alert('Please enter how many points to redeem first.');
            return;
        }

        redeemButton.disabled = true;
        redeemButton.textContent = 'Sending OTP…';
        lockPointsField();

        fetch('{{ route('admin.bookings.points-otp.send') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: JSON.stringify(Object.assign({ user_id: customerId, points }, getBookingContextPayload())),
        })
            .then((response) => response.json().then((data) => ({ status: response.status, data })))
            .then(({ status, data }) => {
                if (status !== 200 || !data.ok) {
                    throw new Error(data.message || 'Could not send OTP.');
                }

                currentOtpId = data.otp_id;
                currentPoints = points;

                redeemButton.disabled = false;
                redeemButton.textContent = 'Redeem points';

                openModal();
                startResendCooldown(10);
            })
            .catch((error) => {
                unlockPointsField();
                redeemButton.disabled = false;
                redeemButton.textContent = 'Redeem points';
                window.alert(error.message || 'Could not send OTP.');
            });
    }

    function verifyOtp() {
        const otp = (otpInput?.value || '').trim();

        if (!/^\d{6}$/.test(otp)) {
            showError('Please enter the 6-digit code.');
            return;
        }

        clearError();
        setVerifyBusy(true);

        fetch('{{ route('admin.bookings.points-otp.verify') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: JSON.stringify({ otp_id: currentOtpId, otp }),
        })
            .then((response) => response.json().then((data) => ({ status: response.status, data })))
            .then(({ status, data }) => {
                setVerifyBusy(false);

                if (status !== 200 || !data.ok) {
                    showError(data.message || 'Incorrect OTP.');
                    return;
                }

                tokenInput.value = data.verification_token;
                closeModal();
                setVerifiedStatus(data.points);
            })
            .catch(() => {
                setVerifyBusy(false);
                showError('Something went wrong. Please try again.');
            });
    }

    redeemButton.addEventListener('click', sendOtp);
    verifyButton?.addEventListener('click', verifyOtp);
    resendButton?.addEventListener('click', sendOtp);

    otpInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            verifyOtp();
        }
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            closeModal();
            unlockPointsField();
        });
    });

    modal.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeModal();
        unlockPointsField();
    });

});
</script>
