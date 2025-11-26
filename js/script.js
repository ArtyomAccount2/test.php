function initBrandCards() 
{
    let brands = [
        { name: "Acura", image: "img/Stamps/Acura.png", search_term: "acura" },
        { name: "Aixam", image: "img/Stamps/Aixam.png", search_term: "aixam" },
        { name: "Alfa Romeo", image: "img/Stamps/Alfa Romeo.png", search_term: "alfa romeo" },
        { name: "Aston Martin", image: "img/Stamps/Aston Martin.png", search_term: "aston martin" },
        { name: "Audi", image: "img/Stamps/Audi.png", search_term: "audi" },
        { name: "BMW", image: "img/Stamps/BMW.png", search_term: "bmw" },
        { name: "Bentley", image: "img/Stamps/Bentley.png", search_term: "bentley" },
        { name: "Buick", image: "img/Stamps/Buick.png", search_term: "buick" },
        { name: "Cadillac", image: "img/Stamps/Cadillac.png", search_term: "cadillac" },
        { name: "Chevrolet", image: "img/Stamps/Chevrolet.png", search_term: "chevrolet" },
        { name: "Chrysler", image: "img/Stamps/Chrysler.png", search_term: "chrysler" },
        { name: "Dodge", image: "img/Stamps/Dodge.png", search_term: "dodge" },
        { name: "Fiat", image: "img/Stamps/Fiat.png", search_term: "fiat" },
        { name: "Ford", image: "img/Stamps/Ford.png", search_term: "ford" },
        { name: "Gaz", image: "img/Stamps/Gaz.png", search_term: "gaz" },
        { name: "Honda", image: "img/Stamps/Honda.png", search_term: "honda" },
        { name: "Hummer", image: "img/Stamps/Hummer.png", search_term: "hummer" },
        { name: "Hyundai", image: "img/Stamps/Hyundai.png", search_term: "hyundai" },
        { name: "Infiniti", image: "img/Stamps/Infiniti.png", search_term: "infiniti" },
        { name: "Jaguar", image: "img/Stamps/Jaguar.png", search_term: "jaguar" },
        { name: "Jeep", image: "img/Stamps/Jeep.png", search_term: "jeep" },
        { name: "Kia", image: "img/Stamps/Kia.png", search_term: "kia" },
        { name: "Lada", image: "img/Stamps/Lada.png", search_term: "lada" },
        { name: "Lamborghini", image: "img/Stamps/Lamborghini.png", search_term: "lamborghini" },
        { name: "Lancia", image: "img/Stamps/Lancia.png", search_term: "lancia" },
        { name: "Land Rover", image: "img/Stamps/Land Rover.png", search_term: "land rover" },
        { name: "Lexus", image: "img/Stamps/Lexus.png", search_term: "lexus" },
        { name: "Lotus", image: "img/Stamps/Lotus.png", search_term: "lotus" }
    ];

    let container = document.getElementById('carBrandsBlock');

    if (container) 
    {
        container.innerHTML = '';

        brands.forEach(brand => {
            let card = document.createElement('div');
            card.className = 'scrollable-item';
            card.innerHTML = `
                <div class="card shadow-sm h-100 brand-card" data-brand="${brand.search_term}">
                    <img src="${brand.image}" class="card-img-top" alt="${brand.name}">
                    <div class="card-body d-flex flex-column justify-content-between align-items-center">
                        <h6 class="card-title">${brand.name}</h6>
                        <button class="btn btn-outline-primary w-100 select-brand-btn" data-brand="${brand.search_term}">
                            Выбрать
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });

        document.querySelectorAll('.select-brand-btn').forEach(button => {
            button.addEventListener('click', function(e) 
            {
                e.stopPropagation();
                let brandName = this.getAttribute('data-brand');
                goToBrandAssortment(brandName);
            });
        });

        document.querySelectorAll('.brand-card').forEach(card => {
            card.addEventListener('click', function(e) 
            {
                if (!e.target.classList.contains('select-brand-btn')) 
                {
                    let brandName = this.getAttribute('data-brand');
                    goToBrandAssortment(brandName);
                }
            });
        });
    }
}

function initPartsCards() 
{
    let parts = [
        { name: "Коленчатый вал", image: "img/SpareParts/image1.png", category: "двигатель", search_term: "коленчатый вал" },
        { name: "Прокладки двигателя", image: "img/SpareParts/image2.png", category: "двигатель", search_term: "прокладки двигателя" },
        { name: "Топливный насос", image: "img/SpareParts/image3.png", category: "двигатель", search_term: "топливный насос" },
        { name: "Распределительный вал", image: "img/SpareParts/image4.png", category: "двигатель", search_term: "распределительный вал" },
        { name: "Тормозной цилиндр", image: "img/SpareParts/image5.png", category: "тормозная система", search_term: "тормозной цилиндр" },
        { name: "Тормозные колодки", image: "img/SpareParts/image6.png", category: "тормозная система", search_term: "тормозные колодки" },
        { name: "Стабилизатор", image: "img/SpareParts/image7.png", category: "ходовая часть", search_term: "стабилизатор" },
        { name: "Тормозные суппорта", image: "img/SpareParts/image8.png", category: "тормозная система", search_term: "тормозные суппорта" },
        { name: "Топливный фильтр", image: "img/SpareParts/image9.png", category: "фильтры", search_term: "топливный фильтр" },
        { name: "Тормозные диски", image: "img/SpareParts/image10.png", category: "тормозная система", search_term: "тормозные диски" },
        { name: "Цапфа", image: "img/SpareParts/image11.png", category: "ходовая часть", search_term: "цапфа" },
        { name: "Сальники", image: "img/SpareParts/image12.png", category: "двигатель", search_term: "сальники" }
    ];

    let container = document.getElementById('partsContainer');

    if (container) 
    {
        container.innerHTML = '';

        parts.forEach(part => {
            let card = document.createElement('div');
            card.className = 'scrollable-item';
            card.innerHTML = `
                <div class="card shadow-sm h-100 part-card" data-part="${part.search_term}" data-category="${part.category}">
                    <img src="${part.image}" class="card-img-top" alt="${part.name}">
                    <div class="card-body d-flex flex-column justify-content-between align-items-center">
                        <h6 class="card-title">${part.name}</h6>
                        <small class="text-muted mb-2">${getCategoryDisplayName(part.category)}</small>
                        <button class="btn btn-outline-primary w-100 details-part-btn" data-part="${part.search_term}" data-category="${part.category}">
                            Подробнее
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });

        document.querySelectorAll('.details-part-btn').forEach(button => {
            button.addEventListener('click', function(e) 
            {
                e.stopPropagation();
                let partName = this.getAttribute('data-part');
                let category = this.getAttribute('data-category');
                goToPartAssortment(partName, category);
            });
        });

        document.querySelectorAll('.part-card').forEach(card => {
            card.addEventListener('click', function(e) 
            {
                if (!e.target.classList.contains('details-part-btn')) 
                {
                    let partName = this.getAttribute('data-part');
                    let category = this.getAttribute('data-category');
                    goToPartAssortment(partName, category);
                }
            });
        });
    }
}

