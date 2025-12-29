document.addEventListener('DOMContentLoaded', function() 
{
    let minusButtons = document.querySelectorAll('.minus-btn');
    let plusButtons = document.querySelectorAll('.plus-btn');
    let quantityInputs = document.querySelectorAll('.quantity-input');
    
    minusButtons.forEach((button, index) => {
        button.addEventListener('click', function() 
        {
            let input = quantityInputs[index];
            let currentValue = parseInt(input.value);

            if (currentValue > 1) 
            {
                input.value = currentValue - 1;
                updateCartItem(this);
            }
        });
    });
    
    plusButtons.forEach((button, index) => {
        button.addEventListener('click', function() 
        {
            let input = quantityInputs[index];
            let currentValue = parseInt(input.value);

            if (currentValue < 99) 
            {
                input.value = currentValue + 1;
                updateCartItem(this);
            }
        });
    });
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() 
        {
            updateCartItem(this);
        });
        
        input.addEventListener('blur', function() 
        {
            let value = parseInt(this.value);

            if (isNaN(value) || value < 1) 
            {
                this.value = 1;
            } 
            else if (value > 99) 
            {
                this.value = 99;
            }

            updateCartItem(this);
        });
    });
    
    function updateCartItem(element) 
    {
        let form = element.closest('form');
        let updateButton = form.querySelector('[name="update_cart"]');

        if (updateButton) 
        {
            updateButton.style.display = 'inline-block';

            setTimeout(() => {
                if (updateButton.style.display === 'inline-block') 
                {
                    form.submit();
                }
            }, 2000);
        }
    }

    let phoneInput = document.getElementById('phone');

    if (phoneInput) 
    {
        phoneInput.addEventListener('input', function(e) 
        {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
            e.target.value = !x[2] ? x[1] : '+7 (' + x[2] + (x[3] ? ') ' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
        });
    }

    let clearCartButton = document.querySelector('[name="clear_cart"]');

    if (clearCartButton) 
    {
        clearCartButton.addEventListener('click', function(e) 
        {
            if (!confirm('Вы уверены, что хотите очистить всю корзину?')) 
            {
                e.preventDefault();
            }
        });
    }

    let urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('added')) 
    {
        showNotification('Товар добавлен в корзину!', 'success');
    }
    
    function showNotification(message, type) 
    {
        let alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.zIndex = '1050';
        alert.style.minWidth = '300px';
        alert.innerHTML = `
            <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
    
    let checkoutForm = document.querySelector('form[action*="checkout"]');

    if (checkoutForm) 
    {
        checkoutForm.addEventListener('submit', function(e) 
        {
            let phoneInput = this.querySelector('#phone');
            
            if (phoneInput && phoneInput.value.replace(/\D/g, '').length < 11) 
            {
                e.preventDefault();
                showNotification('Пожалуйста, введите корректный номер телефона', 'danger');
                phoneInput.focus();
            }
        });
    }
});