document.addEventListener('DOMContentLoaded', function() 
{
    let carBrandsList = document.getElementById('carBrandsList');
    let popularPartsList = document.getElementById('popularParts');

    let isScrolling = false;

    function scrollContainer(container, direction) 
    {
        if (isScrolling) 
        {
            return;
        }

        isScrolling = true;

        let itemWidth = container.querySelector('.scrollable-item').offsetWidth;
        let newScrollLeft = container.scrollLeft + (direction * itemWidth);

        if (newScrollLeft < 0 || newScrollLeft > container.scrollWidth - container.clientWidth) 
        {
            isScrolling = false;
            return;
        }

        container.scrollBy({ left: direction * itemWidth, behavior: 'smooth' });

        setTimeout(() => {
            isScrolling = false;
        }, 500);
    }

    document.getElementById('carBrandsListLeft').addEventListener('click', () => {
        scrollContainer(carBrandsList, -1);
    });

    document.getElementById('carBrandsListRight').addEventListener('click', () => {
        scrollContainer(carBrandsList, 1);
    });

    document.getElementById('popularPartsLeft').addEventListener('click', () => {
        scrollContainer(popularPartsList, -1);
    });

    document.getElementById('popularPartsRight').addEventListener('click', () => {
        scrollContainer(popularPartsList, 1);
    });

    function filterItems(container, searchValue, noResultsId) 
    {
        let items = container.querySelectorAll('.scrollable-item');
        let visibleCount = 0;

        items.forEach(item => {
            let title = item.querySelector('.card-title') ? item.querySelector('.card-title').textContent.toLowerCase() : '';

            let isVisible = title.includes(searchValue);

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
});