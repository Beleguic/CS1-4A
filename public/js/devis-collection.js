document.addEventListener('DOMContentLoaded', function() {
    const collectionContainer = document.getElementById('produits-collection');
    const addButton = document.getElementById('add-produit');
    let index = collectionContainer.children.length;

    // Fonction pour ajouter un nouveau produit
    function addProduit() {
        const prototype = collectionContainer.getAttribute('data-prototype');
        const newForm = prototype.replace(/__name__/g, index);
        
        // Parser le HTML du prototype pour extraire les champs
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = newForm;
        
        // Extraire les champs individuels
        const nameField = tempDiv.querySelector('input[name*="[name]"]');
        const descriptionField = tempDiv.querySelector('input[name*="[description]"]');
        const categoryField = tempDiv.querySelector('select[name*="[category]"]');
        const priceField = tempDiv.querySelector('input[name*="[price]"]');
        const tvaField = tempDiv.querySelector('input[name*="[tva]"]');
        const quantiteField = tempDiv.querySelector('input[name*="[quantite]"]');
        const existingProductField = tempDiv.querySelector('select[name*="[existingProduct]"]');
        
        // Créer un nouvel élément div avec la même structure que le template
        const newElement = document.createElement('div');
        newElement.className = 'produit-item border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4 bg-gray-50 dark:bg-gray-800';
        newElement.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100">Produit ${index + 1}</h4>
                <button type="button" class="remove-produit text-red-500 hover:text-red-700 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Sélecteur de produit existant -->
                <div class="flex flex-col gap-2 md:col-span-2 lg:col-span-3">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Produit existant</label>
                    <select class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 focus:border-mainColor-400 focus:outline-none focus:ring-2 focus:ring-mainColor-400/25 transition-all duration-200 product-selector">
                        <option value="">-- Sélectionner un produit existant --</option>
                    </select>
                </div>

                <!-- Nom du produit -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200 required" for="${nameField.id}">Nom</label>
                    <input type="text" id="${nameField.id}" name="${nameField.name}" required="required" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 focus:border-mainColor-400 focus:outline-none focus:ring-2 focus:ring-mainColor-400/25 transition-all duration-200">
                </div>

                <!-- Description -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200 required" for="${descriptionField.id}">Description</label>
                    <input type="text" id="${descriptionField.id}" name="${descriptionField.name}" required="required" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 focus:border-mainColor-400 focus:outline-none focus:ring-2 focus:ring-mainColor-400/25 transition-all duration-200">
                </div>

                <!-- Catégorie -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200 required" for="${categoryField.id}">Catégorie</label>
                    <select id="${categoryField.id}" name="${categoryField.name}" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 focus:border-mainColor-400 focus:outline-none focus:ring-2 focus:ring-mainColor-400/25 transition-all duration-200">${categoryField.innerHTML}</select>
                </div>

                <!-- Prix -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200 required" for="${priceField.id}">Prix (€)</label>
                    <input type="text" id="${priceField.id}" name="${priceField.name}" required="required" inputmode="decimal" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 focus:border-mainColor-400 focus:outline-none focus:ring-2 focus:ring-mainColor-400/25 transition-all duration-200" step="0.01">
                </div>

                <!-- TVA -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200 required" for="${tvaField.id}">TVA (%)</label>
                    <input type="text" id="${tvaField.id}" name="${tvaField.name}" required="required" inputmode="decimal" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 focus:border-mainColor-400 focus:outline-none focus:ring-2 focus:ring-mainColor-400/25 transition-all duration-200" step="0.01">
                </div>

                <!-- Quantité -->
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-200 required" for="${quantiteField.id}">Quantité</label>
                    <input type="text" id="${quantiteField.id}" name="${quantiteField.name}" required="required" inputmode="decimal" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 focus:border-mainColor-400 focus:outline-none focus:ring-2 focus:ring-mainColor-400/25 transition-all duration-200" min="1" value="1">
                </div>
            </div>
        `;

        // Ajouter l'élément au conteneur
        collectionContainer.appendChild(newElement);
        
        // Ajouter l'événement de suppression
        const removeButton = newElement.querySelector('.remove-produit');
        removeButton.addEventListener('click', function() {
            newElement.remove();
            updateProduitNumbers();
        });

        // Remplir le sélecteur de produit existant avec les options
        const productSelector = newElement.querySelector('.product-selector');
        if (typeof products !== 'undefined' && products.length > 0) {
            products.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = `${product.name} - ${product.category} (${product.price}€)`;
                productSelector.appendChild(option);
            });
        } else {
            console.log('Products data not available:', typeof products, products);
        }

        // Ajouter l'événement de changement sur le sélecteur de produit
        productSelector.addEventListener('change', function() {
            const selectedProductId = this.value;
            if (selectedProductId && typeof products !== 'undefined') {
                const selectedProduct = products.find(p => p.id === selectedProductId);
                if (selectedProduct) {
                    fillProductFields(newElement, selectedProduct);
                }
            }
        });

        index++;
        updateProduitNumbers();
    }

    // Fonction pour mettre à jour les numéros de produits
    function updateProduitNumbers() {
        const produitItems = collectionContainer.querySelectorAll('.produit-item');
        produitItems.forEach((item, index) => {
            const title = item.querySelector('h4');
            if (title) {
                title.textContent = `Produit ${index + 1}`;
            }
        });
    }

    // Fonction pour remplir les champs d'un produit
    function fillProductFields(produitElement, productData) {
        // Remplir le nom
        const nameInput = produitElement.querySelector('input[name*="[name]"]');
        if (nameInput) nameInput.value = productData.name;

        // Remplir la description
        const descriptionInput = produitElement.querySelector('input[name*="[description]"]');
        if (descriptionInput) descriptionInput.value = productData.description;

        // Remplir le prix
        const priceInput = produitElement.querySelector('input[name*="[price]"]');
        if (priceInput) priceInput.value = productData.price;

        // Remplir la TVA
        const tvaInput = produitElement.querySelector('input[name*="[tva]"]');
        if (tvaInput) tvaInput.value = productData.tva;

        // Remplir la quantité (par défaut à 1)
        const quantityInput = produitElement.querySelector('input[name*="[quantite]"]');
        if (quantityInput) quantityInput.value = 1;

        // Sélectionner la catégorie
        const categorySelect = produitElement.querySelector('select[name*="[category]"]');
        if (categorySelect) {
            // Trouver l'option correspondant à la catégorie
            const options = categorySelect.querySelectorAll('option');
            options.forEach(option => {
                if (option.textContent.trim() === productData.category) {
                    option.selected = true;
                }
            });
        }
    }

    // Fonction pour pré-remplir les sélecteurs existants lors de l'édition
    function prefillExistingSelectors() {
        const existingProduitItems = collectionContainer.querySelectorAll('.produit-item');
        existingProduitItems.forEach(item => {
            const productSelector = item.querySelector('.product-selector');
            const nameInput = item.querySelector('input[name*="[name]"]');
            
            if (productSelector && nameInput && nameInput.value && typeof products !== 'undefined') {
                // Remplir les options si elles ne sont pas déjà remplies
                if (productSelector.children.length <= 1) {
                    products.forEach(product => {
                        const option = document.createElement('option');
                        option.value = product.id;
                        option.textContent = `${product.name} - ${product.category} (${product.price}€)`;
                        productSelector.appendChild(option);
                    });
                }
                
                // Sélectionner le produit correspondant
                const productName = nameInput.value.trim();
                const options = productSelector.querySelectorAll('option');
                options.forEach(option => {
                    if (option.value && option.textContent.includes(productName)) {
                        option.selected = true;
                    }
                });
            }
        });
    }

    // Ajouter l'événement au bouton d'ajout
    addButton.addEventListener('click', addProduit);

    // Initialiser les sélecteurs existants
    prefillExistingSelectors();

    // Ajouter les événements de changement sur les sélecteurs existants
    const existingSelectors = collectionContainer.querySelectorAll('.product-selector');
    existingSelectors.forEach(selector => {
        selector.addEventListener('change', function() {
            const selectedProductId = this.value;
            if (selectedProductId && typeof products !== 'undefined') {
                const selectedProduct = products.find(p => p.id === selectedProductId);
                if (selectedProduct) {
                    const produitElement = this.closest('.produit-item');
                    fillProductFields(produitElement, selectedProduct);
                }
            }
        });
    });
});