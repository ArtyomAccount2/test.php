function loadBrandsFromDB() 
{
    return fetch('includes/get_display_data.php?action=get_brands')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) 
            {
                return data.data;
            }
            return [];
        })
        .catch(error => {
            console.error('Ошибка загрузки брендов:', error);
            return [];
        });
}

function loadPartsFromDB() 
{
    return fetch('includes/get_display_data.php?action=get_parts')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) 
            {
                return data.data;
            }
            return [];
        })
        .catch(error => {
            console.error('Ошибка загрузки запчастей:', error);
            return [];
        });
}

function initBrandCards() 
{
    let container = document.getElementById('carBrandsBlock');
    
    if (!container) 
    {
        return;
    }
    
    loadBrandsFromDB().then(brands => {
        if (brands.length === 0) 
        {
            container.innerHTML = `
                <div class="alert alert-warning w-100 text-center">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Данные о брендах временно недоступны
                </div>
            `;
            return;
        }

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
    });
}

function initPartsCards() 
{
    let container = document.getElementById('partsContainer');
    
    if (!container) 
    {
        return;
    }
    
    loadPartsFromDB().then(parts => {
        if (parts.length === 0) 
        {
            container.innerHTML = `
                <div class="alert alert-warning w-100 text-center">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Данные о запчастях временно недоступны
                </div>
            `;
            return;
        }
        
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
    });
}

