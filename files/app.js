"use strict";

document.getElementById('discountCardCheck').addEventListener('change', function() 
{
    if (this.checked)
    {
        document.getElementById('discountCardNumberGroup').style.display = 'block';
    }
    else
    {
        document.getElementById('discountCardNumberGroup').style.display = 'none';
    }
});