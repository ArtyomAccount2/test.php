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

    let showToast = (message, type = 'info') => {
        let toastContainer = document.getElementById('toastContainer');

        if (!toastContainer) 
        {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.style.position = 'fixed';
            toastContainer.style.bottom = '20px';
            toastContainer.style.right = '20px';
            toastContainer.style.zIndex = '9999';
            toastContainer.style.maxWidth = '350px';
            document.body.appendChild(toastContainer);
        }

        let bgClass = type === 'success' ? 'bg-success' : type === 'danger' ? 'bg-danger' : type === 'warning' ? 'bg-warning' : 'bg-info';
        let textClass = type === 'warning' ? 'text-dark' : 'text-white';

        let toast = document.createElement('div');
        toast.className = `toast align-items-center ${bgClass} ${textClass} border-0 mb-2`;
        toast.role = 'alert';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : type === 'danger' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        let bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', function() 
        {
            this.remove();
        });
    };

    let handleNotifications = () => {
        document.querySelectorAll('.notification-item .btn-outline-success').forEach(button => {
            button.addEventListener('click', function(e) 
            {
                e.preventDefault();
                let form = this.closest('form');

                if (!form) 
                {
                    return;
                }

                let notificationId = form.querySelector('input[name="notification_id"]')?.value;

                if (!notificationId) 
                {
                    return;
                }

                let notificationItem = this.closest('.notification-item');
                markNotificationRead(notificationId, notificationItem);
            });
        });

        document.querySelectorAll('.notification-item .btn-outline-danger').forEach(button => {
            button.addEventListener('click', function(e) 
            {
                e.preventDefault();

                if (!confirm('Удалить уведомление?')) 
                {
                    return;
                }

                let form = this.closest('form');

                if (!form) 
                {
                    return;
                }

                let notificationId = form.querySelector('input[name="notification_id"]')?.value;

                if (!notificationId) 
                {
                    return;
                }

                let notificationItem = this.closest('.notification-item');
                deleteNotification(notificationId, notificationItem);
            });
        });
    };

    let markNotificationRead = async (notificationId, notificationItem) => {
        try 
        {
            let formData = new URLSearchParams();
            formData.append('mark_notification_read_ajax', '1');
            formData.append('notification_id', notificationId);

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
                notificationItem.classList.remove('alert-info');
                notificationItem.classList.add('bg-light');
                notificationItem.dataset.read = '1';
                let markBtn = notificationItem.querySelector('.btn-outline-success');

                if (markBtn) 
                {
                    markBtn.closest('form')?.remove();
                }
                
                updateNotificationBadge();
                showToast('Уведомление отмечено как прочитанное', 'success');
            }
        } 
        catch (error) 
        {
            console.error('Ошибка:', error);
            showToast('Ошибка при обновлении', 'danger');
        }
    };

    let deleteNotification = async (notificationId, notificationItem) => {
        try 
        {
            let formData = new URLSearchParams();
            formData.append('delete_notification_ajax', '1');
            formData.append('notification_id', notificationId);

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
                notificationItem.style.transition = 'all 0.3s ease';
                notificationItem.style.opacity = '0';
                notificationItem.style.transform = 'translateX(-100px)';
                
                setTimeout(() => {
                    notificationItem.remove();
                    updateNotificationBadge();
                    
                    let remaining = document.querySelectorAll('.notification-item');

                    if (remaining.length === 0) 
                    {
                        let container = document.querySelector('#notificationsContainer');

                        if (container) 
                        {
                            container.innerHTML = `
                                <div class="text-center py-5" id="noNotifications">
                                    <i class="bi bi-bell-slash display-1 text-muted mb-3"></i>
                                    <h5>Уведомлений пока нет</h5>
                                    <p class="text-muted">Здесь будут появляться ваши уведомления</p>
                                </div>
                            `;
                        }
                    }

                    showToast('Уведомление удалено', 'success');
                }, 300);
            }
        } 
        catch (error) 
        {
            console.error('Ошибка:', error);
            showToast('Ошибка при удалении', 'danger');
        }
    };

    let updateNotificationBadge = () => {
        let unreadItems = document.querySelectorAll('.notification-item[data-read="0"]');
        let count = unreadItems.length;
        let badge = document.querySelector('a[href="#notifications"] .badge');
        let link = document.querySelector('a[href="#notifications"]');
        
        if (count > 0) 
        {
            if (badge) 
            {
                badge.textContent = count;
            } 
            else if (link) 
            {
                let newBadge = document.createElement('span');
                newBadge.className = 'badge bg-warning float-end';
                newBadge.textContent = count;
                link.appendChild(newBadge);
            }
        } 
        else 
        {
            if (badge) 
            {
                badge.remove();
            }
        }
    };

    let initWishlist = () => {
        document.querySelectorAll('.wishlist-item .btn-outline-danger').forEach(button => {
            button.addEventListener('click', function(e) 
            {
                e.preventDefault();
                
                if (!confirm('Удалить товар из избранного?')) 
                {
                    return;
                }
                
                let form = this.closest('form');

                if (!form) 
                {
                    return;
                }

                let wishlistId = form.querySelector('input[name="wishlist_id"]')?.value;

                if (!wishlistId) 
                {
                    return;
                }

                let wishlistItem = this.closest('.wishlist-item');

                removeFromWishlist(wishlistId, wishlistItem);
            });
        });
    };

    let removeFromWishlist = async (wishlistId, wishlistItem) => {
        try 
        {
            let formData = new URLSearchParams();
            formData.append('remove_from_wishlist_ajax', '1');
            formData.append('wishlist_id', wishlistId);

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
                wishlistItem.style.transition = 'all 0.3s ease';
                wishlistItem.style.opacity = '0';
                wishlistItem.style.transform = 'translateX(100px)';
                
                setTimeout(() => {
                    wishlistItem.remove();
                    updateWishlistBadge();
                    
                    let remaining = document.querySelectorAll('.wishlist-item');

                    if (remaining.length === 0) 
                    {
                        let container = document.querySelector('#wishlist .card-body');

                        if (container) 
                        {
                            container.innerHTML = `
                                <div class="text-center py-5">
                                    <i class="bi bi-heart display-1 text-muted mb-3"></i>
                                    <h5>Список избранного пуст</h5>
                                    <p class="text-muted">Добавляйте товары кнопкой ❤️ в каталоге</p>
                                    <a href="includes/assortment.php" class="btn btn-primary mt-2">
                                        Перейти в каталог
                                    </a>
                                </div>
                            `;
                        }
                    }

                    showToast('Удалено из избранного', 'success');
                }, 300);
            }
        } 
        catch (error) 
        {
            console.error('Ошибка:', error);
            showToast('Ошибка при удалении', 'danger');
        }
    };

    let updateWishlistBadge = () => {
        let items = document.querySelectorAll('.wishlist-item');
        let count = items.length;
        let badge = document.querySelector('a[href="#wishlist"] .badge');
        let link = document.querySelector('a[href="#wishlist"]');
        
        if (count > 0) 
        {
            if (badge) 
            {
                badge.textContent = count;
            } 
            else if (link) 
            {
                let newBadge = document.createElement('span');
                newBadge.className = 'badge bg-primary float-end';
                newBadge.textContent = count;
                link.appendChild(newBadge);
            }
        } 
        else 
        {
            if (badge) 
            {
                badge.remove();
            }
        }
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

            if (cartLink) 
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
                showToast('Корзина очищена', 'success');
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