function forceUpdateScrollButtons() 
{
    document.querySelectorAll('.scrollable-container-wrapper').forEach(wrapper => {
        let scrollable = wrapper.querySelector('.scrollable');
        let scrollLeftBtn = wrapper.querySelector('.scroll-left');
        let scrollRightBtn = wrapper.querySelector('.scroll-right');
        
        if (!scrollable || !scrollLeftBtn || !scrollRightBtn) 
        {
            return;
        }

        let visibleItems = Array.from(scrollable.querySelectorAll('.scrollable-item')).filter(
            item => item.style.display !== 'none'
        );
        
        if (visibleItems.length === 0) 
        {
            scrollLeftBtn.style.display = 'none';
            scrollRightBtn.style.display = 'none';
            return;
        }

        scrollLeftBtn.style.display = 'flex';
        scrollRightBtn.style.display = 'flex';
        scrollLeftBtn.style.visibility = 'visible';
        scrollRightBtn.style.visibility = 'visible';

        let allVisible = scrollable.scrollWidth <= scrollable.clientWidth + 10;

        if (allVisible) 
        {
            scrollLeftBtn.style.display = 'none';
            scrollRightBtn.style.display = 'none';
        }
    });
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

    document.querySelectorAll('.scrollable').forEach(scrollable => {
        let observer = new MutationObserver(function() {
            setTimeout(forceUpdateScrollButtons, 50);
        });

        observer.observe(scrollable, { 
            childList: true, 
            subtree: true,
            attributes: true,
            attributeFilter: ['style']
        });
    });
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

function filterItems(container, searchValue, noResultsId, searchType = 'all') 
{
    if (!container) 
    {
        return;
    }

    let items = container.querySelectorAll('.scrollable-item');
    let visibleCount = 0;
    let searchLower = searchValue.toLowerCase().trim();
    let isCyrillic = /[а-яА-ЯЁё]/.test(searchLower);
    let isLatin = /[a-zA-Z]/.test(searchLower);
    
    items.forEach(item => {
        let titleElement = item.querySelector('.card-title');
        let categoryElement = item.querySelector('.text-muted') || item.querySelector('.part-category');
        let title = titleElement ? titleElement.textContent.toLowerCase() : '';
        let category = categoryElement ? categoryElement.textContent.toLowerCase() : '';
        
        let isVisible = false;
        
        if (searchValue === '') 
        {
            isVisible = true;
        } 
        else 
        {
            if (searchType === 'brands') 
            {
                if (isCyrillic) 
                {
                    isVisible = false;
                } 
                else 
                {
                    isVisible = title.includes(searchLower) || category.includes(searchLower);
                }
            } 
            else if (searchType === 'parts') 
            {
                if (isLatin && !isCyrillic) 
                {
                    isVisible = false;
                } 
                else 
                {
                    isVisible = title.includes(searchLower) || category.includes(searchLower);
                }
            } 
            else 
            {
                isVisible = title.includes(searchLower) || category.includes(searchLower);
            }
        }
        
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

    let containerWrapper = container.closest('.scrollable-container-wrapper');

    if (containerWrapper) 
    {
        let scrollLeftBtn = containerWrapper.querySelector('.scroll-left');
        let scrollRightBtn = containerWrapper.querySelector('.scroll-right');
        
        if (visibleCount > 0) 
        {
            if (scrollLeftBtn) 
            {
                scrollLeftBtn.style.display = 'flex';
                scrollLeftBtn.style.visibility = 'visible';
                scrollLeftBtn.style.opacity = '1';
            }
            if (scrollRightBtn) 
            {
                scrollRightBtn.style.display = 'flex';
                scrollRightBtn.style.visibility = 'visible';
                scrollRightBtn.style.opacity = '1';
            }
            setTimeout(() => checkScrollButtonsVisibility(), 50);
        } 
        else 
        {
            if (scrollLeftBtn) 
            {
                scrollLeftBtn.style.display = 'none';
            }
            if (scrollRightBtn) 
            {
                scrollRightBtn.style.display = 'none';
            }
        }
    }
}

function searchByArticle(article) 
{
    return fetch('includes/search_by_article.php?article=' + encodeURIComponent(article))
        .then(response => response.json())
        .then(data => {
            return data;
        })
        .catch(error => {
            console.error('Ошибка при поиске по артиклю:', error);
            return { success: false, message: 'Ошибка сети' };
        });
}

function handleHeaderSearch()
{
    let searchValue = document.getElementById('catalogSearchInput').value.trim();
    let clearBtn = document.querySelector('#catalogSearchForm .search-clear');
    
    if (clearBtn) 
    {
        clearBtn.style.display = searchValue ? 'block' : 'none';
    }
    
    if (searchValue === '') 
    {
        clearAllSearches();
        return;
    }

    showSearchLoading();

    searchByArticle(searchValue).then(result => {
        hideSearchLoading();
        
        if (result.success && result.found) 
        {
            let productUrl;

            if (result.source_table === 'category_products') 
            {
                productUrl = `../files/categories/product_detail.php?id=${result.product_id}&back=../../index.php`;
            }
            else if (result.source_table === 'products')
            {
                productUrl = `includes/all_details.php?id=${result.product_id}&back=index.php`;
            }

            if (productUrl) 
            {
                window.location.href = productUrl;
            }
            
            return;
        }

        performRegularSearch(searchValue);
    });
}

function performRegularSearch(searchValue) 
{
    let isRussian = /[а-яА-ЯЁё]/.test(searchValue);
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
                filterItems(document.querySelector('#popularParts .scrollable'), searchValue, 'no-results-parts', 'parts');
            }
            
            let brandSearch = document.getElementById('brandSearch');

            if (brandSearch) 
            {
                brandSearch.value = '';
                filterItems(document.querySelector('#carBrandsList .scrollable'), '', 'no-results-brands', 'brands');
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
                filterItems(document.querySelector('#carBrandsList .scrollable'), searchValue, 'no-results-brands', 'brands');
            }
            
            let partsSearch = document.getElementById('partsSearch');

            if (partsSearch) 
            {
                partsSearch.value = '';
                filterItems(document.querySelector('#popularParts .scrollable'), '', 'no-results-parts', 'parts');
            }
        }, 500);
    }
}

function showSearchLoading() 
{
    let searchButton = document.querySelector('#catalogSearchForm .search-button');

    if (searchButton) 
    {
        let originalHtml = searchButton.innerHTML;
        searchButton.setAttribute('data-original-html', originalHtml);
        searchButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        searchButton.disabled = true;
    }
}

