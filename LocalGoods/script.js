const home = document.getElementById('home');
const homepage = document.getElementById('home-page');
const product = document.getElementById('product');
const productpage = document.getElementById('product-page');

home.addEventListener('click', () => {
    homepage.style.display = 'block';
    productpage.style.display = 'none';
})
product.addEventListener('click', () => {
    homepage.style.display = 'none';
    productpage.style.display = 'block';
})

const subMenu = document.getElementById("subMenu");
function toggleMenu() {
    subMenu.classList.toggle("open-menu");
}

const cart = document.getElementById("cart");
let lastVisiblePage = 'home';

function toggleCart() {
    const homePages = document.getElementsByClassName('home-page-content');
    const productPages = document.getElementsByClassName('product-page-content');

    if (cart.classList.contains('show-cart')) {
        // Hide the cart and show the last visible page
        cart.classList.remove('show-cart');
        if (lastVisiblePage === 'home') {
            Array.from(homePages).forEach(page => {
                page.style.display = 'block';
            });
        } else if (lastVisiblePage === 'product') {
            Array.from(productPages).forEach(page => {
                page.style.display = 'block';
            });
        }
    } else {
        // Show the cart and hide all other pages
        cart.classList.add('show-cart');
        Array.from(homePages).forEach(page => {
            if (page.style.display === 'block') {
                lastVisiblePage = 'home';
            }
            page.style.display = 'none';
        });
        Array.from(productPages).forEach(page => {
            if (page.style.display === 'block') {
                lastVisiblePage = 'product';
            }
            page.style.display = 'none';
        });
    }
}

// Check query parameter on page load
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const showParam = urlParams.get('show');

    if (showParam === 'products') {
        homepage.style.display = 'none';
        productpage.style.display = 'block';
    }
});