const home = document.getElementById('home');
const homepage = document.getElementById('home-page');
const product = document.getElementById('product');
const productpage = document.getElementById('product-page');
const orders = document.getElementById('orders');
const orderspage = document.getElementById('orders-page');
const messages = document.getElementById('messages');
const messagespage = document.getElementById('messages-page');

home.addEventListener('click', () => {
    homepage.style.display = 'block';
    productpage.style.display = 'none';
    orderspage.style.display = 'none';
    messagespage.style.display = 'none';
});

orders.addEventListener('click', () => {
    orderspage.style.display = 'block';
    homepage.style.display = 'none';
    productpage.style.display = 'none';
    messagespage.style.display = 'none';
});

product.addEventListener('click', () => {
    homepage.style.display = 'none';
    productpage.style.display = 'block';
    orderspage.style.display = 'none';
    messagespage.style.display = 'none';
});

messages.addEventListener('click', () => {
    messagespage.style.display = 'block';
    homepage.style.display = 'none';
    productpage.style.display = 'none';
    orderspage.style.display = 'none';
});

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const showParam = urlParams.get('show');

    if (showParam === 'product') {
        homepage.style.display = 'none';
        productpage.style.display = 'block';
    }
});

function showResponseorderForm(orderId, email) {
    document.getElementById('response-order-id').value = orderId;
    document.getElementById('order-response-form').style.display = 'block';
}

function hideResponseorderForm() {
    document.getElementById('order-response-form').style.display = 'none';
}


function showResponseForm(id, email, subject) {
    document.getElementById('response-id').value = id;
    document.getElementById('reply_subject').value = 'Re: ' + subject;
    document.getElementById('response-form').style.display = 'block';
}

function hideResponseForm() {
    document.getElementById('response-form').style.display = 'none';
}
