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
                        <h6 class="card-title text-center">${brand.name}</h6>
                        <small class="text-muted mb-2">Автомобиль</small>
                        <button class="btn btn-outline-primary select-brand-btn" data-brand="${brand.search_term}">
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

        setTimeout(() => {
            document.querySelectorAll('.scrollable-item').forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
            });
        }, 100);
    }
}

function initPartsCards() 
{
    let parts = [
        { name: "Коленчатый вал", short_name: "Коленч. вал", 
          image: "img/SpareParts/image1.png", 
          category: "двигатель", category_short: "Двиг.",
          search_term: "коленчатый вал" },
        
        { name: "Прокладки двигателя", short_name: "Прокл. двиг.", 
          image: "img/SpareParts/image2.png", 
          category: "двигатель", category_short: "Двиг.",
          search_term: "прокладки двигателя" },
        
        { name: "Топливный насос", short_name: "Топл. насос", 
          image: "img/SpareParts/image3.png", 
          category: "двигатель", category_short: "Двиг.",
          search_term: "топливный насос" },
        
        { name: "Распределительный вал", short_name: "Распред. вал", 
          image: "img/SpareParts/image4.png", 
          category: "двигатель", category_short: "Двиг.",
          search_term: "распределительный вал" },
        
        { name: "Тормозной цилиндр", short_name: "Торм. цилиндр", 
          image: "img/SpareParts/image5.png", 
          category: "тормозная система", category_short: "Торм.",
          search_term: "тормозной цилиндр" },
        
        { name: "Тормозные колодки", short_name: "Торм. колодки", 
          image: "img/SpareParts/image6.png", 
          category: "тормозная система", category_short: "Торм.",
          search_term: "тормозные колодки" },
        
        { name: "Стабилизатор", short_name: "Стабилизатор", 
          image: "img/SpareParts/image7.png", 
          category: "ходовая часть", category_short: "Ход.",
          search_term: "стабилизатор" },
        
        { name: "Тормозные суппорта", short_name: "Торм. суппорта", 
          image: "img/SpareParts/image8.png", 
          category: "тормозная система", category_short: "Торм.",
          search_term: "тормозные суппорта" },
        
        { name: "Топливный фильтр", short_name: "Топл. фильтр", 
          image: "img/SpareParts/image9.png", 
          category: "фильтры", category_short: "Фильтр",
          search_term: "топливный фильтр" },
        
        { name: "Тормозные диски", short_name: "Торм. диски", 
          image: "img/SpareParts/image10.png", 
          category: "тормозная система", category_short: "Торм.",
          search_term: "тормозные диски" },
        
        { name: "Цапфа", short_name: "Цапфа", 
          image: "img/SpareParts/image11.png", 
          category: "ходовая часть", category_short: "Ход.",
          search_term: "цапфа" },
        
        { name: "Сальники", short_name: "Сальники", 
          image: "img/SpareParts/image12.png", 
          category: "двигатель", category_short: "Двиг.",
          search_term: "сальники" }
    ];

    let container = document.getElementById('partsContainer');

    if (container) 
    {
        container.innerHTML = '';

        parts.forEach(part => {
            let categoryFull = getCategoryDisplayName(part.category);
            let categoryShort = part.category_short || getCategoryDisplayName(part.category, true);
            
            let card = document.createElement('div');
            card.className = 'scrollable-item';
            card.innerHTML = `
                <div class="card shadow-sm h-100 part-card" data-part="${part.search_term}" data-category="${part.category}">
                    <img src="${part.image}" class="card-img-top" alt="${part.name}">
                    <div class="card-badge" data-full-text="${categoryFull}" data-short-text="${categoryShort}">
                        ${categoryFull}
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between align-items-center">
                        <h6 class="card-title part-title" data-full-text="${part.name}" data-short-text="${part.short_name}">
                            ${part.name}
                        </h6>
                        <small class="text-muted mb-2 part-category" data-full-text="${categoryFull}" data-short-text="${categoryShort}">
                            ${categoryFull}
                        </small>
                        <button class="btn btn-outline-primary details-part-btn" data-part="${part.search_term}" data-category="${part.category}">
                            <span class="btn-text-full">Подробнее</span>
                            <span class="btn-text-short">Подроб.</span>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });

        updatePartCardTexts();
        window.addEventListener('resize', debounce(updatePartCardTexts, 150));

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

        setTimeout(() => {
            document.querySelectorAll('#partsContainer .scrollable-item').forEach((item, index) => {
                item.style.animationDelay = `${index * 0.1}s`;
            });
        }, 100);
    }
}

function initScrollButtons() 
{
    setupScrollButtons();
    checkScrollButtonsVisibility();

    document.querySelectorAll('.scrollable').forEach(scrollable => {
        scrollable.addEventListener('scroll', debounce(checkScrollButtonsVisibility, 100));
    });

    window.addEventListener('resize', debounce(() => {
        checkScrollButtonsVisibility();
    }, 200));
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

function getCategoryDisplayName(category, short = false) 
{
    let categoryMap = {
        'двигатель': { full: 'Двигатель', short: 'Двиг.' },
        'топливная система': { full: 'Топливная система', short: 'Топл.' },
        'тормозная система': { full: 'Тормозная система', short: 'Торм.' },
        'подвеска': { full: 'Подвеска', short: 'Подв.' },
        'фильтры': { full: 'Фильтры', short: 'Фильтр' },
        'ходовая часть': { full: 'Ходовая часть', short: 'Ход.' },
        'уплотнения': { full: 'Уплотнения', short: 'Упл.' }
    };
    
    if (short && categoryMap[category] && categoryMap[category].short) 
    {
        return categoryMap[category].short;
    }
    
    return categoryMap[category] ? categoryMap[category].full : category;
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
        button.addEventListener('click', function(e) 
        {
            e.preventDefault();
            e.stopPropagation();
            
            let direction = this.classList.contains('scroll-left') ? -1 : 1;
            let containerWrapper = this.closest('.scrollable-container-wrapper');
            let scrollableContainer = containerWrapper.querySelector('.scrollable-container');
            let scrollable = containerWrapper.querySelector('.scrollable');
            
            if (!scrollable || !scrollableContainer) 
            {
                return;
            }

            let visibleItemsCount = getVisibleItemsCount(scrollableContainer);
            let item = scrollable.querySelector('.scrollable-item');

            if (!item) 
            {
                return;
            }
            
            let itemStyle = window.getComputedStyle(item);
            let itemWidth = item.offsetWidth;
            let marginLeft = parseInt(itemStyle.marginLeft) || 0;
            let marginRight = parseInt(itemStyle.marginRight) || 0;
            let itemTotalWidth = itemWidth + marginLeft + marginRight;
            let scrollAmount = itemTotalWidth * visibleItemsCount;

            scrollable.scrollBy({
                left: direction * scrollAmount,
                behavior: 'smooth'
            });

            setTimeout(checkScrollButtonsVisibility, 300);
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
        let scrollLeftBtn = container.parentElement.querySelector('.scroll-left');
        let scrollRightBtn = container.parentElement.querySelector('.scroll-right');
        
        if (scrollable && scrollLeftBtn && scrollRightBtn) 
        {
            let isAtStart = scrollable.scrollLeft <= 10;
            let isAtEnd = scrollable.scrollLeft >= (scrollable.scrollWidth - scrollable.clientWidth - 10);

            scrollLeftBtn.style.opacity = isAtStart ? '0.5' : '1';
            scrollLeftBtn.style.pointerEvents = isAtStart ? 'none' : 'all';
            scrollLeftBtn.style.cursor = isAtStart ? 'not-allowed' : 'pointer';

            scrollRightBtn.style.opacity = isAtEnd ? '0.5' : '1';
            scrollRightBtn.style.pointerEvents = isAtEnd ? 'none' : 'all';
            scrollRightBtn.style.cursor = isAtEnd ? 'not-allowed' : 'pointer';

            let allVisible = scrollable.scrollWidth <= scrollable.clientWidth;

            if (allVisible) 
            {
                scrollLeftBtn.style.visibility = 'hidden';
                scrollRightBtn.style.visibility = 'hidden';
            } 
            else 
            {
                scrollLeftBtn.style.visibility = 'visible';
                scrollRightBtn.style.visibility = 'visible';
            }
        }
    });
}

function updatePartCardTexts() {
    let breakpoint = 992;
    let isMobile = window.innerWidth <= breakpoint;
    
    document.querySelectorAll('.part-title').forEach(titleElement => {
        let fullText = titleElement.getAttribute('data-full-text');
        let shortText = titleElement.getAttribute('data-short-text');
        
        if (isMobile && shortText && shortText !== fullText) 
        {
            titleElement.textContent = shortText;
        } 
        else 
        {
            titleElement.textContent = fullText;
        }
    });
    
    document.querySelectorAll('.part-category').forEach(categoryElement => {
        let fullText = categoryElement.getAttribute('data-full-text');
        let shortText = categoryElement.getAttribute('data-short-text');
        
        if (isMobile && shortText && shortText !== fullText) 
        {
            categoryElement.textContent = shortText;
        } 
        else 
        {
            categoryElement.textContent = fullText;
        }
    });
    
    document.querySelectorAll('.card-badge').forEach(badgeElement => {
        let fullText = badgeElement.getAttribute('data-full-text') || badgeElement.getAttribute('data-full-badge');
        let shortText = badgeElement.getAttribute('data-short-text') || badgeElement.getAttribute('data-short-badge');
        
        if (isMobile && shortText && shortText !== fullText) 
        {
            badgeElement.textContent = shortText;
        } 
        else if (fullText) 
        {
            badgeElement.textContent = fullText;
        }
    });
    
    document.querySelectorAll('.details-part-btn').forEach(button => {
        let fullTextSpans = button.querySelectorAll('.btn-text-full');
        let shortTextSpans = button.querySelectorAll('.btn-text-short');
        
        if (isMobile) 
        {
            fullTextSpans.forEach(span => span.style.display = 'none');
            shortTextSpans.forEach(span => span.style.display = 'inline');
        } 
        else 
        {
            fullTextSpans.forEach(span => span.style.display = 'inline');
            shortTextSpans.forEach(span => span.style.display = 'none');
        }
    });

    document.querySelectorAll('.card-overlay .details-part-btn').forEach(button => {
        let fullTextSpans = button.querySelectorAll('.btn-text-full');
        let shortTextSpans = button.querySelectorAll('.btn-text-short');
        
        if (isMobile) 
        {
            fullTextSpans.forEach(span => span.style.display = 'none');
            shortTextSpans.forEach(span => span.style.display = 'inline');
        } 
        else 
        {
            fullTextSpans.forEach(span => span.style.display = 'inline');
            shortTextSpans.forEach(span => span.style.display = 'none');
        }
    });
}

function centerAllCarouselContent() 
{
    let carousel = document.getElementById('mainCarousel');
    let navbar = document.querySelector('.navbar');
    let allCaptionContents = document.querySelectorAll('.caption-content');
    
    if (!carousel || !navbar || allCaptionContents.length === 0) 
    {
        return;
    }
    
    let navbarHeight = navbar.offsetHeight;
    let isMobile = window.innerWidth <= 768;
    
    allCaptionContents.forEach(captionContent => {
        let slide = captionContent.closest('.carousel-item');

        if (slide) 
        {
            if (isMobile) 
            {
                captionContent.style.paddingTop = '0';
                captionContent.style.marginTop = `${navbarHeight + 20}px`;
                captionContent.style.alignItems = 'center';
                captionContent.style.justifyContent = 'flex-start';
            } 
            else 
            {
                captionContent.style.marginTop = `${navbarHeight}px`;
                captionContent.style.alignItems = 'center';
                captionContent.style.justifyContent = 'center';
            }
        }
    });
}

function centerCarouselContentFlex() 
{
    let carousel = document.getElementById('mainCarousel');
    let navbar = document.querySelector('.navbar');
    let allCarouselCaptions = document.querySelectorAll('.carousel-caption');
    
    if (!carousel || !navbar || allCarouselCaptions.length === 0) 
    {
        return;
    }
    
    let navbarHeight = navbar.offsetHeight;
    let isMobile = window.innerWidth <= 768;
    
    allCarouselCaptions.forEach(caption => {
        let slide = caption.closest('.carousel-item');

        if (slide) 
        {
            if (isMobile) 
            {
                caption.style.paddingTop = `${navbarHeight + 20}px`;
                caption.style.alignItems = 'center';
                caption.style.justifyContent = 'flex-start';
            } 
            else 
            {
                caption.style.paddingTop = `${navbarHeight}px`;
                caption.style.alignItems = 'center';
                caption.style.justifyContent = 'center';
            }
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

    centerCarouselContentFlex();
    
    window.addEventListener('resize', centerCarouselContentFlex);
    window.addEventListener('load', centerCarouselContentFlex);
    
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

    if (activeSlide) {
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
        
        setTimeout(centerCarouselContentFlex, 50);
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

    setTimeout(centerCarouselContentFlex, 1000);

    setTimeout(() => {
        let allSlides = carousel.querySelectorAll('.carousel-item');

        allSlides.forEach(slide => {
            let caption = slide.querySelector('.carousel-caption');

            if (caption) 
            {
                let navbarHeight = document.querySelector('.navbar').offsetHeight;
                let isMobile = window.innerWidth <= 768;
                
                if (isMobile) 
                {
                    caption.style.paddingTop = `${navbarHeight + 20}px`;
                    caption.style.alignItems = 'center';
                    caption.style.justifyContent = 'flex-start';
                } 
                else 
                {
                    caption.style.paddingTop = `${navbarHeight}px`;
                    caption.style.alignItems = 'center';
                    caption.style.justifyContent = 'center';
                }
            }
        });
    }, 1500);
}

function getVisibleItemsCount(container) 
{
    let scrollable = container.querySelector('.scrollable');

    if (!scrollable || scrollable.children.length === 0) 
    {
        return 1;
    }
    
    let firstItem = scrollable.querySelector('.scrollable-item');

    if (!firstItem) 
    {
        return 1;
    }
    
    let containerWidth = container.clientWidth;
    let itemWidth = firstItem.offsetWidth;
    let itemStyle = window.getComputedStyle(firstItem);
    let marginLeft = parseInt(itemStyle.marginLeft) || 0;
    let marginRight = parseInt(itemStyle.marginRight) || 0;
    let itemTotalWidth = itemWidth + marginLeft + marginRight;
    let visibleCount = Math.floor(containerWidth / itemTotalWidth);
    
    return Math.max(1, visibleCount);
}

document.addEventListener('DOMContentLoaded', function() 
{
    initBrandCards();
    initPartsCards();
    initScrollButtons();
    initCookieConsent();
    initEnhancedCarousel();

    setTimeout(() => {
        initScrollButtons();
    }, 500);
    
    setTimeout(updatePartCardTexts, 100);
    setTimeout(checkScrollButtonsVisibility, 500);

    window.addEventListener('resize', debounce(() => {
        updatePartCardTexts();
        checkScrollButtonsVisibility();
    }, 150));

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

function debounce(func, wait) 
{
    let timeout;
    
    return function executedFunction(...args) 
    {
        let later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}