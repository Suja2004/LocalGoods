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

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const showParam = urlParams.get('show');

    if (showParam === 'products') {
        homepage.style.display = 'none';
        productpage.style.display = 'block';
    }
});
