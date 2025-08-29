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

    let init = () => {
        initTools();
        animateStatistics();
        handleForms();

        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert) {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        });
    };

    init();
});