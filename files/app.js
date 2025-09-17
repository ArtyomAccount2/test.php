document.addEventListener('DOMContentLoaded', function() 
{
    let canvas = document.getElementById('wheelCanvas');
    let wheelFortune = canvas.getContext('2d');
    let spinButton = document.getElementById('spinButton');
    let resultModal = new bootstrap.Modal(document.getElementById('wheelResultModal'));
    let purchaseCounter = document.getElementById('purchaseCounter');
    let purchasesLeft = document.getElementById('purchasesLeft');

    let segments = [
        { text: "Неудача", color: "#007bff", desc: "Попробуйте в следующий раз!" },
        { text: "10%", color: "#f0f0f0", desc: "Скидка 10% на следующий заказ" },
        { text: "Бесп. доставка", color: "#007bff", desc: "Бесплатная доставка следующего заказа!" },
        { text: "15%", color: "#f0f0f0", desc: "Скидка 15% на следующий заказ" },
        { text: "5%", color: "#007bff", desc: "Скидка 5% на следующий заказ" },
        { text: "Неудача", color: "#f0f0f0", desc: "Попробуйте в следующий раз!" },
        { text: "10%", color: "#007bff", desc: "Скидка 10% на следующий заказ" },
        { text: "Бесп. доставка", color: "#f0f0f0", desc: "Бесплатная доставка следующего заказа!" },
        { text: "Неудача", color: "#007bff", desc: "Попробуйте в следующий раз!" },
        { text: "5%", color: "#f0f0f0", desc: "Скидка 5% на следующий заказ" }
    ];

    let requiredPurchases = 10;
    let segments_angle = (2 * Math.PI) / segments.length;
    
    let rotation = 0;
    let isSpinning = false;
    let autoCloseTimer;
    let currentPrizeType = '';

    function drawWheel() 
    {
        let centerX = canvas.width / 2;
        let centerY = canvas.height / 2;
        let radius = canvas.width / 2 - 5;

        wheelFortune.clearRect(0, 0, canvas.width, canvas.height);

        segments.forEach((segment, i) => {
            wheelFortune.beginPath();
            wheelFortune.moveTo(centerX, centerY);
            wheelFortune.arc(centerX, centerY, radius, i * segments_angle + rotation, (i + 1) * segments_angle + rotation);

            wheelFortune.fillStyle = segment.color;
            wheelFortune.fill();
            wheelFortune.stroke();
            wheelFortune.save();
            wheelFortune.translate(centerX, centerY);
            wheelFortune.rotate(i * segments_angle + segments_angle / 2 + rotation);

            wheelFortune.textAlign = "right";
            wheelFortune.fillStyle = "#000";
            wheelFortune.font = "bold 12px Montserrat";

            wheelFortune.fillText(segment.text, radius - 10, 5);
            wheelFortune.restore();
        });

        wheelFortune.beginPath();
        wheelFortune.arc(centerX, centerY, 15, 0, 2 * Math.PI);
        wheelFortune.fillStyle = "#333";
        wheelFortune.fill();
    }

    function updateSpinStatus() 
    {
        let spinsLeft = localStorage.getItem('wheelSpinsLeft') || 0;
        spinButton.disabled = spinsLeft > 0;

        if (spinsLeft >= 0)
        {
            purchaseCounter.style.display = 'block';
        }

        purchasesLeft.textContent = spinsLeft;
    }

    spinButton.addEventListener('click', function() 
    {
        if (isSpinning || spinButton.disabled) 
        {
            return;
        }
    
        isSpinning = true;
        spinButton.disabled = true;
    
        let spins = 5 + Math.floor(Math.random() * 3);
        let targetSegment = Math.floor(Math.random() * segments.length);
        let targetSegmentCenterAngle = targetSegment * segments_angle + segments_angle / 2;
    
        let targetRot = spins * 2 * Math.PI + (2 * Math.PI - targetSegmentCenterAngle) - 8;

        animateSpin(targetRot, targetSegment);
    });

    function animateSpin(targetRot, targetSegment) 
    {
        let start = Date.now();
        let duration = 3000;

        function animate() 
        {
            let time = Date.now() - start;
            let progress = Math.min(time / duration, 1);
            rotation = (1 - Math.pow(1 - progress, 3)) * targetRot;

            drawWheel();

            if (progress < 1) 
            {
                requestAnimationFrame(animate);
            } 
            else 
            {
                finishSpin(targetSegment);
            }
        }

        animate();
    }

    function finishSpin(winIndex) 
    {
        isSpinning = false;
        currentWinIndex = winIndex;
        let prize = segments[winIndex];
        
        currentPrizeType = prize.text === "Неудача" ? 'failure' : 'success';
        
        updatePrizeModal(prize);
        
        resultModal.show();
        
        if (currentPrizeType === 'success') 
        {
            createConfetti();
        }
        
        startAutoCloseTimer();
        
        localStorage.setItem('wheelLastPrize', JSON.stringify(prize));
        localStorage.setItem('wheelSpinsLeft', requiredPurchases);
        
        drawWheel();
        updateSpinStatus();
    }

    function updatePrizeModal(prize) 
    {
        let modal = document.getElementById('wheelResultModal');
        let icon = document.getElementById('modalPrizeIcon');
        let resultText = document.getElementById('modalResultText');
        let description = document.getElementById('modalResultDescription');
        let codeSection = document.getElementById('prizeCodeSection');
        let promoCode = document.getElementById('modalPromoCode');
        let usePrizeBtn = document.getElementById('usePrizeBtn');
        let laterBtn = document.querySelector('.prize-action-btn[data-bs-dismiss="modal"]');
        
        modal.classList.remove('prize-success', 'prize-failure');
        modal.classList.add(`prize-${currentPrizeType}`);
        
        icon.className = currentPrizeType === 'success' ? 'bi bi-trophy-fill prize-icon' : 'bi bi-emoji-frown-fill prize-icon';
        
        resultText.textContent = prize.text.includes('%') ? `Скидка ${prize.text}` : prize.text;
        resultText.style.color = currentPrizeType === 'success' ? '#28a745' : '#dc3545';
        description.textContent = prize.desc;
        
        if (currentPrizeType === 'success') 
        {
            let generatedCode = generatePromoCode(prize.text);
            promoCode.textContent = generatedCode;
            codeSection.style.display = 'block';
            usePrizeBtn.style.display = 'block';
            laterBtn.style.display = 'block';
        } 
        else 
        {
            codeSection.style.display = 'none';
            usePrizeBtn.style.display = 'none';
            laterBtn.style.display = 'none';
        }
        
        let copyBtn = document.querySelector('.copy-btn');

        copyBtn.onclick = function() 
        {
            navigator.clipboard.writeText(promoCode.textContent).then(() => {
                let originalHtml = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="bi bi-check"></i>';

                setTimeout(() => {
                    copyBtn.innerHTML = originalHtml;
                }, 2000);
            });
        };
    }

    function generatePromoCode(prizeText) 
    {
        let prefix = prizeText.includes('доставка') ? 'FREE' : 'DISCOUNT';
        let randomNum = Math.floor(1000 + Math.random() * 9000);
        return `${prefix}${randomNum}`;
    }

    function createConfetti() 
    {
        let container = document.getElementById('confetti-container');
        container.innerHTML = '';
        
        let colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#6f42c1', '#fd7e14'];
        
        for (let i = 0; i < 150; i++) 
        {
            let confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
            confetti.style.animationDelay = (Math.random() * 2) + 's';
            
            container.appendChild(confetti);
        }
    }

    function startAutoCloseTimer() 
    {
        let timeLeft = 10;
        let timerElement = document.getElementById('prizeTimer');
        let timerBar = document.getElementById('prizeTimerBar');
        
        if (autoCloseTimer) clearInterval(autoCloseTimer);
        
        autoCloseTimer = setInterval(() => {
            timeLeft--;
            timerElement.textContent = timeLeft;
            timerBar.style.width = (timeLeft * 10) + '%';
            
            if (timeLeft <= 0) 
            {
                clearInterval(autoCloseTimer);
                resultModal.hide();
            }
        }, 1000);
    }

    document.getElementById('wheelResultModal').addEventListener('hidden.bs.modal', function() 
    {
        if (autoCloseTimer) 
        {
            clearInterval(autoCloseTimer);
        }
    });

    document.getElementById('usePrizeBtn').addEventListener('click', function() 
    {
        window.location.href = 'includes/assortment.php';
    });

    function addPurchase() 
    {
        let spinsLeft = parseInt(localStorage.getItem('wheelSpinsLeft')) || 0;

        if (spinsLeft <= 0) 
        {
            return;
        }

        spinsLeft--;
        purchasesLeft.textContent = spinsLeft;

        if (spinsLeft <= 0) 
        {
            localStorage.removeItem('wheelSpinsLeft');
            spinButton.disabled = false;
        } 
        else 
        {
            localStorage.setItem('wheelSpinsLeft', spinsLeft);
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key.toLowerCase() == 'p') 
        {
            addPurchase();
        }
    });

    drawWheel();
    updateSpinStatus();
});