document.addEventListener('DOMContentLoaded', function() {
    const showPasswordCheckbox = document.querySelector('#account_showPassword');
    const passwordsContainer = document.querySelector('#passwords');
    const passwordInputs = document.querySelectorAll('#passwords input[type=password]');
    
    if (showPasswordCheckbox && passwordsContainer) {
        // Désactiver les champs de mot de passe par défaut
        passwordInputs.forEach(function(input) {
            input.setAttribute('disabled', 'disabled');
        });
        
        showPasswordCheckbox.addEventListener('change', function() {
            if (showPasswordCheckbox.checked) {
                passwordsContainer.classList.remove('hidden');
                passwordInputs.forEach(function(input) {
                    input.removeAttribute('disabled');
                });
            } else {
                passwordsContainer.classList.add('hidden');
                passwordInputs.forEach(function(input) {
                    input.setAttribute('disabled', 'disabled');
                    input.value = ''; // Vider les champs quand on les cache
                });
            }
        });
    }
});