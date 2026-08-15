document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
    button.addEventListener('click', function () {
        const input = document.querySelector(button.dataset.passwordToggle);
        const hidden = input.type === 'password';
        input.type = hidden ? 'text' : 'password';
        button.setAttribute('aria-label', hidden ? 'Sembunyikan password' : 'Tampilkan password');
    });
});

document.querySelectorAll('form[data-auth-form]').forEach(function (form) {
    form.addEventListener('submit', function () {
        if (!form.checkValidity()) return;
        const button = form.querySelector('.primary-button');
        if (!button) return;
        button.disabled = true;
        button.dataset.label = button.textContent.trim();
        button.textContent = 'Memproses...';
    });
});

const password = document.querySelector('#password');
const confirmation = document.querySelector('#password_confirmation');
if (password && confirmation) {
    const validateConfirmation = function () {
        confirmation.setCustomValidity(confirmation.value && confirmation.value !== password.value ? 'Konfirmasi password tidak sama.' : '');
    };
    password.addEventListener('input', validateConfirmation);
    confirmation.addEventListener('input', validateConfirmation);
}
