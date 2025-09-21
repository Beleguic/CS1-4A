document.addEventListener('DOMContentLoaded', function() {
    const showPasswordCheckbox = document.querySelector('#account_showPassword');
    const passwordsContainer = document.querySelector('#passwords');
    const passwordInputs = document.querySelectorAll('#passwords input');
    
    if (showPasswordCheckbox && passwordsContainer) {
        showPasswordCheckbox.addEventListener('change', function() {
            if (showPasswordCheckbox.checked) {
                passwordsContainer.classList.remove('hidden');
            } else {
                passwordsContainer.classList.add('hidden');
                // Vider les champs quand on les cache
                passwordInputs.forEach(function(input) {
                    input.value = '';
                });
            }
        });
    }
});