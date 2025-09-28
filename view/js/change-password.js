document.addEventListener('DOMContentLoaded', () => {
            const formContainer = document.querySelector('.change-password-container');
            const changePasswordForm = document.getElementById('changePasswordForm');
            const oldPasswordInput = document.getElementById('oldPassword');
            const newPasswordInput = document.getElementById('newPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const oldPasswordError = document.getElementById('oldPasswordError');
            const newPasswordError = document.getElementById('newPasswordError');
            const confirmPasswordError = document.getElementById('confirmPasswordError');

            const showError = (inputElement, errorElement, message) => {
                inputElement.classList.add('invalid');
                errorElement.textContent = message;
            };

            const clearError = (inputElement, errorElement) => {
                inputElement.classList.remove('invalid');
                errorElement.textContent = '';
            };

            changePasswordForm.addEventListener('submit', (event) => {
                event.preventDefault();

                clearError(oldPasswordInput, oldPasswordError);
                clearError(newPasswordInput, newPasswordError);
                clearError(confirmPasswordInput, confirmPasswordError);

                const oldPasswordValue = oldPasswordInput.value.trim();
                const newPasswordValue = newPasswordInput.value.trim();
                const confirmPasswordValue = confirmPasswordInput.value.trim();
                let isValid = true;

                if (oldPasswordValue === '') {
                    showError(oldPasswordInput, oldPasswordError, 'Old password is required.');
                    isValid = false;
                }

                if (newPasswordValue === '') {
                    showError(newPasswordInput, newPasswordError, 'New password is required.');
                    isValid = false;
                } else if (newPasswordValue.length < 8) {
                    showError(newPasswordInput, newPasswordError, 'Password must be at least 8 characters.');
                    isValid = false;
                }

                if (confirmPasswordValue === '') {
                    showError(confirmPasswordInput, confirmPasswordError, 'Please confirm your password.');
                    isValid = false;
                } else if (newPasswordValue !== confirmPasswordValue) {
                    showError(confirmPasswordInput, confirmPasswordError, 'Passwords do not match.');
                    isValid = false;
                }

                if (isValid) {
                    formContainer.innerHTML = `
                        <h1>Password Updated!</h1>
                        <p class="success-message">Your password has been changed successfully.</p>
                        <p class="back-to-login-link">
                            <a href="../php/login.php">&larr; Back to Login</a>
                        </p>
                    `;
                }
            });
        });