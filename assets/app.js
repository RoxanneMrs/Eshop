
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
$('#nav li').hover(function() {
    $(this).addClass('highlight');
}, function() {
    $(this).removeClass('highlight');
});

$('#nav li').click(function() {
    $('li.selected').find('a').css('color', 'rgb(124, 95, 138)');   
    $('li').removeClass('selected');
    $(this).addClass('selected');
    $(this).find('a').css('color', 'rgb(247, 244, 240)');
});

const CREATIONS = $("#creations");
const DROPDOWN = $("#dropdown");

CREATIONS.hover(function() {
    DROPDOWN.slideToggle("slow")
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
  
        const id_category = parseInt($(this).attr('id').replace('category-link-', ''));
  
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

                if (filter === 'desc') {
                    data.sort((a, b) => b.price - a.price);
                } 
                
                if (filter === 'asc') {
                    data.sort((a, b) => a.price - b.price);
                }
            
                let listProducts = "";

                for(let i = 0; i < data.length; i++) {

                    listProducts += "<a href='{{ path('app_product_show', { id: " + data[i].id + " }) }}'>" +
                        "<div class='products-card'>" +             
                            "<div class='products-img-container'>" +
                                "<img src='/uploads/products/" + data[i].picture +"' alt='"+ data[i].name +"' title='"+ data[i].name +"'>" +
                            "</div>" +
                            "<span class='type'>Pièce unique</span>" +
                            "<h5>" + data[i].name + "</h5>" +
                            "<div class='trait'> </div>" +
                            "<span class='price'>" + data[i].price + "€ </span>" +
                            
                            "<form action='{{ path('app_cart_add', { 'idProduct':" + data[i].id + "}) }}' method='POST'>" +
                                "<input type='submit' class='btn-add' value='Ajouter au panier'>" +
                            "</form>" +
                        "</div>" +
                    "</a>";
                }

            $('#list-products').html(listProducts);

        }

        $("#products-categories a").removeClass("active"); 
        $(`#products-categories a#${id_category}`).addClass("active"); 

        if (id_category) {
            fetchProductByCategory(id_category);
        }
    });
});
  

//// REQUETE ASYNC TRIER PRODUITS PAR PRIX
/* $(document).ready(function() {
    
    $("#filter").change(function() {

        async function fetchData(filter) {

            try {
                const url = `/product/filter/${filter}`;
        
                const response = await fetch(url, {
                    method: 'GET', 
                    headers: {
                    'Content-Type': 'application/json', 
                    },
                });
        
                if (!response.ok) {
                    throw new Error(`Erreur: ${response.status}`); 
                }
        
                const data = await response.json();

                if (filter === 'desc') {
                    data.sort((a, b) => b.price - a.price);
                } 
                
                if (filter === 'asc') {
                    data.sort((a, b) => a.price - b.price);
                }

                let listProducts = "";

                for(let i = 0; i < data.length; i++) {

                    listProducts += "<a href='{{ path('app_product_show', { id: " + data[i].id + " }) }}'>" +
                        "<div class='products-card'>" +             
                            "<div class='products-img-container'>" +
                                "<img src='/uploads/products/" + data[i].picture +"' alt='"+ data[i].name +"' title='"+ data[i].name +"'>" +
                            "</div>" +
                            "<span class='type'>Pièce unique</span>" +
                            "<h5>" + data[i].name + "</h5>" +
                            "<div class='trait'> </div>" +
                            "<span class='price'>" + data[i].price + "€ </span>" +
                            
                            "<form action='{{ path('app_cart_add', { 'idProduct':" + data[i].id + "}) }}' method='POST'>" +
                                "<input type='submit' class='btn-add' value='Ajouter au panier'>" +
                            "</form>" +
                        "</div>" +
                    "</a>";
                }

                $('#list-products').html(listProducts);

                console.log(data); 
            
            } catch (error) {
                console.error("Il y a eu une erreur avec la requête fetch: ", error.message);
            }
        }
  
        let filter = $(this).find(":selected").val();
        if (filter) {
            fetchData(filter);
        }

        fetchData(filter);
    });

});
 */

////  CODE POUR AFFICHER LES PRODUITS PETIT A PETIT
    let lastProductId = 0; // initialiser lastProductId
    let lastProductPrice = 0;
    let allProductsLoaded = false; 

    /// Fonction qui crée les cards à afficher
    function createProductElement(product) {

        const productElement = document.createElement('div');
        productElement.classList.add('products-card');
        productElement.dataset.productId = product.id;
    
        // Ajoutez le contenu HTML du produit (nom, image, prix, etc.)
        productElement.innerHTML = `<a href="{{ path('app_product_show', ${product.id}) }}">            
                                        <div class="products-img-container">
                                            <img src="/uploads/products/${product.picture}" alt="${product.name}" title="${product.name}">
                                        </div>
                                        <span class="type">Pièce unique</span>
                                        <h5> ${product.name} </h5>
                                        <div class="trait"> </div>
                                        <span class="price"> ${product.price} € </span>  
                                        <form action="{{ path('app_cart_add', {'idProduct': ${product.id}}) }}" method="POST">
                                            <input type="submit" class="btn-add" value="Ajouter au panier">
                                        </form>
                                    </a>`;    
    
        return productElement;
    }
    
    // Fonction qui récupère l'id du dernier produit affiché et récupère les infos des produits à l'id supérieur au dernier produit
    function loadProducts(filter) {

        if (allProductsLoaded) {
            return;
        }

        const lastProductElement = document.querySelector('.products-card:last-of-type');
        if (lastProductElement) {
            lastProductId = parseInt(lastProductElement.dataset.productId, 10);
            lastProductPrice = parseInt(lastProductElement.dataset.productPrice, 10);
        }

        console.log('last product element', lastProductElement) // renvoie bien le dernier produit
        console.log('last product id', lastProductId) // renvoie bien l'id du dernier produit
        console.log('last product price', lastProductPrice) // renvoie bien le prix du dernier produit

        const url =`/product/load-more/${encodeURIComponent(filter)}`; // l'url change en fonction du filtre récupéré

        const loadingMessage = document.querySelector('#loading-message');
        if (loadingMessage) {
            loadingMessage.style.display = 'block'; // pour que le message de chargement soit affiché uniquement pendant la requête
        }

        const formData = new FormData();
        formData.append('lastProductId', lastProductId);
        formData.append('lastProductPrice', lastProductPrice);

        // le fetch semble mauvais
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

            console.log('Données reçues depuis fetch :', data);

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

            //  le message de chargement disparait une fois la requête terminée
            if (loadingMessage) {
                loadingMessage.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des produits:', error);
        });
 
    }

    // fonction qui détecte le scroll en bas de page et éxécute la fonction pour charger les produits
    window.addEventListener('scroll', function() {
        if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight) {
            const currentFilter = $('.dropdown-toggle').attr('data-filter');
            loadProducts(currentFilter); // Charger initialement les produits avec le filtre appliqué s'il existe
        }
    });





  