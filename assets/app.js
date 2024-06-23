
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

// NAV
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



//// REQUETE ASYNC TRIER PRODUITS PAR PRIX
$(document).ready(function() {
    
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



//// REQUETE ASYNC POUR LES CATEGORIES DE LA PAGE PRODUCT (A L'AIDE)
$(document).ready(function() {

    $("#products-categories a").click(function(event) {

        event.preventDefault(); 
  
        const id_category = parseInt($(this).attr('id').replace('category-link-', ''));
  
        fetchProductsByCategory(id_category);

            async function fetchProductsByCategory(id_category) {

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

        $("#products-categories a").removeClass("active"); // Supprimer la classe "active" de tous les liens
        $(`#products-categories a#${id_category}`).addClass("active"); // Ajouter la classe "active" au lien sélectionné   

        if (id_category) {
            fetchProductsByCategory(id_category);
        }


    });
  });
  
