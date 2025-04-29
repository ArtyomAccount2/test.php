"use strict";

function initScrollbars() 
{
    let containers = [
        { container: document.getElementById('carBrandsBlock'), scrollbar: document.getElementById('carBrandsScrollbar') },
        { container: document.getElementById('partsContainer'), scrollbar: document.getElementById('popularPartsScrollbar') }
    ];

    containers.forEach(({ container, scrollbar }) => {
        if (!container || !scrollbar) 
        {
            return;
        }

        let scrollThumb = scrollbar.querySelector('.scrollbar-thumb');
        
        let updateScrollbar = () => {
            let scrollWidth = container.scrollWidth;
            let clientWidth = container.clientWidth;
            
            if (scrollWidth > clientWidth) 
            {
                scrollbar.style.display = 'block';
                
                let thumbWidth = (clientWidth / scrollWidth) * 100;
                scrollThumb.style.width = `${thumbWidth}%`;
                
                let scrollLeft = container.scrollLeft;
                let maxScrollLeft = scrollWidth - clientWidth;
                let thumbPosition = (scrollLeft / maxScrollLeft) * (100 - thumbWidth);
                scrollThumb.style.left = `${thumbPosition}%`;
            } 
            else 
            {
                scrollbar.style.display = 'none';
            }
        };

        container.addEventListener('scroll', () => {
            let scrollWidth = container.scrollWidth;
            let clientWidth = container.clientWidth;
            let scrollLeft = container.scrollLeft;
            let maxScrollLeft = scrollWidth - clientWidth;
            let thumbWidth = parseFloat(scrollThumb.style.width);
            
            let thumbPosition = (scrollLeft / maxScrollLeft) * (100 - thumbWidth);
            scrollThumb.style.left = `${thumbPosition}%`;
        });

        let isDragging = false;
        
        scrollThumb.addEventListener('mousedown', (e) => {
            isDragging = true;
            e.preventDefault();
        });
        
        document.addEventListener('mousemove', (e) => {
            if (!isDragging)
            {
                return;
            }
            
            let scrollWidth = container.scrollWidth;
            let clientWidth = container.clientWidth;
            let maxScrollLeft = scrollWidth - clientWidth;
            let scrollbarRect = scrollbar.getBoundingClientRect();
            let thumbWidth = parseFloat(scrollThumb.style.width);
            
            let position = (e.clientX - scrollbarRect.left) / scrollbarRect.width;
            position = Math.max(0, Math.min(position, 1 - thumbWidth / 100));
            
            let scrollLeft = position * maxScrollLeft / (1 - thumbWidth / 100);
            container.scrollLeft = scrollLeft;
            
            scrollThumb.style.left = `${position * 100}%`;
        });
        
        document.addEventListener('mouseup', () => {
            isDragging = false;
        });

        scrollbar.addEventListener('click', (e) => {
            if (e.target === scrollThumb)
            {
                return;
            }
            
            let scrollWidth = container.scrollWidth;
            let clientWidth = container.clientWidth;
            let maxScrollLeft = scrollWidth - clientWidth;
            let scrollbarRect = scrollbar.getBoundingClientRect();
            let thumbWidth = parseFloat(scrollThumb.style.width);
            
            let position = (e.clientX - scrollbarRect.left) / scrollbarRect.width;
            position = Math.max(0, Math.min(position, 1 - thumbWidth / 100));
            
            let scrollLeft = position * maxScrollLeft / (1 - thumbWidth / 100);
            container.scrollLeft = scrollLeft;
        });

        window.addEventListener('resize', updateScrollbar);
        
        updateScrollbar();
    });
}

document.addEventListener('DOMContentLoaded', function() 
{
    initScrollbars();

    let lastScrollTop = 0;
    let navbar = document.querySelector('.navbar');
    let dropdownMenus = document.querySelectorAll('.dropdown-menu');

    window.addEventListener('scroll', function () 
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

    let carBrandsList = document.getElementById('carBrandsList');
    let popularPartsList = document.getElementById('popularParts');

    function filterItems(container, searchValue, noResultsId) 
    {
        let items = container.querySelectorAll('.scrollable-item');
        let visibleCount = 0;

        items.forEach(item => {
            let title = item.querySelector('.card-title') ? item.querySelector('.card-title').textContent.toLowerCase() : '';
            let isVisible = title.includes(searchValue.toLowerCase());

            if (isVisible) 
            {
                item.style.display = 'block';
                visibleCount++;
            } 
            else 
            {
                item.style.display = 'none';
            }
        });

        let noResultsMessage = document.getElementById(noResultsId);

        if (noResultsMessage) 
        {
            if (visibleCount === 0)
            {
                noResultsMessage.style.display = 'flex';
            }
            else
            {
                noResultsMessage.style.display = 'none';
            }
        }

        initScrollbars();
    }

    document.getElementById('catalogSearchForm').addEventListener('submit', function(event) 
    {
        event.preventDefault();

        let searchValue = document.getElementById('catalogSearchInput').value.toLowerCase();

        filterItems(carBrandsList.querySelector('#carBrandsBlock'), searchValue, 'no-results-brands');
        filterItems(popularPartsList.querySelector('#partsContainer'), searchValue, 'no-results-parts');
    });
    
    document.getElementById('brandSearch').addEventListener('input', function() 
    {
        filterItems(carBrandsList.querySelector('#carBrandsBlock'), this.value, 'no-results-brands');
    });
    
    document.getElementById('partsSearch').addEventListener('input', function() 
    {
        filterItems(popularPartsList.querySelector('#partsContainer'), this.value, 'no-results-parts');
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => 
    {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
    
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    $(document).on('click', function (e) 
    {
        if (!$(e.target).closest('.dropdown').length) 
        {
            $('.dropdown-menu').removeClass('show');
        }
    });

    let discountCheckbox = document.getElementById('discountCardCheck');

    if (discountCheckbox) 
    {
        discountCheckbox.addEventListener('change', function() 
        {
            let group = document.getElementById('discountCardNumberGroup');
            
            if (group) 
            {
                if (this.checked) 
                {
                    group.style.display = 'block';
                } 
                else 
                {
                    group.style.display = 'none';
                }
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

        let observer = new IntersectionObserver((entries, observer) => 
        {
            entries.forEach(entry => {
                if (entry.isIntersecting) 
                {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        observer.observe(aboutUsSection);
    }

    function isInViewport(element) 
    {
        let rect = element.getBoundingClientRect();
        return (
        rect.top <= window.innerHeight && rect.bottom >= 0
        );
    }
    
    function onScroll() 
    {
        let aboutUsSection = document.getElementById('aboutUs');
    
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
});