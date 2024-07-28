const home = document.getElementById('home');
const homepage = document.getElementById('home-page');
const product = document.getElementById('product');
const productpage = document.getElementById('product-page');

// Handle navigation clicks
home.addEventListener('click', () => {
    homepage.style.display = 'block';
    productpage.style.display = 'none';
});

product.addEventListener('click', () => {
    homepage.style.display = 'none';
    productpage.style.display = 'block';
});

// Check query parameter on page load
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const showParam = urlParams.get('show');

    if (showParam === 'products') {
        homepage.style.display = 'none';
        productpage.style.display = 'block';
    }
});
