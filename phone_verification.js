(function () {
    let statusKnown = false;
    let phoneVerified = false;
    let pendingResolver = null;
    let phoneInput = null;

    const byId = (id) => document.getElementById(id);

    function setError(message, success) {
        const output = byId('phoneVerificationMessage');
        if (!output) return;
        output.textContent = message || '';
        output.style.color = success ? '#46e6a4' : '#ff8d9a';
    }

    function setStep(step) {
        byId('phoneVerificationPhoneStep').hidden = step !== 'phone';
        byId('phoneVerificationCodeStep').hidden = step !== 'code';
        setError('');
    }

    function openModal() {
        const modal = byId('phoneVerificationModal');
        if (!modal) return Promise.resolve(false);
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        setStep('phone');
        if (!phoneInput && window.intlTelInput) {
            phoneInput = window.intlTelInput(byId('phoneVerificationPhone'), {
                initialCountry: 'lb',
                separateDialCode: true,
                preferredCountries: ['lb', 'ae', 'sa', 'qa', 'kw', 'gb', 'us'],
            });
        }
        return new Promise((resolve) => { pendingResolver = resolve; });
    }

    function closeModal(result) {
        const modal = byId('phoneVerificationModal');
        if (modal) modal.hidden = true;
        document.body.style.overflow = '';
        if (pendingResolver) pendingResolver(!!result);
        pendingResolver = null;
    }

    function internationalPhone() {
        const input = byId('phoneVerificationPhone');
        const localDigits = (input?.value || '').replace(/\D/g, '').replace(/^0+/, '');
        const country = phoneInput?.getSelectedCountry ? phoneInput.getSelectedCountry() : null;
        return country?.dialCode ? `+${country.dialCode}${localDigits}` : `+${localDigits}`;
    }

    async function checkStatus() {
        const response = await fetch('phone_verification_status.php', {cache: 'no-store'});
        if (response.status === 401) return {login_required: true};
        const data = await response.json();
        if (!response.ok || !data.success) throw new Error(data.message || 'Unable to check phone number');
        statusKnown = true;
        phoneVerified = !data.verification_required;
        return data;
    }

    async function ensureVerifiedPhone() {
        try {
            if (!statusKnown) await checkStatus();
            if (phoneVerified) return true;
            return await openModal();
        } catch (error) {
            console.error(error);
            return false;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = byId('phoneVerificationModal');
        if (!modal) return;

        byId('phoneVerificationClose')?.addEventListener('click', () => closeModal(false));
        byId('phoneVerificationSend')?.addEventListener('click', async function () {
            const button = this;
            const phone = internationalPhone();
            if (!/^\+\d{8,15}$/.test(phone)) {
                setError('Enter a valid WhatsApp number.');
                return;
            }
            button.disabled = true;
            setError('Sending your code...', true);
            try {
                const response = await fetch('request_phone_otp.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({phone})
                });
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.message || 'Unable to send code');
                setStep('code');
                byId('phoneVerificationCode')?.focus();
            } catch (error) {
                setError(error.message || 'Unable to send code.');
            } finally {
                button.disabled = false;
            }
        });

        byId('phoneVerificationVerify')?.addEventListener('click', async function () {
            const button = this;
            const code = (byId('phoneVerificationCode')?.value || '').replace(/\D/g, '');
            if (!/^\d{6}$/.test(code)) {
                setError('Enter the six-digit code.');
                return;
            }
            button.disabled = true;
            setError('Verifying...', true);
            try {
                const response = await fetch('verify_phone_otp.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({code})
                });
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.message || 'Verification failed');
                phoneVerified = true;
                statusKnown = true;
                setError('Phone number verified.', true);
                setTimeout(() => closeModal(true), 450);
            } catch (error) {
                setError(error.message || 'Verification failed.');
            } finally {
                button.disabled = false;
            }
        });

        byId('phoneVerificationBack')?.addEventListener('click', () => setStep('phone'));
    });

    window.TTRPhoneVerification = {ensureVerifiedPhone, refresh: checkStatus};
})();

