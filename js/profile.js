document.addEventListener('DOMContentLoaded', function() 
{
    let initTools = () => {
        let tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    };

    let animateStatistics = () => {
        let statNumbers = document.querySelectorAll('.stat-number');

        statNumbers.forEach(number => {
            let originalText = number.textContent;
            let targetValue;
            
            if (originalText.includes('₽')) 
            {
                targetValue = parseInt(originalText.replace(/\s|₽/g, '')) || 0;
            } 
            else 
            {
                targetValue = parseInt(originalText) || 0;
            }

            let current = 0;
            let increment = Math.max(1, targetValue / 30);

            let timer = setInterval(() => {
                current += increment;

                if (current >= targetValue) 
                {
                    number.textContent = originalText;
                    clearInterval(timer);
                } 
                else 
                {
                    if (originalText.includes('₽')) 
                    {
                        number.textContent = Math.round(current).toLocaleString('ru-RU') + ' ₽';
                    } 
                    else 
                    {
                        number.textContent = Math.round(current);
                    }
                }
            }, 50);
        });
    };

    let handleForms = () => {
        let profileForm = document.getElementById('profileForm');

        if (profileForm) 
        {
            profileForm.addEventListener('submit', function() 
            {
                let submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Сохранение...';
                submitBtn.disabled = true;
            });
        }
    };

    let handleTabs = () => {
        let tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');

        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                tabLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                let target = document.querySelector(this.getAttribute('href'));

                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                target.classList.add('show', 'active');

                setTimeout(() => {
                    animateTabContent(target.id);
                }, 100);
            });
        });
    };

    let animateTabContent = (tabId) => {
        let elements = {
            'profile': '.stat-card, .activity-item',
            'orders': 'table tr',
            'cart': 'table tr',
            'wishlist': '.wishlist-item',
            'notifications': '.notification-item'
        };

        let selectors = elements[tabId];

        if (selectors) 
        {
            let items = document.querySelectorAll(`${selectors}`);
            items.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    item.style.transition = 'all 0.4s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });
        }
    };

    let handleNotifications = () => {
        let notificationButtons = document.querySelectorAll('.notification-item .btn');

        notificationButtons.forEach(button => {
            button.addEventListener('click', function() 
            {
                let notification = this.closest('.notification-item');
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(-100px)';

                setTimeout(() => {
                    notification.remove();
                    let badge = document.querySelector('a[href="#notifications"] .badge');

                    if (badge) 
                    {
                        let count = parseInt(badge.textContent);

                        if (count > 1) 
                        {
                            badge.textContent = count - 1;
                        } 
                        else 
                        {
                            badge.remove();
                        }
                    }

                    let notifications = document.querySelectorAll('.notification-item');

                    if (notifications.length === 0) 
                    {
                        let notificationList = document.querySelector('.notification-list');
                        notificationList.innerHTML = `
                            <div class="text-center py-5">
                                <i class="bi bi-bell-slash display-1 text-muted"></i>
                                <p class="text-muted mt-3">Нет непрочитанных уведомлений</p>
                            </div>
                        `;
                    }
                }, 300);
            });
        });
    };

    let initWishlist = () => {
        let wishlistItems = document.querySelectorAll('.wishlist-item');

        wishlistItems.forEach(item => {
            let removeBtn = item.querySelector('.btn-remove-wishlist');
            if (removeBtn) 
            {
                removeBtn.addEventListener('click', function() 
                {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(100px)';

                    setTimeout(() => {
                        item.remove();
                        let badge = document.querySelector('a[href="#wishlist"] .badge');

                        if (badge) 
                        {
                            let count = parseInt(badge.textContent);

                            if (count > 1) 
                            {
                                badge.textContent = count - 1;
                            } 
                            else 
                            {
                                badge.remove();
                            }
                        }

                        let wishlistItems = document.querySelectorAll('.wishlist-item');

                        if (wishlistItems.length === 0) 
                        {
                            let wishlistContainer = document.querySelector('#wishlist .card-body');
                            wishlistContainer.innerHTML = `
                                <div class="text-center py-5">
                                    <i class="bi bi-heart display-1 text-muted"></i>
                                    <p class="text-muted mt-3">В избранном пока нет товаров</p>
                                    <a href="includes/assortment.php" class="btn btn-primary mt-2">Перейти в каталог</a>
                                </div>
                            `;
                        }
                    }, 300);
                });
            }
        });
    };

    let updateCartUI = (data) => {
        if (!data.items) 
        {
            return;
        }

        data.items.forEach(item => {
            let row = document.querySelector(`.cart-item-row[data-item-id="${item.id}"]`);

            if (row) 
            {
                let quantityInput = row.querySelector('.cart-quantity-input');

                if (quantityInput) 
                {
                    quantityInput.value = item.quantity;
                }

                let totalSpan = document.querySelector(`.cart-item-total[data-item-id="${item.id}"]`);

                if (totalSpan) 
                {
                    totalSpan.textContent = item.total_formatted;
                }
            }
        });

        let totalDisplay = document.querySelector('.cart-total-display');
        let countDisplay = document.querySelector('.cart-count-display');
        let cartBadge = document.querySelector('.cart-badge');

        if (totalDisplay) 
        {
            totalDisplay.textContent = `Итого: ${data.cart_total_formatted}`;
        }

        if (countDisplay) 
        {
            countDisplay.textContent = `Товаров: ${data.cart_count} шт.`;
        }

        if (cartBadge) 
        {
            cartBadge.textContent = `${data.cart_count} товар(ов)`;
        }

        let sidebarCartBadge = document.querySelector('a[href="#cart"] .badge');

        if (sidebarCartBadge) 
        {
            if (data.cart_count > 0) 
            {
                sidebarCartBadge.textContent = data.cart_count;
            } 
            else 
            {
                sidebarCartBadge.remove();
            }
        } 
        else if (data.cart_count > 0) 
        {
            let cartLink = document.querySelector('a[href="#cart"]');

            if (cartLink && !cartLink.querySelector('.badge')) 
            {
                let badge = document.createElement('span');
                badge.className = 'badge bg-danger float-end';
                badge.textContent = data.cart_count;
                cartLink.appendChild(badge);
            }
        }
    };

    let showEmptyCart = () => {
        let cartBody = document.querySelector('#cart .card-body');

        if (cartBody) 
        {
            cartBody.innerHTML = `
                <div class="text-center py-5 flex-grow-1 d-flex flex-column justify-content-center empty-cart-message">
                    <i class="bi bi-cart display-1 text-muted mb-4"></i>
                    <h5 class="text-muted mb-3">Ваша корзина пуста</h5>
                    <p class="text-muted mb-4">Добавьте товары из каталога</p>
                    <a href="includes/assortment.php" class="btn btn-primary">
                        <i class="bi bi-arrow-right me-2"></i>Перейти в каталог
                    </a>
                </div>
            `;
        }

        let cartBadge = document.querySelector('.cart-badge');

        if (cartBadge) 
        {
            cartBadge.textContent = '0 товар(ов)';
        }

        let sidebarCartBadge = document.querySelector('a[href="#cart"] .badge');

        if (sidebarCartBadge) 
        {
            sidebarCartBadge.remove();
        }
    };

    let updateCartItem = async (itemId, quantity) => {
        try 
        {
            let formData = new URLSearchParams();
            formData.append('update_cart_item_ajax', '1');
            formData.append('item_id', itemId);
            formData.append('quantity', quantity);

            let response = await fetch('profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString()
            });

            let data = await response.json();

            if (data.success) 
            {
                updateCartUI(data);
            }
        } 
        catch (error) 
        {
            console.error('Ошибка:', error);
        }
    };

    let removeCartItem = async (itemId) => {
        if (!confirm('Удалить товар из корзины?')) 
        {
            return;
        }

        try 
        {
            let formData = new URLSearchParams();
            formData.append('remove_cart_item_ajax', '1');
            formData.append('item_id', itemId);

            let response = await fetch('profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString()
            });

            let data = await response.json();

            if (data.success) 
            {
                let row = document.querySelector(`.cart-item-row[data-item-id="${itemId}"]`);

                if (row) 
                {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(100px)';
                    setTimeout(() => row.remove(), 300);
                }

                if (data.cart_count === 0) 
                {
                    showEmptyCart();
                } 
                else 
                {
                    updateCartUI(data);
                }
            }
        } 
        catch (error) 
        {
            console.error('Ошибка:', error);
        }
    };

    let clearCart = async () => {
        if (!confirm('Очистить всю корзину?')) 
        {
            return;
        }

        try 
        {
            let formData = new URLSearchParams();
            formData.append('clear_cart_ajax', '1');

            let response = await fetch('profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString()
            });

            let data = await response.json();

            if (data.success) 
            {
                showEmptyCart();
            }
        } 
        catch (error) 
        {
            console.error('Ошибка:', error);
        }
    };

    let initCartControls = () => {
        document.addEventListener('click', async (e) => {
            let target = e.target;

            if (target.classList.contains('cart-plus-btn')) 
            {
                e.preventDefault();
                let itemId = target.getAttribute('data-item-id');
                let input = document.querySelector(`.cart-quantity-input[data-item-id="${itemId}"]`);

                if (input) 
                {
                    let newValue = parseInt(input.value) + 1;

                    if (newValue <= 99) 
                    {
                        input.value = newValue;
                        await updateCartItem(itemId, newValue);
                    }
                }
            }

            if (target.classList.contains('cart-minus-btn')) 
            {
                e.preventDefault();
                let itemId = target.getAttribute('data-item-id');
                let input = document.querySelector(`.cart-quantity-input[data-item-id="${itemId}"]`);

                if (input) 
                {
                    let newValue = parseInt(input.value) - 1;

                    if (newValue >= 1) 
                    {
                        input.value = newValue;
                        await updateCartItem(itemId, newValue);
                    } 
                    else if (newValue === 0) 
                    {
                        await removeCartItem(itemId);
                    }
                }
            }
            if (target.classList.contains('cart-remove-btn')) 
            {
                e.preventDefault();
                let itemId = target.getAttribute('data-item-id');
                await removeCartItem(itemId);
            }
            if (target.classList.contains('cart-clear-btn')) 
            {
                e.preventDefault();
                await clearCart();
            }
        });

        document.addEventListener('change', async (e) => {
            if (e.target.classList.contains('cart-quantity-input')) 
            {
                let input = e.target;
                let itemId = input.getAttribute('data-item-id');
                let value = parseInt(input.value);

                if (isNaN(value) || value < 1) 
                {
                    value = 1;
                    input.value = 1;
                }

                if (value > 99) 
                {
                    value = 99;
                    input.value = 99;
                }
                
                await updateCartItem(itemId, value);
            }
        });
    };

    let init = () => {
        initTools();
        animateStatistics();
        handleForms();
        handleTabs();
        handleNotifications();
        initWishlist();
        initCartControls();
        setTimeout(() => {
            animateTabContent('profile');
        }, 500);
    };

    init();
});