function hideSearchLoading() 
{
    let searchButton = document.querySelector('#catalogSearchForm .search-button');

    if (searchButton) 
    {
        let originalHtml = searchButton.getAttribute('data-original-html');

        if (originalHtml) 
        {
            searchButton.innerHTML = originalHtml;
        }
        
        searchButton.disabled = false;
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

        if (!scrollLeftBtn || !scrollRightBtn) 
        {
            return;
        }

        if (!scrollable || scrollable.children.length === 0) 
        {
            scrollLeftBtn.style.display = 'none';
            scrollRightBtn.style.display = 'none';
            return;
        }

        scrollLeftBtn.style.display = 'flex';
        scrollRightBtn.style.display = 'flex';

        let allVisible = scrollable.scrollWidth <= scrollable.clientWidth + 10;
        
        if (allVisible) 
        {
            scrollLeftBtn.style.display = 'none';
            scrollRightBtn.style.display = 'none';
            return;
        }

        let isAtStart = scrollable.scrollLeft <= 10;
        let isAtEnd = scrollable.scrollLeft >= (scrollable.scrollWidth - scrollable.clientWidth - 10);

        scrollLeftBtn.style.opacity = isAtStart ? '0.5' : '1';
        scrollLeftBtn.style.pointerEvents = isAtStart ? 'none' : 'all';
        scrollLeftBtn.style.cursor = isAtStart ? 'not-allowed' : 'pointer';

        scrollRightBtn.style.opacity = isAtEnd ? '0.5' : '1';
        scrollRightBtn.style.pointerEvents = isAtEnd ? 'none' : 'all';
        scrollRightBtn.style.cursor = isAtEnd ? 'not-allowed' : 'pointer';

        scrollLeftBtn.style.visibility = 'visible';
        scrollRightBtn.style.visibility = 'visible';
    });
}

function updatePartCardTexts() 
{
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
            }, 50);
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
        });
    }
}

