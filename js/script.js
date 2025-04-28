"use strict";

document.addEventListener('DOMContentLoaded', function() 
{
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

            updateScrollbar(container);
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

        function updateScrollbar(container) 
        {
            let scrollbar = container.closest('.row').nextElementSibling;
            let visibleCount = container.querySelectorAll('.scrollable-item[style*="display: block"]').length;
        
            if (scrollbar) 
            {
                let scrollThumb = scrollbar.querySelector('.scrollbar-thumb');
        
                if (scrollThumb && visibleCount > 0) 
                {
                    scrollbar.style.display = 'flex';
                    let scrollWidth = (container.scrollWidth / container.scrollHeight) * 100;
                    scrollThumb.style.width = `${scrollWidth}%`;
                } 
                else 
                {
                    scrollbar.style.display = 'none';
                }
            }
        }
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