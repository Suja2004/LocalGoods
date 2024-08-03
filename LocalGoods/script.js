const home = document.getElementById('home');
const homepage = document.getElementById('home-page');
const product = document.getElementById('product');
const productpage = document.getElementById('product-page');
const help = document.getElementById('help');
const helppage = document.getElementById('help-page');
const contact = document.getElementById('contact');
const contactpage = document.getElementById('contact-page');
const gocontact = document.getElementById('gocontact');



function showPage(pageToShow) {
    const pages = [homepage, productpage, helppage, contactpage];
    pages.forEach(page => {
        if (page === pageToShow) {
            page.style.display = 'block';
        } else {
            page.style.display = 'none';
        }
    });
}

gocontact.addEventListener('click', () => showPage(contactpage));
contact.addEventListener('click', () => showPage(contactpage));
home.addEventListener('click', () => showPage(homepage));
product.addEventListener('click', () => showPage(productpage));
help.addEventListener('click', () => showPage(helppage));
showPage(homepage);


const subMenu = document.getElementById("subMenu");
function toggleMenu() {
    subMenu.classList.toggle("open-menu");
}

function redirectToHomepage() {
    window.location.href = 'home.php'; 
}

// Check if the page was reloaded
const navigationEntries = performance.getEntriesByType('navigation');
if (navigationEntries.length > 0) {
    const navigationEntry = navigationEntries[0];
    if (navigationEntry.type === 'reload') {
        redirectToHomepage();
    }
}

const cart = document.getElementById("cart");
let lastVisiblePage = 'home';
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const showParam = urlParams.get('show');

    

    homepage.style.display = 'none';
    productpage.style.display = 'none';
    helppage.style.display = 'none';
    contactpage.style.display = 'none';

    switch (showParam) {
        case 'products':
            productpage.style.display = 'block';
            break;
        case 'help':
            helppage.style.display = 'block';
            break;
        case 'contact':
            contactpage.style.display = 'block';
            break;
        default:
            homepage.style.display = 'block'; 
            break;
    }
});

function enableEditing() {
    const formElements = document.querySelectorAll('#profile-form input, #profile-form textarea');
    formElements.forEach(element => element.removeAttribute('readonly'));
    const emailInput = document.getElementById("email");
    const usernameInput = document.getElementById("username");
    usernameInput.addEventListener("input", (event) => {
        event.preventDefault();
    });
    emailInput.addEventListener("input", (event) => {
        event.preventDefault();
    });

    document.getElementById('account').style.display = 'none';
    document.getElementById('save-button').style.display = 'inline';
    document.getElementById('cancel-button').style.display = 'inline';
}

function cancelUpdate() {
    const formElements = document.querySelectorAll('#profile-form input, #profile-form textarea');
    formElements.forEach(element => element.setAttribute('readonly', true));
    document.getElementById('account').style.display = 'flex';
    document.getElementById('save-button').style.display = 'none';
    document.getElementById('cancel-button').style.display = 'none';
}

function showLogoutPopup() {
    document.getElementById("logout-popup").style.display = "block";
}

function hideLogoutPopup() {
    document.getElementById("logout-popup").style.display = "none";
}

function showDeletePopup() {
    document.getElementById("delete-popup").style.display = "block";
}

function hideDeletePopup() {
    document.getElementById("delete-popup").style.display = "none";
}

function showPopup(message) {
    document.getElementById("popup-message").innerText = message;
    document.getElementById("popup").style.display = "block";
}

function hidePopup() {
    document.getElementById("popup").style.display = "none";
}

$(document).ready(function () {
    $('.categories').on('click', function () {
        var category = $(this).data('category');
        $.ajax({
            url: 'load_products.php',
            type: 'POST',
            data: {
                category: category
            },
            success: function (response) {
                $('.contentload').html(response);
                $('.details-btn').on('click', function () {
                    var productId = $(this).closest('.card').data('id');
                    window.location.href = 'product_details.php?id=' + productId;
                });
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error: ' + status + error);
            }
        });
    });

    document.querySelectorAll('.details-btn').forEach(item => {
        item.addEventListener('click', () => {
            const productId = item.closest('.home-items').getAttribute('data-id');
            window.location.href = `product_details.php?id=${productId}`;
        });
    });

    document.getElementById('searchInput').addEventListener('input', function () {
        let query = this.value;
        if (query.length > 1) {
            fetchSuggestions(query);
        } else {
            document.getElementById('suggestions').innerHTML = '';
        }
    });

    document.getElementById('searchInput').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            window.location.href = 'search_results.php?search=' + encodeURIComponent(this.value);
        }
    });

    function fetchSuggestions(query) {
        fetch('search_suggestions.php?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                let suggestions = document.getElementById('suggestions');
                suggestions.innerHTML = '';
                data.forEach(item => {
                    let div = document.createElement('div');
                    div.textContent = item.product_name;
                    div.addEventListener('click', () => {
                        window.location.href = 'search_results.php?search=' + encodeURIComponent(item.product_name);
                    });
                    suggestions.appendChild(div);
                });
            });
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const faqs = document.querySelectorAll('.faq h3');
    
    faqs.forEach(faq => {
        faq.addEventListener('click', () => {
            const content = faq.nextElementSibling;

            if (content.style.display === 'block') {
                content.style.display = 'none';
            } else {
                content.style.display = 'block';
            }
        });
    });
});