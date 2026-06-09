document.addEventListener('DOMContentLoaded', function() 
{
    async function updateCartQuantity(itemId, newQuantity) 
    {
        try 
        {
            let formData = new FormData();
            formData.append('ajax_update', '1');
            formData.append('item_id', itemId);
            formData.append('quantity', newQuantity);
            
            let response = await fetch('cart.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            if (!response.ok) 
            {
                throw new Error('Ошибка сервера');
            }
            
            let result = await response.json();
            
            if (result.success) 
            {
                updateCartDisplay(itemId, result);
            }
        } 
        catch (error) 
        {
            console.error('Ошибка:', error);
            let row = document.querySelector(`tr[data-item-id="${itemId}"]`);

            if (row) 
            {
                let input = row.querySelector('.quantity-input');
                let oldQuantity = input.getAttribute('data-old-value');

                if (oldQuantity) 
                {
                    input.value = oldQuantity;
                }
            }
        }
    }
    
    async function removeCartItem(itemId) 
    {
        if (!confirm('Удалить товар из корзины?')) 
        {
            return false;
        }
        
        try 
        {
            let formData = new FormData();
            formData.append('ajax_remove', '1');
            formData.append('item_id', itemId);
            
            let response = await fetch('cart.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            if (!response.ok) 
            {
                throw new Error('Ошибка сервера');
            }
            
            let result = await response.json();
            
            if (result.success) 
            {
                let row = document.querySelector(`tr[data-item-id="${itemId}"]`);

                if (row) 
                {
                    row.remove();
                }

                updateTotalDisplay(result.cart_total, result.cart_count);
                checkEmptyCart();
            }
        } 
        catch (error) 
        {
            console.error('Ошибка:', error);
        }
    }

    async function clearCart() 
    {
        if (!confirm('Вы уверены, что хотите очистить всю корзину?')) 
        {
            return false;
        }
        
        try 
        {
            let formData = new FormData();
            formData.append('ajax_clear', '1');
            
            let response = await fetch('cart.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            if (!response.ok) 
            {
                throw new Error('Ошибка сервера');
            }
            
            let result = await response.json();
            
            if (result.success) 
            {
                location.reload();
            }
        } 
        catch (error) 
        {
            console.error('Ошибка:', error);
        }
    }

    function updateCartDisplay(itemId, result) 
    {
        let row = document.querySelector(`tr[data-item-id="${itemId}"]`);

        if (row) 
        {
            let quantityInput = row.querySelector('.quantity-input');
            let itemTotalSpan = row.querySelector('.cart-item-total');
            
            if (quantityInput && parseInt(quantityInput.value) !== result.quantity) 
            {
                quantityInput.value = result.quantity;
            }
            
            if (itemTotalSpan) 
            {
                let newTotal = result.price * result.quantity;
                itemTotalSpan.textContent = formatNumber(newTotal) + ' ₽';
            }
        }
        
        updateTotalDisplay(result.cart_total, result.cart_count);
    }

    function updateTotalDisplay(total, count) 
    {
        let cartTotalSpan = document.getElementById('cart-total');
        let cartGrandTotalSpan = document.getElementById('cart-grand-total');
        let cartCountSpan = document.getElementById('cart-count');
        let cartBadge = document.getElementById('cart-badge');
        
        if (cartTotalSpan) 
        {
            cartTotalSpan.textContent = formatNumber(total) + ' ₽';
        }

        if (cartGrandTotalSpan) 
        {
            cartGrandTotalSpan.textContent = formatNumber(total) + ' ₽';
        }

        if (cartCountSpan) 
        {
            cartCountSpan.textContent = count;
        }

        if (cartBadge) 
        {
            cartBadge.textContent = count + ' товар(ов)';
        }
    }

    function formatNumber(num) 
    {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    function checkEmptyCart() 
    {
        let tbody = document.getElementById('cart-items-body');

        if (tbody && tbody.children.length === 0) 
        {
            location.reload();
        }
    }

    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('focus', function() {
            this.setAttribute('data-old-value', this.value);
        });
    });

    document.querySelectorAll('.minus-btn').forEach(button => {
        button.addEventListener('click', function() 
        {
            let itemId = this.dataset.id;
            let input = document.querySelector(`.quantity-input[data-id="${itemId}"]`);

            if (input) 
            {
                let currentValue = parseInt(input.value);

                if (currentValue > 1) 
                {
                    let newValue = currentValue - 1;
                    input.value = newValue;
                    updateCartQuantity(itemId, newValue);
                }
            }
        });
    });
    
    document.querySelectorAll('.plus-btn').forEach(button => {
        button.addEventListener('click', function() 
        {
            let itemId = this.dataset.id;
            let input = document.querySelector(`.quantity-input[data-id="${itemId}"]`);

            if (input) 
            {
                let currentValue = parseInt(input.value);

                if (currentValue < 99) 
                {
                    let newValue = currentValue + 1;
                    input.value = newValue;
                    updateCartQuantity(itemId, newValue);
                }
            }
        });
    });
    
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() 
        {
            let itemId = this.dataset.id;
            let value = parseInt(this.value);
            
            if (isNaN(value) || value < 1) 
            {
                value = 1;
            } 
            else if (value > 99) 
            {
                value = 99;
            }
            
            if (value !== parseInt(this.value)) 
            {
                this.value = value;
            }
            
            updateCartQuantity(itemId, value);
        });
        
        input.addEventListener('blur', function() 
        {
            let value = parseInt(this.value);

            if (isNaN(value) || value < 1) 
            {
                value = 1;
                this.value = value;
                let itemId = this.dataset.id;
                updateCartQuantity(itemId, value);
            }
        });
    });
    
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() 
        {
            let itemId = this.dataset.id;
            removeCartItem(itemId);
        });
    });
    
    let clearCartBtn = document.getElementById('clear-cart-btn');

    if (clearCartBtn) 
    {
        clearCartBtn.addEventListener('click', function(e) 
        {
            e.preventDefault();
            clearCart();
        });
    }

    let phoneInput = document.getElementById('phone');

    if (phoneInput) 
    {
        phoneInput.addEventListener('input', function(e) 
        {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);

            if (x) 
            {
                e.target.value = !x[2] ? x[1] : '+7 (' + x[2] + (x[3] ? ') ' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
            }
        });
    }

    let checkoutForm = document.getElementById('checkout-form');

    if (checkoutForm) 
    {
        checkoutForm.addEventListener('submit', function(e) 
        {
            let phoneInput = this.querySelector('#phone');

            if (phoneInput && phoneInput.value.replace(/\D/g, '').length < 11) 
            {
                e.preventDefault();
                alert('Пожалуйста, введите корректный номер телефона');
                phoneInput.focus();
            }
        });
    }
});