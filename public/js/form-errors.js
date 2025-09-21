/**
 * Script pour ajouter la classe .errors aux listes <ul> contenant des erreurs de formulaire
 */
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour ajouter la classe .errors aux ul contenant des erreurs
    function addErrorClassToLists() {
        // Chercher tous les éléments avec des erreurs de formulaire
        const errorElements = document.querySelectorAll('.form-error, .invalid-feedback, .text-red-500, .text-danger, [class*="error"]');
        
        errorElements.forEach(function(errorElement) {
            // Remonter dans l'arbre DOM pour trouver le ul parent
            let parent = errorElement.parentElement;
            
            while (parent && parent !== document.body) {
                if (parent.tagName === 'UL') {
                    parent.classList.add('errors');
                    break;
                }
                parent = parent.parentElement;
            }
        });
        
        // Alternative: chercher directement les ul qui contiennent des erreurs
        const allUls = document.querySelectorAll('ul');
        allUls.forEach(function(ul) {
            const hasErrors = ul.querySelector('.form-error, .invalid-feedback, .text-red-500, .text-danger, [class*="error"]');
            if (hasErrors) {
                ul.classList.add('errors');
            }
        });
    }
    
    // Fonction pour ajouter les classes d'erreur lors de la soumission
    function handleFormSubmission() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                // Retirer les anciennes classes d'erreur
                const previousErrorUls = document.querySelectorAll('ul.errors');
                previousErrorUls.forEach(function(ul) {
                    ul.classList.remove('errors');
                });
                
                // Après un court délai, vérifier s'il y a de nouvelles erreurs
                setTimeout(function() {
                    addErrorClassToLists();
                }, 100);
            });
            
            // Ajouter aussi la classe lors du changement des champs
            const formFields = form.querySelectorAll('input, select, textarea');
            formFields.forEach(function(field) {
                field.addEventListener('blur', function() {
                    setTimeout(function() {
                        addErrorClassToLists();
                    }, 50);
                });
            });
        });
    }
    
    // Exécuter au chargement de la page
    addErrorClassToLists();
    handleFormSubmission();
    
    // Observer les changements dans le DOM (pour les erreurs AJAX)
    if (window.MutationObserver) {
        const observer = new MutationObserver(function(mutations) {
            let shouldCheck = false;
            
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || mutation.type === 'attributes') {
                    shouldCheck = true;
                }
            });
            
            if (shouldCheck) {
                setTimeout(addErrorClassToLists, 10);
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['class']
        });
    }
});
