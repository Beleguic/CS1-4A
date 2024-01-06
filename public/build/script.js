const accordion = document.getElementsByClassName('accordion-container');

for (i=0; i<accordion.length; i++) {
    accordion[i].addEventListener('click', function () {
        console.log("click")
        this.classList.toggle('active')
    })
}