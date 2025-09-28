document.addEventListener('DOMContentLoaded', () => {
            const formContainer = document.querySelector('.update-profile-container');
            const updateProfileForm = document.getElementById('updateProfileForm');
            const fullNameInput = document.getElementById('fullName');
            const emailInput = document.getElementById('email');
            const fullNameError = document.getElementById('fullNameError');
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

            updateProfileForm.addEventListener('submit', (event) => {
                event.preventDefault();

                clearError(fullNameInput, fullNameError);
                clearError(emailInput, emailError);

                const fullNameValue = fullNameInput.value.trim();
                const emailValue = emailInput.value.trim();
                let isValid = true;

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

                if (isValid) {
                    formContainer.innerHTML = `
                        <h1>Profile Updated!</h1>
                        <p class="success-message">Your profile information has been saved.</p>
                        <p class="back-link">
                            <a href="home.html">&larr; Back to Home</a>
                        </p>
                    `;
                }
            });
        });