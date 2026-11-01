document.addEventListener('DOMContentLoaded', () => {
    const refresh = document.getElementById('refreshCaptcha');
    const image = document.getElementById('captchaImage');
    if (refresh && image) {
        refresh.addEventListener('click', () => {
            image.src = 'captcha.php?t=' + Date.now();
        });
    }

    const confirm = document.getElementById('confirm_password');
    const password = document.getElementById('password');
    if (confirm && password) {
        confirm.addEventListener('input', () => {
            confirm.setCustomValidity(
                confirm.value && confirm.value !== password.value
                    ? 'Passwords do not match.'
                    : ''
            );
        });
        password.addEventListener('input', () => {
            if (confirm.value) {
                confirm.setCustomValidity(
                    confirm.value !== password.value ? 'Passwords do not match.' : ''
                );
            }
        });
    }

    const otp = document.getElementById('otp');
    if (otp) otp.addEventListener('input', () => {
        otp.value = otp.value.replace(/\D/g, '').slice(0, 6);
    });
});
