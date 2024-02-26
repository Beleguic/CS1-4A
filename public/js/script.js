    // Sélectionnez tous les éléments .accordion-header
    var accordionHeaders = document.querySelectorAll('.accordion-header');

    // Pour chaque .accordion-header, ajoutez un écouteur d'événements click
    accordionHeaders.forEach(function(header) {
    header.addEventListener('click', function () {
        var accordionContainer = this.parentNode; // Parent de l'en-tête, qui est l'accordion-container
        var isActive = accordionContainer.classList.contains('active'); // Vérifie si l'accordion-container est déjà actif

        // Supprime la classe 'active' de tous les accordion-container
        accordionHeaders.forEach(function (header) {
            header.parentNode.classList.remove('active');
        });

        // Si l'accordion-container n'était pas actif, l'activer
        if (!isActive) {
            accordionContainer.classList.add('active');
        }
    });
});