function goToBrandAssortment(brandName) 
{
    let encodedBrand = encodeURIComponent(brandName);
    let url = `includes/assortment.php?search=${encodedBrand}`;

    window.location.href = url;
}

function goToPartAssortment(partName, category) 
{
    let encodedPart = encodeURIComponent(partName);
    let url = `includes/assortment.php?search=${encodedPart}`;

    if (category && getCategoryMapping(category)) 
    {
        let mappedCategory = getCategoryMapping(category);
        url += `&category=${encodeURIComponent(mappedCategory)}`;
    }

    window.location.href = url;
}

function getCategoryDisplayName(category) 
{
    let categoryMap = {
        'двигатель': 'Двигатель',
        'топливная система': 'Топливная система', 
        'тормозная система': 'Тормозная система',
        'подвеска': 'Подвеска',
        'фильтры': 'Фильтры',
        'ходовая часть': 'Ходовая часть',
        'уплотнения': 'Уплотнения'
    };
    
    return categoryMap[category] || category;
}

function getCategoryMapping(category) 
{
    let mapping = {
        'топливная система': 'двигатель',
        'подвеска': 'ходовая часть', 
        'уплотнения': 'двигатель'
    };
    
    return mapping[category] || category;
}

function setupScrollButtons() 
{
    document.querySelectorAll('.scroll-button').forEach(button => {
        button.addEventListener('click', function() {
            let direction = this.classList.contains('scroll-left') ? -1 : 1;
            let container = this.closest('.position-relative').querySelector('.scrollable');

            let scrollAmount = window.innerWidth <= 768 ? 350 : 450;
            
            container.scrollBy({
                left: direction * scrollAmount,
                behavior: 'smooth'
            });
        });
    });
}

