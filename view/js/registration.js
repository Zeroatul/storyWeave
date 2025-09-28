document.addEventListener('DOMContentLoaded', () => {
            const registrationForm = document.getElementById('registrationForm');
            const fullNameInput = document.getElementById('fullName');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            const fullNameError = document.getElementById('fullNameError');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');

            const showError = (inputElement, errorElement, message) => {
                inputElement.classList.add('invalid');
                errorElement.textContent = message;
            };

            const clearError = (inputElement, errorElement) => {
                inputElement.classList.remove('invalid');
                errorElement.textContent = '';
            };

            const isValidEmail = (email) => {
                const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
                return regex.test(String(email).toLowerCase());
            };

            const validateForm = () => {
                let isValid = true;
                clearError(fullNameInput, fullNameError);
                clearError(emailInput, emailError);
                clearError(passwordInput, passwordError);

                const fullNameValue = fullNameInput.value.trim();
                const emailValue = emailInput.value.trim();
                const passwordValue = passwordInput.value.trim();

                if (fullNameValue === '') {
                    showError(fullNameInput, fullNameError, 'Full name is required.');
                    isValid = false;
                }

                if (emailValue === '') {
                    showError(emailInput, emailError, 'Email address is required.');
                    isValid = false;
                } else if (!isValidEmail(emailValue)) {
                    showError(emailInput, emailError, 'Please enter a valid email address.');
                    isValid = false;
                }

                if (passwordValue === '') {
                    showError(passwordInput, passwordError, 'Password is required.');
                    isValid = false;
                } else if (passwordValue.length < 8) {
                    showError(passwordInput, passwordError, 'Password must be at least 8 characters.');
                    isValid = false;
                }

                return isValid;
            };

            registrationForm.addEventListener('submit', (event) => {
                if (!validateForm()) {
                    event.preventDefault();
                }
            });

        });