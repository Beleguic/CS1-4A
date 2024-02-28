var accordionHeaders = document.querySelectorAll('.accordion-header');
accordionHeaders.forEach(function(header) {
    header.addEventListener('click', function () {
        var accordionContainer = this.parentNode;
        var isActive = accordionContainer.classList.contains('active');
        accordionHeaders.forEach(function (header) {
            header.parentNode.classList.remove('active');
        });
        if (!isActive) {
            accordionContainer.classList.add('active');
        }
    });
});