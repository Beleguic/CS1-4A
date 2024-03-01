document.addEventListener('DOMContentLoaded', function () {

    var index = 0;
    const table = document.createElement("table");
    // Récupérer le prototype du formulaire pour les tags
    const tagForm = document.getElementById('devis_produits');
    const tagFormContainer = tagForm; //.parentElement

    // Ajouter le bouton "Ajouter un tag"
    const addButton = document.createElement('button');
    addButton.type = 'button';
    addButton.className = 'btn btn-success';
    addButton.textContent = 'Ajouter un produit';

    const newTagLinkLi = document.createElement('div');
    newTagLinkLi.appendChild(addButton);

    // Ajouter le bouton à la fin de la liste des tags
    tagFormContainer.appendChild(newTagLinkLi);

    addTagForm(index, table);

    // Gérer l'ajout de nouveaux champs au clic sur le bouton
    addButton.addEventListener('click', function (e) {
        index++;
        addTagForm(index, table);
    });

    // Fonction pour ajouter un nouveau champ de tag
    function addTagForm(index, table) {
        // Récupérer le nombre total de tags présents

        // Récupérer le prototype du champ tag
        const prototype = tagForm.getAttribute('data-prototype').replace(/__name__/g, index);

        // Créer un élément div pour analyser la chaîne HTML
        var divElement = document.createElement('div');
        divElement.innerHTML = prototype;

        // Sélectionner tous les éléments input dans le div
        // Créer un élément div pour analyser la chaîne HTML
        var inputData = [];
        let g = divElement.children[0].children[1].children;

        for (let i = 0; i < g.length; i++) {
            if(g[i].nodeName == "DIV"){
                var inputInfo = {
                    label: g[i].children[0].textContent.trim(),
                    input: g[i].children[1],
                };
                inputData.push(inputInfo);
            }
            else {
                let label
                if(i === 5){
                    label = "Quantité";
                }else if(i === 6){
                    label = "Prix Totale";
                }

                var inputInfo = {
                    label: label,
                    input: g[i],
                };
                inputData.push(inputInfo);
            }
        }


        // Sélectionner tous les éléments input et select dans le div
        //var inputElements = divElement.querySelectorAll('input');
        //var selectElement = divElement.querySelector('select');
        // Créer un tableau pour stocker les informations
        //var inputData = [];

        // Parcourir les éléments input et stocker les informations dans le tableau
        /*inputElements.forEach(function (input) {
            var inputInfo = {
                label: input.previousElementSibling.textContent.trim(),
                input: input,
            };
            inputData.push(inputInfo);
        });

        // Stocker les informations du select dans le tableau
        var selectInfo = {
            label: selectElement.previousElementSibling.textContent.trim(),
            input: selectElement,
        };
        inputData.push(selectInfo);*/

        // création du select ici
        /*for (let i = 0; i < products.length; i++) {
            console.log(products[i]);
        }*/
        // Ajout du select a la place du input[0]
        let select = document.createElement("select");
        select.name = inputData[0].input.name;
        select.id = inputData[0].input.id;
        select.required = inputData[0].input.required;
        let option = document.createElement('option');
        option.value = "";
        option.disabled = true;
        option.selected = true;
        option.textContent = "Selectionner un produits";
        select.appendChild(option);


        for (var i = 0; i < products.length; i++) {
            let option = document.createElement('option');
            option.value = i;
            option.textContent = products[i].name;
            select.appendChild(option);
        }

        select.addEventListener("change", function(j){

            let selected = select.selectedOptions[0].value;
            let trChildren = j.target.parentElement.parentElement.children;
            console.log(products[selected]);

            var selectElement = trChildren[2].children[0];

            // Définir l'index de l'option que vous souhaitez sélectionner (par exemple, l'option avec la valeur "option2")
            var optionValueToSelect = products[selected]['category'];

            // Parcourir les options pour trouver l'index de l'option à sélectionner
            for (var i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].textContent === optionValueToSelect) {
                    // Définir l'index de l'option à sélectionner
                    selectElement.selectedIndex = i;
                    break; // Sortir de la boucle une fois que l'option est trouvée
                }
            }
            // Condition Initiale
            trChildren[1].children[0].value = products[selected]['description'];
            trChildren[3].children[0].value = products[selected]['price']
            trChildren[4].children[0].value = products[selected]['tva'];
            trChildren[5].children[0].value = 1;
            let tvaCalcIni = (products[selected]['tva']/100)+1;
            trChildren[6].children[0].value = (products[selected]['price'] * tvaCalcIni);

            // condition de changement
            let prix = trChildren[3].children[0];
            let tva = trChildren[4].children[0]
            let qte = trChildren[5].children[0];
            let totale = trChildren[6].children[0];
            qte.addEventListener("change", function(){

                console.log("changement qte" + qte.value);

                let tvaCalc = (tva.value / 100) + 1;
                totale.value = qte.value * (prix.value * tvaCalc);
            });
            tva.addEventListener("change", function(){

                let tvaCalc = (tva.value/100)+1;
                totale.value = qte.value * (prix.value * tvaCalc);
            });
            prix.addEventListener("change", function(){

                let tvaCalc = (tva.value/100)+1;
                totale.value = qte.value * (prix.value * tvaCalc);

                console.log("changement prix" + prix.value);
            });
        })

        inputData[0].input = select;

        console.log(inputData);

        // Afficher le tableau résultant dans la console

        if(table.children.length > 0){
            // Tableau deja remplie au moins une fois
            const newTrbody= document.createElement('tr');
            for (let i = 0; i < inputData.length; i++) {
                let td = document.createElement('td');
                if(inputData[i].input.type == "hidden"){
                    inputData[i].input.type = "text";
                }
                td.appendChild(inputData[i].input);
                newTrbody.appendChild(td);
            }
            let tdDeleteButton = document.createElement('td');
            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'btn btn-danger';
            deleteButton.textContent = 'Supprimer';

            deleteButton.addEventListener('click', function (){
                newTrbody.remove();
            });

            tdDeleteButton.appendChild(deleteButton);
            newTrbody.appendChild(tdDeleteButton);

            table.children[1].appendChild(newTrbody);
        }
        else{
            // tableau vierge
            const thead = document.createElement('thead');
            const newTrHead= document.createElement('tr');

            for (let i = 0; i < inputData.length; i++) {
                let th = document.createElement('th');
                th.textContent = inputData[i].label;
                newTrHead.appendChild(th);
            }
            let delButtonSpace = document.createElement('th');
            newTrHead.appendChild(delButtonSpace);
            thead.appendChild(newTrHead);
            table.appendChild(thead);

            const tbody= document.createElement('tbody');
            const newTrbody= document.createElement('tr');
            for (let i = 0; i < inputData.length; i++) {
                let td = document.createElement('td');
                if(inputData[i].input.type == "hidden"){
                    inputData[i].input.type = "text";
                }
                td.appendChild(inputData[i].input);
                newTrbody.appendChild(td);
            }
            let tdDeleteButton = document.createElement('td');
            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'btn btn-danger';
            deleteButton.textContent = 'Supprimer';

            deleteButton.addEventListener('click', function (){
                newTrbody.remove();
            });

            tdDeleteButton.appendChild(deleteButton);
            newTrbody.appendChild(tdDeleteButton);
            tbody.appendChild(newTrbody);

            table.appendChild(tbody);

            newTagLinkLi.before(table);
        }

        /*const newTable = document.createElement('table');
        const thead = document.createElement('thead');
        const newTrHead= document.createElement('tr');

        for (let i = 0; i < inputData.length - 1; i++) {
            let th = document.createElement('th');
            th.textContent = inputData[i].label;
            newTrHead.appendChild(th);
        }
        let delButtonSpace = document.createElement('th');
        newTrHead.appendChild(delButtonSpace);
        thead.appendChild(newTrHead);
        const tbody = document.createElement('tbody');
        const newTrbody= document.createElement('tr');
        for (let i = 0; i < inputData.length - 1; i++) {
            let td = document.createElement('td');
            td.appendChild(inputData[i].input);
            newTrbody.appendChild(td);
        }
        let tdDeleteButton = document.createElement('td');
        const deleteButton = document.createElement('button');
        deleteButton.type = 'button';
        deleteButton.className = 'btn btn-danger';
        deleteButton.textContent = 'Supprimer';
        tdDeleteButton.appendChild(deleteButton);
        newTrbody.appendChild(tdDeleteButton);
        tbody.appendChild(newTrbody);

        newTable.appendChild(thead);
        newTable.appendChild(tbody);*/


        //-----------------------------------------------------------------------------
        // Créer le nouveau champ de tag en ajoutant le prototype à la fin de la liste
        // const newForm = document.createElement('div');
        //newForm.innerHTML = prototype;

        // Ajouter le bouton de suppression à côté du champ

        // newTagLinkLi.before(newTable);


        // Mettre à jour le compteur des tags
        tagForm.setAttribute('data-index', index + 1);
    }

    // Fonction pour supprimer un champ de tag
    function removeTagForm(button) {
        const formToRemove = button.parentElement;
        formToRemove.remove();
    }



});