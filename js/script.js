"use strict";

document.addEventListener('DOMContentLoaded', function() 
{
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

        let scrollbar = container.closest('.row').nextElementSibling;

        if (scrollbar) 
        {
            let isMobile = window.matchMedia("(max-width: 768px)").matches;

            if (visibleCount > 5 || !isMobile) 
            {
                scrollbar.style.display = 'block';
                let scrollThumb = scrollbar.querySelector('.scrollbar-thumb');
                
                if (scrollThumb) 
                {
                    let scrollWidth = (container.scrollWidth / visibleCount) * visibleCount;
                    scrollThumb.style.width = `${scrollWidth}px`;
                }
            } 
            else 
            {
                scrollbar.style.display = 'none';
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

    function addSwipeSupport(container) 
    {
        let hammer = new Hammer(container);
        
        hammer.on('swipeleft', function() 
        {
            let nextItem = container.querySelector('.scrollable-item:not([style*="display: none"]) + .scrollable-item');

            if (nextItem) 
            {
                nextItem.scrollIntoView({ behavior: 'smooth', inline: 'start' });
            }
        });

        hammer.on('swiperight', function() 
        {
            let previousItem = container.querySelector('.scrollable-item[style*="display: none"] + .scrollable-item');

            if (previousItem) 
            {
                previousItem.scrollIntoView({ behavior: 'smooth', inline: 'start' });
            }
        });
    }

    addSwipeSupport(carBrandsList.querySelector('#carBrandsBlock'));
    addSwipeSupport(popularPartsList.querySelector('#partsContainer'));

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
    
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
});