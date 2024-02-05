const showPasswordCheckbox = document.querySelector('#account_showPassword');
const passwordsContainer = document.querySelector('#passwords');
const passwordsInputs = document.querySelectorAll('#passwords .input-field.password input[type=password]')
passwordsInputs.forEach(function(input) {
    input.setAttribute('disabled', 'disabled');
});
showPasswordCheckbox.addEventListener('change', function() {
    if (showPasswordCheckbox.checked) {
        passwordsContainer.classList.remove('hidden');
        passwordsInputs.forEach(function(input) {
            input.removeAttribute('disabled');
        });
    } else {
        passwordsContainer.classList.add('hidden');
        passwordsInputs.forEach(function(input) {
            input.setAttribute('disabled', 'disabled');
        });
    }
});