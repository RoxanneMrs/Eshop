import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// NAV
const li = document.querySelectorAll("#nav li");

$('#nav li').hover(function() {
    $(this).addClass('highlight');
}, function() {
    $(this).removeClass('highlight');
});

$('li').click(function() {
    $('li.selected').find('a').css('color', 'rgb(124, 95, 138)');   
    $('li').removeClass('selected');
    $(this).addClass('selected');
    $(this).find('a').css('color', 'rgb(247, 244, 240)');
});

