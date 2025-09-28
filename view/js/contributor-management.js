 document.addEventListener('DOMContentLoaded', () => {
            const formContainer = document.querySelector('.invite-container');
            const forgotPasswordForm = document.getElementById('invite-form');
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('emailError');

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

            forgotPasswordForm.addEventListener('submit', (event) => {
                event.preventDefault();
                clearError(emailInput, emailError);
                const emailValue = emailInput.value.trim();

                let isValid = true;

                if (emailValue === '') {
                    showError(emailInput, emailError, 'Email address is required.');
                    isValid = false;
                } else if (!isValidEmail(emailValue)) {
                    showError(emailInput, emailError, 'Please enter a valid email address.');
                    isValid = false;
                }

                if (isValid) {
                    formContainer.innerHTML = `
                        <h1>Check Your Email</h1>
                        <p class="success-message">A invitation has been sent to <strong>${emailValue}</strong>. </p>
                        <p class="back-to-home-link">
                            <a href="home.html">&larr; Back to home</a>
                        </p>
                    `;
                }
            });


        });