document.addEventListener('DOMContentLoaded', function() 
{
    let carBrandsList = document.getElementById('carBrandsList');
    let popularPartsList = document.getElementById('popularParts');
    
    let scrollAmount = 265;
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
        container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });

        setTimeout(() => {
            if (direction < 0 && container.scrollLeft <= 0) 
            {
                container.scrollLeft = container.scrollWidth / 2 - container.clientWidth;
            } 
            else if (direction > 0 && container.scrollLeft >= container.scrollWidth / 5) 
            {
                container.scrollLeft = 0;
            }

            isScrolling = false;
        }, 600);
    }

    document.getElementById('carBrandsListLeft').addEventListener('click', function() 
    {
        scrollContainer(carBrandsList, -4);
    });

    document.getElementById('carBrandsListRight').addEventListener('click', function() 
    {
        scrollContainer(carBrandsList, 4);
    });

    document.getElementById('popularPartsLeft').addEventListener('click', function() 
    {
        scrollContainer(popularPartsList, -4);
    });

    document.getElementById('popularPartsRight').addEventListener('click', function() 
    {
        scrollContainer(popularPartsList, 4);
    });
});