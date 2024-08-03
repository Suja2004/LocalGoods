const showProfile = document.getElementById("showProfile");
const profilepage = document.getElementById("profile-page");

showProfile.addEventListener("click", () => {
    if (profilepage.classList.contains("show")) {
        profilepage.classList.remove("show");
    } else {
        profilepage.classList.add("show");
    }
});

// script.js

document.addEventListener('DOMContentLoaded', function() {
    const toggleOrders = document.getElementById('toggle-orders');
    const ordersSection = document.getElementById('orders-section');
    const closeOrders = document.getElementById('close-orders');
    const ordersList = document.getElementById('orders-list');

    // Toggle sliding section
    toggleOrders.addEventListener('click', function(e) {
        e.preventDefault();
        ordersSection.classList.toggle('open');

        // Load order details dynamically
        if (ordersSection.classList.contains('open')) {
            loadOrderDetails();
        }
    });

    closeOrders.addEventListener('click', function() {
        ordersSection.classList.remove('open');
    });

    function loadOrderDetails() {
        fetch('fetch_order_details.php')
            .then(response => response.text())
            .then(data => {
                ordersList.innerHTML = data;
            })
            .catch(error => {
                console.error('Error loading order details:', error);
            });
    }
});


function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order?')) {
        fetch('cancel_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ 'order_id': orderId })
        })
        .then(response => response.text())
        .then(result => {
            alert(result);
            loadOrderDetails();
        })
        .catch(error => {
            console.error('Error cancelling order:', error);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('theme-toggle');

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
        });
    }
});