function filterItems(container, searchValue, noResultsId) 
{
    if (!container) 
    {
        return;
    }

    let items = container.querySelectorAll('.scrollable-item');
    let visibleCount = 0;
    
    items.forEach(item => {
        let title = item.querySelector('.card-title') ? item.querySelector('.card-title').textContent.toLowerCase() : '';
        let category = item.querySelector('.text-muted') ? item.querySelector('.text-muted').textContent.toLowerCase() : '';
        let isVisible = searchValue === '' || title.includes(searchValue.toLowerCase()) || category.includes(searchValue.toLowerCase());
        
        item.style.display = isVisible ? 'block' : 'none';

        if (isVisible) 
        {
            visibleCount++;
        }
    });
    
    let noResultsMessage = document.getElementById(noResultsId);

    if (noResultsMessage) 
    {
        noResultsMessage.style.display = visibleCount === 0 ? 'flex' : 'none';
    }
}

function handleHeaderSearch()
{
    let searchValue = document.getElementById('catalogSearchInput').value.trim();
    let clearBtn = document.querySelector('#catalogSearchForm .search-clear');
    
    if (clearBtn) 
    {
        clearBtn.style.display = searchValue ? 'block' : 'none';
    }
    
    let isRussian = /[а-яА-ЯЁё]/.test(searchValue);
    
    if (searchValue === '') 
    {
        clearAllSearches();
        return;
    }

    let navbarHeight = document.querySelector('.navbar') ? document.querySelector('.navbar').offsetHeight : 0;
    let offset = navbarHeight + 20;

    if (isRussian) 
    {
        let targetSection = document.querySelector('#nextSection2');

        if (targetSection) 
        {
            let targetPosition = targetSection.getBoundingClientRect().top + window.pageYOffset - offset;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
        
        setTimeout(() => {
            let partsSearch = document.getElementById('partsSearch');

            if (partsSearch) 
            {
                partsSearch.value = searchValue;
                filterItems(document.querySelector('#popularParts .scrollable'), searchValue, 'no-results-parts');
            }
            
            let brandSearch = document.getElementById('brandSearch');

            if (brandSearch) 
            {
                brandSearch.value = '';
                filterItems(document.querySelector('#carBrandsList .scrollable'), '', 'no-results-brands');
            }
        }, 500);
    } 
    else 
    {
        let targetSection = document.querySelector('#nextSection');

        if (targetSection) 
        {
            let targetPosition = targetSection.getBoundingClientRect().top + window.pageYOffset - offset;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
        
        setTimeout(() => {
            let brandSearch = document.getElementById('brandSearch');

            if (brandSearch) 
            {
                brandSearch.value = searchValue;
                filterItems(document.querySelector('#carBrandsList .scrollable'), searchValue, 'no-results-brands');
            }
            
            let partsSearch = document.getElementById('partsSearch');

            if (partsSearch) 
            {
                partsSearch.value = '';
                filterItems(document.querySelector('#popularParts .scrollable'), '', 'no-results-parts');
            }
        }, 500);
    }
}

function clearAllSearches() 
{
    let brandSearch = document.getElementById('brandSearch');
    let partsSearch = document.getElementById('partsSearch');
    
    if (brandSearch) 
    {
        brandSearch.value = '';
    }

    if (partsSearch) 
    {
        partsSearch.value = '';
    }
    
    filterItems(document.querySelector('#carBrandsList .scrollable'), '', 'no-results-brands');
    filterItems(document.querySelector('#popularParts .scrollable'), '', 'no-results-parts');
}

function checkScrollButtonsVisibility() 
{
    document.querySelectorAll('.scrollable-container').forEach(container => {
        let scrollable = container.querySelector('.scrollable');
        let scrollLeftBtn = container.querySelector('.scroll-left');
        let scrollRightBtn = container.querySelector('.scroll-right');
        
        if (scrollable && scrollLeftBtn && scrollRightBtn) 
        {
            scrollLeftBtn.style.display = scrollable.scrollLeft > 0 ? 'flex' : 'none';
            scrollRightBtn.style.display = scrollable.scrollLeft < (scrollable.scrollWidth - scrollable.clientWidth - 10) ? 'flex' : 'none';
        }
    });
}

function initEnhancedCarousel() 
{
    let carousel = document.getElementById('mainCarousel');
    
    if (!carousel)
    {
        return;
    }
    
    function applyZoomEffect(slide) 
    {
        let img = slide.querySelector('.carousel-image');

        if (img) 
        {
            img.style.transition = 'transform 12s ease-in-out';
            img.style.transform = 'scale(1.05)';
        }
    }
    
    function resetZoomEffect(slide) 
    {
        let img = slide.querySelector('.carousel-image');

        if (img) 
        {
            img.style.transition = 'transform 1.5s ease-in-out';
            img.style.transform = 'scale(1)';
        }
    }
    
    let activeSlide = carousel.querySelector('.carousel-item.active');

    if (activeSlide) 
    {
        setTimeout(() => {
            applyZoomEffect(activeSlide);
        }, 500);
    }

    carousel.addEventListener('slide.bs.carousel', function (e) 
    {
        let currentSlide = e.relatedTarget;
        resetZoomEffect(currentSlide);
    });

    carousel.addEventListener('slid.bs.carousel', function (e) 
    {
        let activeSlide = e.relatedTarget;
        setTimeout(() => {
            applyZoomEffect(activeSlide);
        }, 100);
    });

    let indicators = carousel.querySelector('.carousel-indicators');

    if (indicators) 
    {
        indicators.style.display = 'none';
    }
    
    let prevButton = carousel.querySelector('.carousel-control-prev');
    let nextButton = carousel.querySelector('.carousel-control-next');
    
    if (prevButton) 
    {
        prevButton.style.zIndex = '1000';
        prevButton.style.pointerEvents = 'auto';
        
        prevButton.addEventListener('click', function() 
        {
            let activeSlide = carousel.querySelector('.carousel-item.active');

            if (activeSlide) 
            {
                resetZoomEffect(activeSlide);
            }
        });
    }
    
    if (nextButton) 
    {
        nextButton.style.zIndex = '1000';
        nextButton.style.pointerEvents = 'auto';
        
        nextButton.addEventListener('click', function() 
        {
            let activeSlide = carousel.querySelector('.carousel-item.active');

            if (activeSlide) 
                {
                resetZoomEffect(activeSlide);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() 
{
    initBrandCards();
    initPartsCards();
    setupScrollButtons();
    initCookieConsent();
    initEnhancedCarousel();
    
    setTimeout(checkScrollButtonsVisibility, 100);

    let lastScrollTop = 0;
    let navbar = document.querySelector('.navbar');
    let dropdownMenus = document.querySelectorAll('.dropdown-menu');

    if (navbar) 
    {
        window.addEventListener('scroll', function() 
        {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > lastScrollTop) 
            {
                navbar.classList.add('collapsed');

                dropdownMenus.forEach(menu => {
                    if (menu.classList.contains('show')) 
                    {
                        menu.classList.remove('show');
                    }
                });
            } 
            else 
            {
                navbar.classList.remove('collapsed');
            }

            lastScrollTop = scrollTop;
        });
    }

    let catalogSearchForm = document.getElementById('catalogSearchForm');

    if (catalogSearchForm) 
    {
        catalogSearchForm.addEventListener('submit', function(event) {
            event.preventDefault();
            handleHeaderSearch();
        });
    }

    let searchClear = document.querySelector('#catalogSearchForm .search-clear');

    if (searchClear) 
    {
        searchClear.addEventListener('click', function() 
        {
            let searchInput = document.getElementById('catalogSearchInput');

            if (searchInput)
            {
                searchInput.value = '';
                this.style.display = 'none';
            }
            clearAllSearches();
        });
    }

    let catalogSearchInput = document.getElementById('catalogSearchInput');

    if (catalogSearchInput) 
    {
        catalogSearchInput.addEventListener('input', function() 
        {
            let clearBtn = this.parentElement.querySelector('.search-clear');

            if (clearBtn) 
            {
                clearBtn.style.display = this.value ? 'block' : 'none';
            }

            if (!this.value.trim()) 
            {
                clearAllSearches();
            }
        });
    }

    let brandSearch = document.getElementById('brandSearch');

    if (brandSearch) 
    {
        brandSearch.addEventListener('input', function() {
            filterItems(document.querySelector('#carBrandsList .scrollable'), this.value, 'no-results-brands');
        });
    }

    let partsSearch = document.getElementById('partsSearch');

    if (partsSearch) 
    {
        partsSearch.addEventListener('input', function() {
            filterItems(document.querySelector('#popularParts .scrollable'), this.value, 'no-results-parts');
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) 
        {
            e.preventDefault();
            let target = document.querySelector(this.getAttribute('href'));

            if (target) 
            {
                let navbarHeight = document.querySelector('.navbar') ? document.querySelector('.navbar').offsetHeight : 0;
                let offset = navbarHeight + 20;
                
                let targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    document.querySelectorAll('.scrollable').forEach(scrollable => {
        scrollable.addEventListener('scroll', checkScrollButtonsVisibility);
    });

    window.addEventListener('resize', checkScrollButtonsVisibility);

    if (typeof $ !== 'undefined') 
    {
        $(document).on('click', function(e) 
        {
            if (!$(e.target).closest('.dropdown').length) 
            {
                $('.dropdown-menu').removeClass('show');
            }
        });
    }

    let discountCheckbox = document.getElementById('discountCardCheck');

    if (discountCheckbox) 
    {
        discountCheckbox.addEventListener('change', function() {
            let group = document.getElementById('discountCardNumberGroup');

            if (group) 
            {
                group.style.display = this.checked ? 'block' : 'none';
            }
        });
    }

    let aboutUsSection = document.getElementById('aboutUs');
    
    if (aboutUsSection) 
    {
        let observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1 
        };

        let observer = new IntersectionObserver(function(entries, observer) 
        {
            entries.forEach(function(entry) 
            {
                if (entry.isIntersecting) 
                {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        observer.observe(aboutUsSection);

        function isInViewport(element) 
        {
            if (!element) 
            {
                return false;
            }
            
            let rect = element.getBoundingClientRect();
            return (rect.top <= window.innerHeight && rect.bottom >= 0);
        }
        
        function onScroll() 
        {
            if (isInViewport(aboutUsSection)) 
            {
                if (!aboutUsSection.classList.contains('animate-fadeInUp')) 
                {
                    aboutUsSection.classList.add('animate-fadeInUp');
                }
            } 
        }
        
        window.addEventListener('scroll', onScroll);
        window.addEventListener('load', onScroll);
    }
});

function initCookieConsent() 
{
    let cookieConsent = document.getElementById('cookieConsent');
    let cookieAccept = document.getElementById('cookieAccept');
    let cookieReject = document.getElementById('cookieReject');

    let cookieDecision = getCookie('cookie_decision');
        
    if (!cookieDecision) 
    {
        setTimeout(() => {
            cookieConsent.style.display = 'block';
            setTimeout(() => {
                cookieConsent.classList.add('show');
            }, 100);
        }, 1000);
    }

    if (cookieAccept) 
    {
        cookieAccept.addEventListener('click', function() 
        {
            setCookie('cookie_decision', 'accepted', 365);
            hideCookieConsent();
        });
    }

    if (cookieReject) 
    {
        cookieReject.addEventListener('click', function() 
        {
            setCookie('cookie_decision', 'rejected', 365);
            hideCookieConsent();
            rejectAllCookies();
        });
    }
}

function setCookie(name, value, days) 
{
    let date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    let expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/;SameSite=Lax";
}

function getCookie(name) 
{
    let nameEQ = name + "=";
    let ca = document.cookie.split(';');

    for (let i = 0; i < ca.length; i++) 
    {
        let c = ca[i];

        while (c.charAt(0) === ' ') 
        {
            c = c.substring(1, c.length);
        }

        if (c.indexOf(nameEQ) === 0) 
        {
            return c.substring(nameEQ.length, c.length);
        }
    }

    return null;
}

function deleteCookie(name) 
{
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
}

function hideCookieConsent() 
{
    let cookieConsent = document.getElementById('cookieConsent');

    if (cookieConsent) 
    {
        cookieConsent.classList.remove('show');
        cookieConsent.classList.add('hide');
            
        setTimeout(() => {
            cookieConsent.style.display = 'none';
        }, 500);
    }
}

function checkCookieConsent() 
{
    let decision = getCookie('cookie_decision');
    return decision === 'accepted';
}