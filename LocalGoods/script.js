const home = document.getElementById('home');
const homepage = document.getElementById('home-page');
const product = document.getElementById('product');
const productpage = document.getElementById('product-page');

home.addEventListener('click',()=>{
    homepage.style.display = 'block';
    productpage.style.display = 'none';
})
product.addEventListener('click',()=>{
    homepage.style.display = 'none';
    productpage.style.display = 'block';
})