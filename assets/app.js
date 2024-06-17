
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

        //ici je veux faire une requête asynchrone

        // Fonction pour effectuer la requête asynchrone
    async function fetchData(filter) {

        try {
        // Construit l'URL avec le filtre
        const url = `/product/filter/${filter}`;
    
        // Exécute la requête asynchrone
        const response = await fetch(url, {
            method: 'GET', // Méthode HTTP
            headers: {
            'Content-Type': 'application/json', // Type Mime de contenu attendu de la réponse
            },
        });
    
        // Vérifie si la requête a réussi
        if (!response.ok) {
            throw new Error(`Erreur: ${response.status}`); // Lance une exception si la réponse est une erreur
        }
    
        // Extrait les données JSON de la réponse
        const data = await response.json();

        let listProducts = "";

        for(let i = 0; i < data.length; i++) {

            listProducts += "<a href='{{path('app_product_show', { id : " + data[i].id + " })}}'>" +
                "<div class='d-flex article p-3'>" +

                    // "<img class='col-md-4' src='{{ asset('/uploads/articles/default.jpg') }}' alt='" + data[i].title  + "' title='" + data[i].title  + "'>" +
                    "<img class='col-md-4' src='/uploads/products/" + data[i].picture  + "' alt='" + data[i].name  + "' title='" + data[i].name  + "'>" +

                    "<div class='col-md-8 d-flex flex-column ms-3'>" +
                        "<h3>" +
                            data[i].name +
                        "</h3>" +
                        "<p>" +
                        data[i].text +
                        "</p>" +
                    "</div>" +
                "</div>" +
            "</a>";
        }

        $('#list-products').html(listProducts);

        // Ici, vous pouvez traiter les données JSON retournées
        console.log(data); // Affiche les données dans la console pour le debug
    
        // Pour afficher les données sur votre page, vous devez décider comment
        // vous souhaitez les afficher et mettre à jour le DOM en conséquence.
        // Par exemple, si vous avez un élément avec l'id 'dataContainer' :
        // const container = document.getElementById('dataContainer');
        // container.textContent = JSON.stringify(data, null, 2); // Convertit les données JSON en chaîne et les affiche
        } catch (error) {
            console.error("Il y a eu une erreur avec la requête fetch: ", error.message);
        }
    }
  
        let filter = $(this).find(":selected").val();
        if (filter) {
            fetchData(filter);
        }
        // Appel de la fonction avec le filtre désiré, par exemple 'monFiltre'
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