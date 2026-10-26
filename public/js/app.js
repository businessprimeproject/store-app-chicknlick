// Application JavaScript entry point for shared-hosting deployments.
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (toggle && password) {
        toggle.addEventListener('click', () => {
            password.type = password.type === 'password' ? 'text' : 'password';
        });
    }
});
