
document.addEventListener('DOMContentLoaded', function() {
    const couponForm = document.getElementById('coupon-form');
    if(couponForm) {
        couponForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const couponInput = couponForm.querySelector('input[name="coupon"]');
            const couponCode = couponInput.value.trim();
            const total = document.querySelector('input[name="total"]').value;
    
            if (couponCode === '') {
                alert('Vui lòng nhập mã giảm giá.');
                return;
            }
    
            const formData = new FormData();
            formData.append('coupon', couponCode);
            formData.append('total', total);
    
            fetch('modules/apply_coupon.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    updateOrderSummary(data.new_total, data.applied_coupons);
                    couponInput.value = '';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            });
        });
    }

    function updateOrderSummary(newTotal, appliedCoupons) {
        const appliedCouponsList = document.getElementById('applied-coupons-list');
        const totalAmountElement = document.getElementById('total-amount');

        // Clear existing coupon list
        appliedCouponsList.innerHTML = '';

        if (appliedCoupons && appliedCoupons.length > 0) {
            appliedCoupons.forEach(coupon => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between text-success';
                
                const div = document.createElement('div');
                const h6 = document.createElement('h6');
                h6.className = 'my-0';
                h6.textContent = `Mã giảm giá: ${coupon.code}`;
                div.appendChild(h6);

                const span = document.createElement('span');
                span.className = 'text-success';
                span.textContent = `-${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(coupon.discount_amount)}`;

                li.appendChild(div);
                li.appendChild(span);
                appliedCouponsList.appendChild(li);
            });
        }

        totalAmountElement.textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(newTotal);
    }
});
