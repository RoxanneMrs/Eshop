
import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';

// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// loads the jquery package from node_modules
import $ from 'jquery'

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// NAV PRINCIPALE
    // la page de ma nav que j'hover devient verte
    $('#nav li').hover(function() {
        if (!$(event.target).closest('#creations') || window.innerWidth >= 768) {
            $(this).addClass('highlight');
        }
    }, function() {
            $(this).removeClass('highlight');
    });

    // la page de ma nav sur laquelle je clique devient violette
    $('#nav li').click(function() {
        if (!$(event.target).closest('#creations') || window.innerWidth > 768) {
            $('li.selected').find('a').css('color', 'rgb(124, 95, 138)');   
            $('li').removeClass('selected');
            $(this).addClass('selected');
            $(this).find('a').css('color', 'rgb(247, 244, 240)');
        }
    });

    const CREATIONS = $("#creations");
    const DROPDOWN = $("#dropdown");

    CREATIONS.hover(function() {
        DROPDOWN.slideToggle("slow")
    });

    // pour le responsive, afficher la nav grâce au menu burger
    $("#burger_menu").click(function(){
        $("#nav").slideToggle();
    });

    // ici je fais en sorte que "mes créations" ne redirigent pas vers las page index mais qu'à la place il permette à ce que le dropdown s'ouvre et se ferme 
    let isDropdownOpen = false;
    CREATIONS.click(function(event) {
        if (window.innerWidth <= 768) {
            event.preventDefault();
            isDropdownOpen = !isDropdownOpen;
            CREATIONS.toggleClass('open', isDropdownOpen);
            DROPDOWN.slideToggle("slow");
        }
    });

    // les liens sous "mes créations" doivent rester cliquables
    DROPDOWN.find('a').click(function(event) {
        event.stopPropagation(); 
    });



/// PAGE PRODUCT : LISTE DES CATEGORIES
$('#products-categories li').hover(function() {
    $(this).addClass('highlight');
}, function() {
    $(this).removeClass('highlight');
});

$('#products-categories li').click(function() {
    $('li.selected').find('a').css('color', 'rgb(124, 95, 138)');   
    $('li').removeClass('selected');
    $(this).addClass('selected');
    $(this).find('a').css('color', 'rgb(247, 244, 240)');
});




// REQUETE ASYNC POUR LES CATEGORIES DE LA PAGE PRODUCT
$(document).ready(function() {
    $("#products-categories a").click(function(event) {
        event.preventDefault(); 

        const isViewAll = $(this).attr('id') === 'view-all';
        const id_category = isViewAll ? 0 : parseInt($(this).attr('id').replace('category-link-', ''));
  
        hidePriceDropdown();

        fetchProductByCategory(id_category);

            async function fetchProductByCategory(id_category) {

                const url = `/product/api/category/${id_category}`;
            
                const response = await fetch(url, {
                    method: 'GET', 
                    headers: {
                    'Content-Type': 'application/json', 
                    },
                });
                
                let data = await response.json();    
                let listProducts = "";

                for(let i = 0; i < data.length; i++) {

                    listProducts += `<a href="/product/product/${data[i].id}">` +
                        `<div class='products-card'>` +
                            `<div class='products-img-container'>` +
                                `<img src='/uploads/products/${data[i].picture}' alt='${data[i].name}' title='${data[i].name}'>` +
                            `</div>` +
                            `<span class='type'>Pièce unique</span>` +
                            `<h5>${data[i].name}</h5>` +
                            `<div class='trait'></div>` +
                            `<span class='price'>${data[i].price}€</span>` +
                            
                            `<form action='/cart/${data[i].id}' method='POST'>` +
                                `<input type="hidden" value="1" name="quantity" />` +
                                `<input type='submit' class='btn-add' value='Ajouter au panier'>` +
                            `</form>` +
                        `</div>` +
                    `</a>`;
                }

            $('#list-products').html(listProducts);
        }

        function hidePriceDropdown() {
            // Sélectionne et masque le dropdown de prix
            $('#filter-dropdown').hide(); // ou $('.dropdown').css('display', 'none');
        }

        $("#products-categories a").removeClass("active"); 
        if (isViewAll) {
            $("view-all").addClass("active");
        } else {
        $(`#products-categories a#${id_category}`).addClass("active"); 
        }
        
        if (id_category) {
            fetchProductByCategory(id_category);
        }
    });
});
  

