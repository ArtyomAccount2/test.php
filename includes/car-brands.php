<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $login = $_POST['login'];
    $password = $_POST['password'];
    $redirect_url = $_POST['redirect_url'] ?? $_SERVER['REQUEST_URI'];

    if (strtolower($login) === 'admin' && strtolower($password) === 'admin') 
    {
        $_SESSION['loggedin'] = true;
        $_SESSION['user'] = 'admin';
        unset($_SESSION['login_error']);
        unset($_SESSION['error_message']);
        header("Location: ../admin.php");
        exit();
    }
    else
    {
        $stmt = $conn->prepare("SELECT * FROM users WHERE LOWER(login_users) = LOWER(?) AND LOWER(password_users) = LOWER(?)");
        $stmt->bind_param("ss", $login, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) 
        {
            $row = $result->fetch_assoc();
            $_SESSION['loggedin'] = true;
            $_SESSION['user'] = !empty($row['surname_users']) ? $row['surname_users'] . " " . $row['name_users'] . " " . $row['patronymic_users'] : $row['person_users'];
            unset($_SESSION['login_error']);
            unset($_SESSION['error_message']);
            header("Location: " . $redirect_url);
            exit();
        } 
        else 
        {
            $_SESSION['login_error'] = true;
            $_SESSION['error_message'] = "Неверный логин или пароль!";
            $_SESSION['form_data'] = $_POST;
            header("Location: " . $redirect_url);
            exit();
        }
    }
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$carBrands = [
    [
        'name' => 'Acura',
        'country' => 'Япония',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Японский премиальный бренд, принадлежащий Honda',
        'models' => ['MDX', 'RDX', 'TLX', 'NSX'],
        'image' => '../img/Stamps/Acura.png'
    ],
    [
        'name' => 'Aixam',
        'country' => 'Франция',
        'category' => 'special',
        'category_name' => 'Микрокары',
        'description' => 'Французский производитель микроавтомобилей',
        'models' => ['City', 'Crossover', 'E-City'],
        'image' => '../img/Stamps/Aixam.png'
    ],
    [
        'name' => 'Alfa Romeo',
        'country' => 'Италия',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Итальянский производитель спортивных автомобилей',
        'models' => ['Giulia', 'Stelvio', 'Tonale'],
        'image' => '../img/Stamps/Alfa Romeo.png'
    ],
    [
        'name' => 'Aston Martin',
        'country' => 'Великобритания',
        'category' => 'luxury',
        'category_name' => 'Люкс',
        'description' => 'Британский производитель роскошных спортивных автомобилей',
        'models' => ['DB11', 'Vantage', 'DBS', 'Valhalla'],
        'image' => '../img/Stamps/Aston Martin.png'
    ],
    [
        'name' => 'Audi',
        'country' => 'Германия',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Немецкий производитель автомобилей премиум-класса',
        'models' => ['A4', 'A6', 'Q5', 'Q7', 'TT'],
        'image' => '../img/Stamps/Audi.png'
    ],
    [
        'name' => 'BMW',
        'country' => 'Германия',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Немецкий производитель автомобилей и мотоциклов',
        'models' => ['3 серии', '5 серии', 'X5', 'X3', 'i8'],
        'image' => '../img/Stamps/BMW.png'
    ],
    [
        'name' => 'Bentley',
        'country' => 'Великобритания',
        'category' => 'luxury',
        'category_name' => 'Люкс',
        'description' => 'Британский производитель роскошных автомобилей',
        'models' => ['Continental', 'Flying Spur', 'Bentayga'],
        'image' => '../img/Stamps/Bentley.png'
    ],
    [
        'name' => 'Buick',
        'country' => 'США',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Американский бренд автомобилей премиум-класса',
        'models' => ['Enclave', 'Encore', 'Regal'],
        'image' => '../img/Stamps/Buick.png'
    ],
    [
        'name' => 'Cadillac',
        'country' => 'США',
        'category' => 'luxury',
        'category_name' => 'Люкс',
        'description' => 'Американский производитель автомобилей класса люкс',
        'models' => ['Escalade', 'XT5', 'CT5', 'Lyriq'],
        'image' => '../img/Stamps/Cadillac.png'
    ],
    [
        'name' => 'Chevrolet',
        'country' => 'США',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Американский производитель массовых автомобилей',
        'models' => ['Camaro', 'Malibu', 'Tahoe', 'Equinox'],
        'image' => '../img/Stamps/Chevrolet.png'
    ],
    [
        'name' => 'Chrysler',
        'country' => 'США',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Американский производитель автомобилей',
        'models' => ['Pacifica', '300', 'Voyager'],
        'image' => '../img/Stamps/Chrysler.png'
    ],
    [
        'name' => 'Dodge',
        'country' => 'США',
        'category' => 'sport',
        'category_name' => 'Спорт',
        'description' => 'Американский производитель спортивных автомобилей',
        'models' => ['Charger', 'Challenger', 'Durango'],
        'image' => '../img/Stamps/Dodge.png'
    ],
    [
        'name' => 'Fiat',
        'country' => 'Италия',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Итальянский производитель автомобилей',
        'models' => ['500', 'Panda', 'Tipo', 'Doblo'],
        'image' => '../img/Stamps/Fiat.png'
    ],
    [
        'name' => 'Ford',
        'country' => 'США',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Американский производитель автомобилей',
        'models' => ['Focus', 'Fiesta', 'Mustang', 'Explorer'],
        'image' => '../img/Stamps/Ford.png'
    ],
    [
        'name' => 'Gaz',
        'country' => 'Россия',
        'category' => 'commercial',
        'category_name' => 'Коммерческий',
        'description' => 'Российский производитель грузовых и легковых автомобилей',
        'models' => ['Волга', 'Газель', 'Соболь'],
        'image' => '../img/Stamps/Gaz.png'
    ],
    [
        'name' => 'Honda',
        'country' => 'Япония',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Японский производитель автомобилей и мотоциклов',
        'models' => ['Civic', 'Accord', 'CR-V', 'Pilot'],
        'image' => '../img/Stamps/Honda.png'
    ],
    [
        'name' => 'Hummer',
        'country' => 'США',
        'category' => 'offroad',
        'category_name' => 'Внедорожник',
        'description' => 'Американский бренд внедорожников',
        'models' => ['H2', 'H3', 'EV'],
        'image' => '../img/Stamps/Hummer.png'
    ],
    [
        'name' => 'Hyundai',
        'country' => 'Корея',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Южнокорейский производитель автомобилей',
        'models' => ['Solaris', 'Tucson', 'Santa Fe', 'Elantra'],
        'image' => '../img/Stamps/Hyundai.png'
    ],
    [
        'name' => 'Infiniti',
        'country' => 'Япония',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Японский премиальный бренд, принадлежащий Nissan',
        'models' => ['Q50', 'QX60', 'QX80'],
        'image' => '../img/Stamps/Infiniti.png'
    ],
    [
        'name' => 'Jaguar',
        'country' => 'Великобритания',
        'category' => 'luxury',
        'category_name' => 'Люкс',
        'description' => 'Британский производитель роскошных автомобилей',
        'models' => ['XF', 'F-Pace', 'E-Pace', 'I-Pace'],
        'image' => '../img/Stamps/Jaguar.png'
    ],
    [
        'name' => 'Jeep',
        'country' => 'США',
        'category' => 'offroad',
        'category_name' => 'Внедорожник',
        'description' => 'Американский производитель внедорожников',
        'models' => ['Wrangler', 'Grand Cherokee', 'Renegade'],
        'image' => '../img/Stamps/Jeep.png'
    ],
    [
        'name' => 'Kia',
        'country' => 'Корея',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Южнокорейский производитель автомобилей',
        'models' => ['Rio', 'Sportage', 'Sorento', 'K5'],
        'image' => '../img/Stamps/Kia.png'
    ],
    [
        'name' => 'Lada',
        'country' => 'Россия',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Российский производитель автомобилей',
        'models' => ['Vesta', 'Granta', 'Niva', 'XRAY'],
        'image' => '../img/Stamps/Lada.png'
    ],
    [
        'name' => 'Lamborghini',
        'country' => 'Италия',
        'category' => 'luxury',
        'category_name' => 'Люкс',
        'description' => 'Итальянский производитель суперкаров',
        'models' => ['Aventador', 'Huracan', 'Urus'],
        'image' => '../img/Stamps/Lamborghini.png'
    ],
    [
        'name' => 'Lancia',
        'country' => 'Италия',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Итальянский производитель автомобилей',
        'models' => ['Ypsilon', 'Delta', 'Thema'],
        'image' => '../img/Stamps/Lancia.png'
    ],
    [
        'name' => 'Land Rover',
        'country' => 'Великобритания',
        'category' => 'offroad',
        'category_name' => 'Внедорожник',
        'description' => 'Британский производитель внедорожников',
        'models' => ['Range Rover', 'Discovery', 'Defender'],
        'image' => '../img/Stamps/Land Rover.png'
    ],
    [
        'name' => 'Lexus',
        'country' => 'Япония',
        'category' => 'luxury',
        'category_name' => 'Люкс',
        'description' => 'Японский бренд автомобилей класса люкс, принадлежащий Toyota',
        'models' => ['RX', 'NX', 'ES', 'LS'],
        'image' => '../img/Stamps/Lexus.png'
    ],
    [
        'name' => 'Lotus',
        'country' => 'Великобритания',
        'category' => 'sport',
        'category_name' => 'Спорт',
        'description' => 'Британский производитель спортивных автомобилей',
        'models' => ['Evora', 'Emira', 'Elise'],
        'image' => '../img/Stamps/Lotus.png'
    ],
    [
        'name' => 'Mazda',
        'country' => 'Япония',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Японский производитель автомобилей',
        'models' => ['3', '6', 'CX-5', 'MX-5'],
        'image' => '../img/Stamps/Mazda.png'
    ],
    [
        'name' => 'Mercedes-Benz',
        'country' => 'Германия',
        'category' => 'luxury',
        'category_name' => 'Люкс',
        'description' => 'Немецкий производитель автомобилей премиум-класса',
        'models' => ['C-класс', 'E-класс', 'S-класс', 'GLE'],
        'image' => '../img/Stamps/Mercedes.png'
    ],
    [
        'name' => 'Mini',
        'country' => 'Великобритания',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Британский производитель малолитражных автомобилей',
        'models' => ['Cooper', 'Countryman', 'Clubman'],
        'image' => '../img/Stamps/Mini.png'
    ],
    [
        'name' => 'Mitsubishi',
        'country' => 'Япония',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Японский производитель автомобилей',
        'models' => ['Outlander', 'Pajero Sport', 'Lancer'],
        'image' => '../img/Stamps/Mitsubishi.png'
    ],
    [
        'name' => 'Nissan',
        'country' => 'Япония',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Японский производитель автомобилей',
        'models' => ['Qashqai', 'X-Trail', 'Note', 'GT-R'],
        'image' => '../img/Stamps/Nissan.png'
    ],
    [
        'name' => 'Opel',
        'country' => 'Германия',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Немецкий производитель автомобилей',
        'models' => ['Astra', 'Corsa', 'Insignia', 'Mokka'],
        'image' => '../img/Stamps/Opel.png'
    ],
    [
        'name' => 'Peugeot',
        'country' => 'Франция',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Французский производитель автомобилей',
        'models' => ['308', '3008', '508', '2008'],
        'image' => '../img/Stamps/Peugeot.png'
    ],
    [
        'name' => 'Porsche',
        'country' => 'Германия',
        'category' => 'luxury',
        'category_name' => 'Люкс',
        'description' => 'Немецкий производитель спортивных автомобилей',
        'models' => ['911', 'Cayenne', 'Panamera', 'Macan'],
        'image' => '../img/Stamps/Porsche.png'
    ],
    [
        'name' => 'Renault',
        'country' => 'Франция',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Французский производитель автомобилей',
        'models' => ['Logan', 'Sandero', 'Duster', 'Kaptur'],
        'image' => '../img/Stamps/Renault.png'
    ],
    [
        'name' => 'Skoda',
        'country' => 'Чехия',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Чешский производитель автомобилей',
        'models' => ['Octavia', 'Kodiaq', 'Karoq', 'Superb'],
        'image' => '../img/Stamps/Skoda.png'
    ],
    [
        'name' => 'Subaru',
        'country' => 'Япония',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Японский производитель автомобилей',
        'models' => ['Forester', 'Outback', 'Impreza', 'XV'],
        'image' => '../img/Stamps/Subaru.png'
    ],
    [
        'name' => 'Suzuki',
        'country' => 'Япония',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Японский производитель автомобилей',
        'models' => ['Vitara', 'Swift', 'SX4', 'Jimny'],
        'image' => '../img/Stamps/Suzuki.png'
    ],
    [
        'name' => 'Tesla',
        'country' => 'США',
        'category' => 'electric',
        'category_name' => 'Электрический',
        'description' => 'Американский производитель электромобилей',
        'models' => ['Model 3', 'Model S', 'Model X', 'Model Y'],
        'image' => '../img/Stamps/Tesla.png'
    ],
    [
        'name' => 'Toyota',
        'country' => 'Япония',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Японский производитель автомобилей',
        'models' => ['Camry', 'RAV4', 'Land Cruiser', 'Corolla'],
        'image' => '../img/Stamps/Toyota.png'
    ],
    [
        'name' => 'Volkswagen',
        'country' => 'Германия',
        'category' => 'mass',
        'category_name' => 'Массовый',
        'description' => 'Немецкий производитель автомобилей',
        'models' => ['Passat', 'Tiguan', 'Polo', 'Golf'],
        'image' => '../img/Stamps/VW.png'
    ],
    [
        'name' => 'Volvo',
        'country' => 'Швеция',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Шведский производитель автомобилей',
        'models' => ['XC90', 'XC60', 'S90', 'V90'],
        'image' => '../img/Stamps/Volvo.png'
    ],
    [
        'name' => 'UAZ',
        'country' => 'Россия',
        'category' => 'offroad',
        'category_name' => 'Внедорожник',
        'description' => 'Российский производитель внедорожников',
        'models' => ['Patriot', 'Hunter', 'Pickup', 'Profi'],
        'image' => '../img/Stamps/UAZ.png'
    ]
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все марки автомобилей - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/brands-styles.css">
    <link rel="stylesheet" href="../css/car-brands-styles.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() 
    {
        <?php 
        if (isset($_SESSION['login_error'])) 
        { 
        ?>
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            
            <?php unset($_SESSION['login_error']); ?>
        <?php 
        } 
        ?>

        let CARDS_PER_PAGE = 12;
        let currentPage = 1;
        let totalPages = 1;
        let allBrandItems = document.querySelectorAll('.car-brand-item');
        let visibleBrandItems = [];
        let resizeTimeout;
        
        window.addEventListener('resize', function() 
        {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(equalizeBrandCards, 250);
        });

        let filterButtons = document.querySelectorAll('.filter-btn');
        let brandItems = document.querySelectorAll('.car-brand-item');
        let searchInput = document.getElementById('brandSearch');
        let searchClear = document.querySelector('.search-clear');
        let paginationElement = document.getElementById('pagination');
        let noResultsElement = document.getElementById('noResults');
        let modelsGridElement = document.getElementById('modelsGrid');

        function initializeCounters() 
        {
            updateCategoryCounters('all', '');
        }
        
        function updateCategoryCounters(activeFilter, searchText) 
        {
            let categoryCounts = {
                'all': 0,
                'premium': 0,
                'luxury': 0,
                'mass': 0,
                'offroad': 0,
                'sport': 0,
                'electric': 0,
                'commercial': 0,
                'special': 0
            };

            brandItems.forEach(item => {
                let brandName = item.querySelector('.car-brand-name').textContent.toLowerCase();
                let brandDescription = item.querySelector('.car-brand-description').textContent.toLowerCase();
                let itemCategory = item.getAttribute('data-category');
                
                let searchMatch = !searchText || brandName.includes(searchText) || brandDescription.includes(searchText);
                
                if (searchMatch) 
                {
                    categoryCounts[itemCategory]++;
                    categoryCounts.all++;
                }
            });

            filterButtons.forEach(button => {
                let filter = button.getAttribute('data-filter');
                let badge = button.querySelector('.badge');

                if (badge) 
                {
                    badge.textContent = categoryCounts[filter];
                }
            });
        }
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() 
            {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                let filter = this.getAttribute('data-filter');
                filterBrands(filter, searchInput.value.toLowerCase());
            });
        });
        
        searchInput.addEventListener('input', function() 
        {
            let searchText = this.value.toLowerCase();
            searchClear.style.display = searchText ? 'block' : 'none';
            
            let activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
            filterBrands(activeFilter, searchText);
        });
        
        searchClear.addEventListener('click', function() 
        {
            searchInput.value = '';
            searchClear.style.display = 'none';
            let activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
            filterBrands(activeFilter, '');
        });
        
        function filterBrands(category, searchText) 
        {
            visibleBrandItems = [];
            
            brandItems.forEach(item => {
                let brandName = item.querySelector('.car-brand-name').textContent.toLowerCase();
                let brandDescription = item.querySelector('.car-brand-description').textContent.toLowerCase();
                let itemCategory = item.getAttribute('data-category');
                
                let categoryMatch = category === 'all' || itemCategory === category;
                let searchMatch = !searchText || brandName.includes(searchText) || brandDescription.includes(searchText);
                
                if (categoryMatch && searchMatch) 
                {
                    visibleBrandItems.push(item);
                }
            });
            
            currentPage = 1;
            updatePagination();
            showPage(currentPage);
            updateCategoryCounters(category, searchText);
            setTimeout(equalizeBrandCards, 350);
        }

        function updatePagination() 
        {
            totalPages = Math.ceil(visibleBrandItems.length / CARDS_PER_PAGE);
            paginationElement.innerHTML = '';
            
            if (visibleBrandItems.length <= CARDS_PER_PAGE) 
            {
                paginationElement.style.display = 'none';
            } 
            else 
            {
                paginationElement.style.display = 'flex';
            }

            if (visibleBrandItems.length === 0) 
            {
                noResultsElement.style.display = 'block';
                modelsGridElement.style.display = 'none';
            } 
            else 
            {
                noResultsElement.style.display = 'none';
                modelsGridElement.style.display = 'grid';
            }
            
            if (totalPages > 1) 
            {
                let prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Предыдущая">‹</a>`;
                prevLi.addEventListener('click', (e) => {
                    e.preventDefault();

                    if (currentPage > 1) 
                    {
                        currentPage--;
                        showPage(currentPage);
                    }
                });
                paginationElement.appendChild(prevLi);

                for (let i = 1; i <= totalPages; i++) 
                {
                    let li = document.createElement('li');
                    li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    li.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = i;
                        showPage(currentPage);
                    });
                    paginationElement.appendChild(li);
                }
                
                let nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Следующая">›</a>`;
                nextLi.addEventListener('click', (e) => {
                    e.preventDefault();

                    if (currentPage < totalPages) 
                    {
                        currentPage++;
                        showPage(currentPage);
                    }
                });
                paginationElement.appendChild(nextLi);
            }
        }
        
        function showPage(page) 
        {
            brandItems.forEach(item => {
                item.style.display = 'none';
                item.style.opacity = '0';
                item.style.transform = 'scale(0.8)';
            });
            
            let startIndex = (page - 1) * CARDS_PER_PAGE;
            let endIndex = startIndex + CARDS_PER_PAGE;
            
            visibleBrandItems.slice(startIndex, endIndex).forEach((item, index) => {
                setTimeout(() => {
                    item.style.display = 'block';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, 50);
                }, index * 50);
            });
            
            updatePagination();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function initializePage() 
        {
            initializeCounters();
            filterBrands('all', '');
        }

        window.addEventListener('load', initializePage);
        window.addEventListener('resize', equalizeBrandCards);
        setTimeout(equalizeBrandCards, 100);
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-4">
    <div class="hero-section text-center mb-4" style="padding-top: 105px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Все марки автомобилей</h1>
        <p class="lead text-muted mb-3">Подберите запчасти для вашего автомобиля по марке</p>
        <div class="stats-row d-flex justify-content-center gap-4 flex-wrap mb-4">
            <div class="stat-item">
                <div class="stat-number text-primary fw-bold fs-3"><?php echo count($carBrands); ?>+</div>
                <div class="stat-label text-muted">марок</div>
            </div>
            <div class="stat-item">
                <div class="stat-number text-primary fw-bold fs-3">50 000+</div>
                <div class="stat-label text-muted">запчастей</div>
            </div>
            <div class="stat-item">
                <div class="stat-number text-primary fw-bold fs-3">15 лет</div>
                <div class="stat-label text-muted">опыта</div>
            </div>
        </div>
    </div>
    <div class="search-section mb-4">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="search-container position-relative">
                    <input type="text" id="brandSearch" placeholder="Поиск по маркам автомобилей..." class="form-control search-input">
                    <button class="btn btn-link search-clear" type="button" style="display: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 5;">
                        <i class="bi bi-x"></i>
                    </button>
                    <i class="bi bi-search search-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="filters-section mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <button class="btn btn-outline-primary filter-btn active" data-filter="all">Все <span class="badge bg-primary ms-1"><?php echo count($carBrands); ?></span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="premium">Премиум <span class="badge bg-primary ms-1">10</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="luxury">Люкс <span class="badge bg-primary ms-1">8</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="mass">Массовые <span class="badge bg-primary ms-1">15</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="offroad">Внедорожники <span class="badge bg-primary ms-1">5</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="sport">Спорт <span class="badge bg-primary ms-1">3</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="electric">Электрические <span class="badge bg-primary ms-1">1</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="commercial">Коммерческие <span class="badge bg-primary ms-1">2</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="special">Специальные <span class="badge bg-primary ms-1">1</span></button>
        </div>
    </div>
    <div class="brands-grid-section mb-5">
        <div class="row g-3 w-100" id="modelsGrid" style="display: grid;">
            <?php 
            foreach ($carBrands as $brand)
            {
            ?>
            <div class="col-xl-3 col-lg-3 col-md-6 car-brand-item w-100" data-category="<?php echo $brand['category']; ?>">
                <div class="car-brand-card">
                    <div class="car-brand-logo-container">
                        <img src="<?php echo $brand['image']; ?>" alt="<?php echo $brand['name']; ?>" class="car-brand-logo">
                    </div>
                    <div class="car-brand-info">
                        <h5 class="car-brand-name"><?php echo $brand['name']; ?></h5>
                        <p class="car-brand-country">
                            <i class="bi bi-geo-alt me-1"></i><?php echo $brand['country']; ?>
                        </p>
                        <div class="car-brand-category <?php echo $brand['category']; ?>">
                            <?php echo $brand['category_name']; ?>
                        </div>
                        <p class="car-brand-description"><?php echo $brand['description']; ?></p>
                        <div class="car-brand-models">
                            <small class="text-muted d-block mb-1">Популярные модели:</small>
                            <div class="models-tags">
                                <?php 
                                foreach ($brand['models'] as $model)
                                {
                                ?>
                                <span class="model-tag"><?php echo $model; ?></span>
                                <?php 
                                }
                                ?>
                            </div>
                        </div>
                        <a href="../includes/assortment.php?search=<?php echo urlencode(strtolower($brand['name'])); ?>" class="btn btn-outline-primary w-100 mt-3">
                            <i class="bi bi-search me-1"></i>Найти запчасти
                        </a>
                    </div>
                </div>
            </div>
            <?php 
            } 
            ?>
        </div>
        <div class="pagination-section mt-5">
            <nav aria-label="Навигация по страницам">
                <ul class="pagination justify-content-center" id="pagination">
                </ul>
            </nav>
        </div>
        <div class="no-results text-center py-5" id="noResults" style="display: none;">
            <div class="no-results-icon mb-3">
                <i class="bi bi-search display-1 text-muted"></i>
            </div>
            <h4 class="text-muted mb-3">Марки не найдены</h4>
            <p class="text-muted">Попробуйте изменить параметры поиска или фильтрации</p>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>