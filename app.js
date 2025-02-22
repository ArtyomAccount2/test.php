document.addEventListener('DOMContentLoaded', function() 
{
    let carBrandsList = document.getElementById('carBrandsList');
    let popularPartsList = document.getElementById('popularParts');

    let isScrolling = false;

    function cloneItems(container, count) 
    {
        let items = container.querySelectorAll('.scrollable-item');

        for (let i = 0; i < count; i++) 
        {
            items.forEach(item => {
                let clone = item.cloneNode(true);
                container.appendChild(clone);
            });
        }
    }

    cloneItems(carBrandsList.querySelector('#carBrandsBlock'), 3);
    cloneItems(popularPartsList.querySelector('#partsContainer'), 3);

    function scrollContainer(container, direction) 
    {
        if (isScrolling) 
        {
            return;
        }

        isScrolling = true;

        let itemWidth = container.querySelector('.scrollable-item').offsetWidth;
        container.scrollBy({ left: direction * itemWidth, behavior: 'smooth' });

        setTimeout(() => {
            if (direction < 0 && container.scrollLeft <= 0) 
            {
                container.scrollLeft = container.scrollWidth - container.clientWidth;
            } 
            else if (direction > 0 && container.scrollLeft >= container.scrollWidth - container.clientWidth) 
            {
                container.scrollLeft = 0;
            }

            isScrolling = false;
        }, 500);
    }

    document.getElementById('carBrandsListLeft').addEventListener('click', () => 
    {
        scrollContainer(carBrandsList, -1);
    });

    document.getElementById('carBrandsListRight').addEventListener('click', () => 
    {
        scrollContainer(carBrandsList, 1);
    });

    document.getElementById('popularPartsLeft').addEventListener('click', () => 
    {
        scrollContainer(popularPartsList, -1);
    });

    document.getElementById('popularPartsRight').addEventListener('click', () => 
    {
        scrollContainer(popularPartsList, 1);
    });

    function filterItems(container, searchValue) 
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
    
        let noResultsMessage = document.getElementById('no-results');
    
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
    
    document.getElementById('brandSearch').addEventListener('input', function() 
    {
        if (this.value.trim() === "") 
        {
            carBrandsList.querySelectorAll('.scrollable-item').forEach(item => {
                item.style.display = 'block';
            });
        }
        filterItems(carBrandsList.querySelector('#carBrandsBlock'), this.value);
    });
    
    document.getElementById('partsSearch').addEventListener('input', function() 
    {
        if (this.value.trim() === "") 
        {
            popularPartsList.querySelectorAll('.scrollable-item').forEach(item => {
                item.style.display = 'block';
            });
        }
        filterItems(popularPartsList.querySelector('#partsContainer'), this.value);
    });

    document.getElementById('brandSearch').addEventListener('input', function() 
    {
        filterItems(carBrandsList.querySelector('#carBrandsBlock'), this.value);
    });

    document.getElementById('partsSearch').addEventListener('input', function() 
    {
        filterItems(popularPartsList.querySelector('#partsContainer'), this.value);
    });
});