////  CODE POUR AFFICHER LES PRODUITS PETIT A PETIT
    
    let lastProductId = 0;
    let lastProductPrice = 0;
    let allProductsLoaded = false; 

    /// Fonction qui crée les cards à afficher
    function createProductElement(product) {

        const productElement = document.createElement('div');
        productElement.classList.add('products-card');
        productElement.dataset.productId = product.id;
        productElement.dataset.productPrice = product.price;
    
        // Ajoutez le contenu HTML du produit (nom, image, prix, etc.)
        productElement.innerHTML = `<a href="/product/product/${product.id}">            
                                        <div class="products-img-container">
                                            <img src="/uploads/products/${product.picture}" alt="${product.name}" title="${product.name}">
                                        </div>
                                        <span class="type">Pièce unique</span>
                                        <h5> ${product.name} </h5>
                                        <div class="trait"> </div>
                                        <span class="price"> ${product.price} € </span>  
                                        <form action="/cart/${product.id}" method="POST">
                                            <input type="hidden" value="1" name="quantity" />
                                            <input type="submit" class="btn-add" value="Ajouter au panier">
                                        </form>
                                    </a>`;    
    
        return productElement;
    } 

    // Fonction qui récupère l'id du dernier produit affiché et récupère les infos des produits à l'id supérieur au dernier produit
    function loadProducts(filter, categoryId = null) {

        if (allProductsLoaded) {
            return;
        }

        const lastProductElement = document.querySelector('.products-card:last-of-type');
        if (lastProductElement) {
            lastProductId = parseInt(lastProductElement.dataset.productId, 10);
            lastProductPrice = parseInt(lastProductElement.dataset.productPrice, 10);
        }

        if (filter !== 'ASC' && filter !== 'DESC') {
            filter = 'none';
        }

        const url =`/product/${encodeURIComponent(categoryId)}/load-more/${encodeURIComponent(filter)}`; // l'url change en fonction du filtre récupéré
        const formData = new FormData();
        formData.append('lastProductId', lastProductId);
        formData.append('lastProductPrice', lastProductPrice);
        if (categoryId !== null) {
            formData.append('categoryId', categoryId);
        }

        // récupère les données de la requête
        fetch(url, {
            method: 'POST',
            body: formData,
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {

            const productsContainer = document.querySelector('#list-products'); // le parent qui contient mes cards product
            
            data.products.forEach(product => {
                const productElement = createProductElement(product); // créer l'élément HTML pour le produit
                productsContainer.appendChild(productElement); // ajouter l'élément au container
            });

            // met à jour la valeur de 'lastProductId' pour le prochain chargement (à vérifier)
            if (data.products.length > 0) {
                lastProductId = data.products[data.products.length - 1].id;
                lastProductPrice = data.products[data.products.length - 1].price;
            }

            if (data.products.length < 8) {
                allProductsLoaded = true;
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des produits:', error);
        });
 
    }


    // pour avoir le bon ID de category dans loadMore, je le prends depuis l'URL
    function extractCategoryIdFromUrl(url) {
        const match = url.match(/\/product\/(\d+)/);

        if (match && match.length > 1) {
            return match[1]; // Le groupe capturé correspond à l'ID de la catégorie
        }

        return null; // Retourne null si aucune correspondance trouvée
    }


    // fonction qui détecte le scroll en bas de page et éxécute la fonction pour charger les produits
    window.addEventListener('scroll', function() {
        if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight) {
            const currentFilter = $('#dropdown-toggle').attr('data-filter');
            const currentUrl = window.location.href; // je récupère l'url actuelle de ma page
            const currentCategory = extractCategoryIdFromUrl(currentUrl); // et grâce à elle je récupère ma catégorie
            loadProducts(currentFilter === 'none' ? null : currentFilter, currentCategory); 
            // charger initialement les produits avec le filtre appliqué s'il existe
        }
    });





  