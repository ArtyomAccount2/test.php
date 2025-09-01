document.addEventListener('DOMContentLoaded', function() {
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
            
            if (originalText.includes('₽')) {
                targetValue = parseInt(originalText.replace(/\s|₽/g, '')) || 0;
            } else {
                targetValue = parseInt(originalText) || 0;
            }

            let current = 0;
            let increment = Math.max(1, targetValue / 30);

            let timer = setInterval(() => {
                current += increment;
                if (current >= targetValue) {
                    number.textContent = originalText;
                    clearInterval(timer);
                } else {
                    if (originalText.includes('₽')) {
                        number.textContent = Math.round(current).toLocaleString('ru-RU') + ' ₽';
                    } else {
                        number.textContent = Math.round(current);
                    }
                }
            }, 50);
        });
    };

    let handleForms = () => {
        let profileForm = document.getElementById('profileForm');
        
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                let submitBtn = this.querySelector('button[type="submit"]');
                let originalText = submitBtn.innerHTML;
                
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
            'wishlist': '.wishlist-item',
            'notifications': '.notification-item'
        };

        let selectors = elements[tabId];

        if (selectors) {
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
            button.addEventListener('click', function() {
                let notification = this.closest('.notification-item');
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(-100px)';
                
                setTimeout(() => {
                    notification.remove();

                    let badge = document.querySelector('a[href="#notifications"] .badge');

                    if (badge) {
                        let count = parseInt(badge.textContent);
                        if (count > 1) {
                            badge.textContent = count - 1;
                        } else {
                            badge.remove();
                        }
                    }

                    let notifications = document.querySelectorAll('.notification-item');

                    if (notifications.length === 0) {
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

            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(100px)';
                    
                    setTimeout(() => {
                        item.remove();

                        let badge = document.querySelector('a[href="#wishlist"] .badge');
                        if (badge) {
                            let count = parseInt(badge.textContent);
                            if (count > 1) {
                                badge.textContent = count - 1;
                            } else {
                                badge.remove();
                            }
                        }

                        let wishlistItems = document.querySelectorAll('.wishlist-item');

                        if (wishlistItems.length === 0) {
                            let wishlistContainer = document.querySelector('#wishlist .card-body');
                            wishlistContainer.innerHTML = `
                                <div class="text-center py-5">
                                    <i class="bi bi-heart display-1 text-muted"></i>
                                    <p class="text-muted mt-3">В избранном пока нет товаров</p>
                                    <a href="assortment.php" class="btn btn-primary mt-2">Перейти в каталог</a>
                                </div>
                            `;
                        }
                    }, 300);
                });
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

        setTimeout(() => {
            animateTabContent('profile');
        }, 500);
    };

    init();
});