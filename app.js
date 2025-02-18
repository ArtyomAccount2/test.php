document.addEventListener('DOMContentLoaded', function() 
{
    let partsContainer = document.getElementById('partsContainer');
    let carBrandsBlock = document.getElementById('carBrandsBlock');

    partsContainer.innerHTML += partsContainer.innerHTML;
    carBrandsBlock.innerHTML += carBrandsBlock.innerHTML;

    let carBrandsListLeft = document.getElementById('carBrandsListLeft');
    let carBrandsListRight = document.getElementById('carBrandsListRight');
    let popularPartsLeft = document.getElementById('popularPartsLeft');
    let popularPartsRight = document.getElementById('popularPartsRight');

    function scrollLeft(container) 
    {
        container.scrollBy({ left: -265, behavior: 'smooth' });
    }

    function scrollRight(container) 
    {
        container.scrollBy({ left: 265, behavior: 'smooth' });
    }

    function resetScroll(container) 
    {
        if (container.scrollLeft <= 0) 
        {
            container.scrollLeft = container.scrollWidth / 2 - container.clientWidth / 2;
        }

        else if (container.scrollLeft >= container.scrollWidth / 2) 
        {
            container.scrollLeft = 0;
        }
    }

    carBrandsListLeft.addEventListener('click', function() 
    {
        scrollLeft(document.getElementById('carBrandsList'));
    });

    carBrandsListRight.addEventListener('click', function() 
    {
        scrollRight(document.getElementById('carBrandsList'));
    });

    popularPartsLeft.addEventListener('click', function() 
    {
        scrollLeft(document.getElementById('popularParts'));
    });

    popularPartsRight.addEventListener('click', function()
    {
        scrollRight(document.getElementById('popularParts'));
    });

    document.getElementById('popularParts').addEventListener('scroll', function() 
    {
        resetScroll(this);
    });

    document.getElementById('carBrandsList').addEventListener('scroll', function() 
    {
        resetScroll(this);
    });
});