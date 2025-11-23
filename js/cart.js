document.addEventListener('DOMContentLoaded', function () {
    // Function to handle Add to Cart
    const handleAddToCart = (button) => {
        const productId = button.dataset.id;
        let quantity = 1; // Default quantity

        // Check if there is a quantity input associated with the button
        const form = button.closest('form');
        if (form) {
            const quantityInput = form.querySelector('input[name="quantity"]');
            if (quantityInput) {
                quantity = quantityInput.value;
            }
        }

        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('id', productId);
        formData.append('quantity', quantity);

        fetch('modules/cart_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
                alert('Đã thêm sản phẩm vào giỏ hàng!');
            } else {
                alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi kết nối, vui lòng thử lại.');
        });
    };

    // Function to handle updating item quantity in the cart
    const handleUpdateQuantity = (input) => {
        const productId = input.dataset.id;
        const quantity = input.value;
        
        if (quantity < 1) {
            if (confirm("Bạn có muốn xóa sản phẩm này khỏi giỏ hàng không?")) {
                handleRemoveItem(productId, input.closest('tr'));
            } else {
                input.value = 1; // Revert to 1 if user cancels
            }
            return;
        }

        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('id', productId);
        formData.append('quantity', quantity);

        fetch('modules/cart_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartView(data, productId, quantity);
            } else {
                alert(data.message || 'Lỗi khi cập nhật giỏ hàng.');
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Function to handle removing an item from the cart
    const handleRemoveItem = (button) => {
        const productId = button.dataset.id;
        if (!confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) return;

        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('id', productId);

        fetch('modules/cart_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = button.closest('tr');
                row.remove();
                updateCartTotals(data);
                updateCartCount(data.cart_count);

                // If cart is now empty, show the empty message
                if (data.cart_count === 0) {
                    const cartContainer = document.querySelector('.container.py-5');
                    cartContainer.innerHTML = `
                        <h1 class="mb-4">Giỏ hàng của bạn</h1>
                        <div class="text-center">
                            <p class="lead">Giỏ hàng của bạn đang trống.</p>
                            <a href="order.php" class="btn btn-primary">Bắt đầu mua sắm</a>
                        </div>`;
                }
            } else {
                alert(data.message || 'Lỗi khi xóa sản phẩm.');
            }
        })
        .catch(error => console.error('Error:', error));
    };

    // Helper to update the cart count in the header
    const updateCartCount = (count) => {
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.innerText = count;
        }
    };
    
    // Helper to update total price and item subtotal
    const updateCartView = (data, productId, newQuantity) => {
        updateCartCount(data.cart_count);
    
        const row = document.querySelector(`tr[data-product-id="${productId}"]`);
        if (row) {
            const price = parseFloat(row.querySelector('.item-price').dataset.price);
            const subtotalElement = row.querySelector('.subtotal');
            const newSubtotal = price * newQuantity;
            subtotalElement.textContent = newSubtotal.toLocaleString('vi-VN') + ' ₫';
        }
    
        updateCartTotals(data);
    };

    // Helper to update the main cart totals section
    const updateCartTotals = (data) => {
        const totalElement = document.getElementById('cart-total-price');
        if (totalElement) {
            totalElement.textContent = data.total_price.toLocaleString('vi-VN') + ' ₫';
        }
    };
    
    // Debounce function to delay execution
    const debounce = (func, delay) => {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    };

    // Event Listeners
    document.addEventListener('click', function(event) {
        if (event.target.matches('.add-to-cart')) {
            handleAddToCart(event.target);
        }
        if (event.target.matches('.remove-item')) {
            handleRemoveItem(event.target);
        }
    });

    const quantityInputs = document.querySelectorAll('.quantity-input');
    const debouncedUpdate = debounce(handleUpdateQuantity, 500);
    quantityInputs.forEach(input => {
        input.addEventListener('input', () => debouncedUpdate(input));
    });
});
