document.getElementById('add-to-cart-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    const messageDiv = document.getElementById('add-to-cart-message');

    fetch('modules/cart_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            // Optionally, update the cart count in the header
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }
        } else {
            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    })
    .catch(error => {
        messageDiv.innerHTML = `<div class="alert alert-danger">An error occurred. Please try again.</div>`;
        console.error('Error:', error);
    });
});