function hideCookieConsent() 
{
    let cookieConsent = document.getElementById('cookieConsent');

    if (cookieConsent) 
    {
        cookieConsent.classList.remove('show');
        cookieConsent.classList.add('hiding');
        
        setTimeout(() => {
            cookieConsent.style.display = 'none';
            cookieConsent.classList.remove('hiding');
        }, 500);
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

function forceUpdateScrollButtons() 
{
    document.querySelectorAll('.scrollable-container-wrapper').forEach(wrapper => {
        let scrollable = wrapper.querySelector('.scrollable');
        let scrollLeftBtn = wrapper.querySelector('.scroll-left');
        let scrollRightBtn = wrapper.querySelector('.scroll-right');
        
        if (!scrollable || !scrollLeftBtn || !scrollRightBtn) 
        {
            return;
        }
        
        let visibleItems = Array.from(scrollable.querySelectorAll('.scrollable-item')).filter(
            item => item.style.display !== 'none'
        );
        
        if (visibleItems.length === 0) 
        {
            scrollLeftBtn.style.display = 'none';
            scrollRightBtn.style.display = 'none';
            return;
        }
        
        scrollLeftBtn.style.display = 'flex';
        scrollRightBtn.style.display = 'flex';
        scrollLeftBtn.style.visibility = 'visible';
        scrollRightBtn.style.visibility = 'visible';
        
        let allVisible = scrollable.scrollWidth <= scrollable.clientWidth + 10;

        if (allVisible) 
        {
            scrollLeftBtn.style.display = 'none';
            scrollRightBtn.style.display = 'none';
        }
    });
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
    let isScrollingToAnchor = false;

    if (navbar) 
    {
        window.addEventListener('scroll', function() 
        {
            if (isScrollingToAnchor) 
            {
                return;
            }
            
            if (window.innerWidth <= 1024) 
            {
                if (navbar.classList.contains('collapsed')) 
                {
                    navbar.classList.remove('collapsed');
                }

                return;
            }

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
        brandSearch.addEventListener('input', function() 
        {
            filterItems(document.querySelector('#carBrandsList .scrollable'), this.value, 'no-results-brands', 'brands');

            let isCyrillic = /[а-яА-ЯЁё]/.test(this.value);
            let noResultsMsg = document.getElementById('no-results-brands');
            
            if (isCyrillic && this.value.length > 0) 
            {
                if (noResultsMsg) 
                {
                    noResultsMsg.innerHTML = `
                        <i class="bi bi-info-circle" style="font-size: 2rem; color: #ffc107;"></i>
                        <p class="mt-2 text-center">Поиск по маркам работает только на <strong>латинице</strong></p>
                        <small class="text-muted text-center">Введите название марки на английском языке</small>
                    `;
                    noResultsMsg.style.display = 'flex';
                }
            } 
            else if (noResultsMsg) 
            {
                noResultsMsg.innerHTML = `
                    <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                    <p class="mt-2 text-center">Марка не найдена</p>
                    <small class="text-muted text-center">Попробуйте изменить запрос или посмотреть все марки</small>
                `;
            }
            
            setTimeout(forceUpdateScrollButtons, 100);
        });
    }

    let partsSearch = document.getElementById('partsSearch');

    if (partsSearch) 
    {
        partsSearch.addEventListener('input', function() 
        {
            filterItems(document.querySelector('#popularParts .scrollable'), this.value, 'no-results-parts', 'parts');

            let isLatin = /[a-zA-Z]/.test(this.value);
            let isCyrillic = /[а-яА-ЯЁё]/.test(this.value);
            let noResultsMsg = document.getElementById('no-results-parts');
            
            if (isLatin && !isCyrillic && this.value.length > 0) 
            {
                if (noResultsMsg) 
                {
                    noResultsMsg.innerHTML = `
                        <i class="bi bi-info-circle" style="font-size: 2rem; color: #ffc107;"></i>
                        <p class="mt-2 text-center">Поиск по запчастям работает только на <strong>кириллице</strong></p>
                        <small class="text-muted text-center">Введите название запчасти на русском языке</small>
                    `;
                    noResultsMsg.style.display = 'flex';
                }
            } 
            else if (noResultsMsg) 
            {
                noResultsMsg.innerHTML = `
                    <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                    <p class="mt-2 text-center">Запчасть не найдена</p>
                    <small class="text-muted text-center">Попробуйте изменить запрос или посмотреть весь каталог</small>
                `;
            }
            
            setTimeout(forceUpdateScrollButtons, 100);
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) 
        {
            let href = this.getAttribute('href');

            if (href === '#' || href === '#0' || href === '#carouselExample' || href === '#mainCarousel') 
            {
                return;
            }
            
            e.preventDefault();
            let target = document.querySelector(href);
            
            if (target) 
            {
                isScrollingToAnchor = true;
                let navbar = document.querySelector('.navbar');

                if (navbar && navbar.classList.contains('collapsed')) 
                {
                    navbar.classList.remove('collapsed');
                }
                
                let navbarHeight = navbar ? navbar.offsetHeight : 0;
                let targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });

                setTimeout(() => {
                    isScrollingToAnchor = false;
                }, 1000);
            }
        });
    });

    document.querySelectorAll('.search-clear').forEach(clearBtn => {
        clearBtn.addEventListener('click', function() 
        {
            let input = this.parentElement.querySelector('.search-input');

            if (input) 
            {
                input.value = '';

                if (input.id === 'brandSearch') 
                {
                    filterItems(document.querySelector('#carBrandsList .scrollable'), '', 'no-results-brands', 'brands');

                    let noResultsMsg = document.getElementById('no-results-brands');

                    if (noResultsMsg) 
                    {
                        noResultsMsg.innerHTML = `
                            <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-center">Марка не найдена</p>
                            <small class="text-muted text-center">Попробуйте изменить запрос или посмотреть все марки</small>
                        `;
                    }
                } 
                else if (input.id === 'partsSearch') 
                {
                    filterItems(document.querySelector('#popularParts .scrollable'), '', 'no-results-parts', 'parts');

                    let noResultsMsg = document.getElementById('no-results-parts');

                    if (noResultsMsg) 
                    {
                        noResultsMsg.innerHTML = `
                            <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-center">Запчасть не найдена</p>
                            <small class="text-muted text-center">Попробуйте изменить запрос или посмотреть весь каталог</small>
                        `;
                    }
                }

                this.style.display = 'none';
                setTimeout(forceUpdateScrollButtons, 100);
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
        discountCheckbox.addEventListener('change', function() 
        {
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

    let socialFloat = document.getElementById('socialFloat');
    let socialToggle = document.getElementById('socialToggle');
    let socialIcons = document.querySelector('.social-icons-container');
    let socialIconsList = document.querySelectorAll('.social-icon-float');
    
    setTimeout(() => {
        socialFloat.style.display = 'flex';
    }, 1000);
    
    if (socialToggle) 
    {
        socialToggle.addEventListener('click', function(e) 
        {
            e.stopPropagation();
            
            let isActive = socialToggle.classList.contains('active');
            
            if (isActive) 
            {
                socialToggle.classList.remove('active');
                socialIcons.classList.remove('show');
                socialIcons.classList.add('social-float-out');
                
                setTimeout(() => {
                    socialIcons.classList.remove('social-float-out');
                }, 400);
                
            } 
            else 
            {
                socialToggle.classList.add('active');
                socialIcons.classList.add('show');
                socialIcons.classList.add('social-float-in');
                
                setTimeout(() => {
                    socialIcons.classList.remove('social-float-in');
                }, 400);
            }
        });
    }

    document.addEventListener('click', function(e) 
    {
        if (!socialFloat.contains(e.target) && socialToggle.classList.contains('active')) 
        {
            socialToggle.classList.remove('active');
            socialIcons.classList.remove('show');
            socialIcons.classList.add('social-float-out');
            
            setTimeout(() => {
                socialIcons.classList.remove('social-float-out');
            }, 400);
        }
    });

    document.addEventListener('keydown', function(e) 
    {
        if (e.key === 'Escape' && socialToggle.classList.contains('active')) 
        {
            socialToggle.click();
        }
    });
    
    let scrollTimer;
    
    window.addEventListener('scroll', function() 
    {
        if (window.innerWidth <= 768 && socialToggle.classList.contains('active')) 
        {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(() => {
                if (socialToggle.classList.contains('active')) 
                {
                    socialToggle.click();
                }
            }, 300);
        }
    });

    socialIconsList.forEach(icon => {
        icon.addEventListener('mouseenter', function() 
        {
            this.style.transition = 'all 0.25s ease';
        });
        
        icon.addEventListener('mouseleave', function() 
        {
            this.style.transition = 'all 0.3s ease';
        });
    });

    let aboutUsButton = document.querySelector('.carousel-caption .btn-outline-light[href="#aboutUs"]');

    if (aboutUsButton) 
    {
        aboutUsButton.addEventListener('click', function(e) 
        {
            let navbar = document.querySelector('.navbar');

            if (navbar && navbar.classList.contains('collapsed')) 
            {
                navbar.classList.remove('collapsed');
            }
        });
    }

    document.querySelectorAll('.carousel-caption .btn').forEach(button => {
        let href = button.getAttribute('href');

        if (href && href.startsWith('#')) 
        {
            button.addEventListener('click', function() 
            {
                let navbar = document.querySelector('.navbar');
                
                if (navbar && navbar.classList.contains('collapsed')) 
                {
                    navbar.classList.remove('collapsed');
                }
            });
        }
    });
});