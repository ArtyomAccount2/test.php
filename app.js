document.addEventListener('DOMContentLoaded', function() 
{
    let popupMenu = document.querySelector('.popup-menu');
    let closeButton = document.querySelector('.close');
    let menuButton = document.querySelector('.menu');

    menuButton.addEventListener('click', function() 
    {
        popupMenu.style.display = 'flex';
        popupMenu.classList.remove('slide-out');
        popupMenu.classList.add('slide-in');
    });

    closeButton.addEventListener('click', function() 
    {
        popupMenu.classList.add('slide-out');
        setTimeout(() => {
            popupMenu.style.display = 'none';
            popupMenu.classList.remove('slide-out');
        }, 300);
    });

    let partSearchForm = document.querySelector('.head-center form');
    let catalogSearchForm = document.querySelector('.body-block3 form');

    let partsData = [
        { name: 'Коленчатый вал', article: '1234' },
        { name: 'Тормозные колодки', article: '5678' },
        { name: 'Прокладки двигателя', article: '91011' },
        { name: 'Стабилизатор', article: '1213' },
    ];

    let catalogData = [
        { name: 'Топливный насос', catalog: 'Топливная система' },
        { name: 'Тормозной цилиндр', catalog: 'Тормозная система' },
    ];

    partSearchForm.addEventListener('submit', function(event) 
    {
        event.preventDefault();

        let query = event.target.query.value.trim();
        
        if (!query) 
        {
            alert('Введите артикул запчасти или VIN код');
            return;
        }
        
        let result = partsData.filter(part => part.article === query || part.name.toLowerCase().includes(query.toLowerCase()));
        
        if (result.length > 0) 
        {
            alert(`Найдено: ${result.map(part => part.name).join(', ')}`);
        } 

        else 
        {
            alert('Запчасть не найдена');
        }
    });

    catalogSearchForm.addEventListener('submit', function(event) 
    {
        event.preventDefault();

        let query = event.target.catalog.value.trim();
        let result = catalogData.filter(item => item.name.toLowerCase().includes(query.toLowerCase()));
        
        if (result.length > 0) 
        {
            alert(`Найдено в каталоге: ${result.map(item => item.name).join(', ')}`);
        } 
        else 
        {
            alert('В каталоге ничего не найдено');
        }
    });

    let loginButton = document.querySelector('.login');
    let registerButton = document.querySelector('.register');
    let popupLogin = document.querySelector('.popup-login');
    let popupRegister = document.querySelector('.popup-register');
    let closeLogin = document.querySelector('.close-login');
    let closeRegister = document.querySelector('.close-register');

    loginButton.addEventListener('click', (e) => {
        e.preventDefault();
        popupLogin.style.display = 'flex';
        popupLogin.classList.remove('slide-out');
        popupLogin.classList.add('slide-in');
    });

    registerButton.addEventListener('click', (e) => {
        e.preventDefault();
        popupRegister.style.display = 'flex';
        popupRegister.classList.remove('slide-out');
        popupRegister.classList.add('slide-in');
    });

    closeLogin.addEventListener('click', function() 
    {
        popupLogin.classList.add('slide-out');
        setTimeout(() => {
            popupLogin.style.display = 'none';
            popupLogin.classList.remove('slide-out');
        }, 300);
    });
    
    closeRegister.addEventListener('click', function() 
    {
        popupRegister.classList.add('slide-out');
        setTimeout(() => {
            popupRegister.style.display = 'none';
            popupRegister.classList.remove('slide-out');
        }, 300);
    });

    window.addEventListener('click', (e) => 
    {
        if (e.target === popupLogin) 
        {
            popupLogin.classList.add('slide-out');
            setTimeout(() => {
                popupLogin.style.display = 'none';
                popupLogin.classList.remove('slide-out');
            }, 300);
        }

        if (e.target === popupRegister) 
        {
            popupRegister.classList.add('slide-out');
            setTimeout(() => {
                popupRegister.style.display = 'none';
                popupRegister.classList.remove('slide-out');
            }, 300);
        }
    });
});