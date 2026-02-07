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

function enhanceBrandSearch($search_term, $products) 
{
    $brands_mapping = [
        'acura' => ['acura'],
        'aixam' => ['aixam'],
        'alfa romeo' => ['alfa romeo', 'alfa'],
        'alfa' => ['alfa romeo', 'alfa'],
        'aston martin' => ['aston martin', 'aston'],
        'aston' => ['aston martin', 'aston'],
        'audi' => ['audi'],
        'bmw' => ['bmw'],
        'bentley' => ['bentley'],
        'buick' => ['buick'],
        'cadillac' => ['cadillac'],
        'chevrolet' => ['chevrolet'],
        'chrysler' => ['chrysler'],
        'dodge' => ['dodge'],
        'fiat' => ['fiat'],
        'ford' => ['ford'],
        'gaz' => ['gaz'],
        'honda' => ['honda'],
        'hummer' => ['hummer'],
        'hyundai' => ['hyundai'],
        'infiniti' => ['infiniti'],
        'jaguar' => ['jaguar'],
        'jeep' => ['jeep'],
        'kia' => ['kia'],
        'lada' => ['lada'],
        'lamborghini' => ['lamborghini'],
        'lancia' => ['lancia'],
        'land rover' => ['land rover', 'land', 'rover'],
        'land' => ['land rover', 'land'],
        'rover' => ['land rover', 'rover'],
        'lexus' => ['lexus'],
        'lotus' => ['lotus']
    ];
    
    $search_lower = strtolower(trim($search_term));
    $found_products = [];

    foreach ($brands_mapping as $brand_key => $brand_variants) 
    {
        if (in_array($search_lower, $brand_variants)) 
        {
            $found_products = array_filter($products, function($product) use ($brand_key, $brand_variants) 
            {
                $title_lower = strtolower($product['title']);
                $badge_lower = isset($product['badge']) ? strtolower($product['badge']) : '';
                $badge_match = strpos($badge_lower, 'для ') !== false && (strpos($badge_lower, $brand_key) !== false);
                $title_match = false;

                foreach ($brand_variants as $variant) 
                {
                    if (strpos($title_lower, $variant) !== false) 
                    {
                        $title_match = true;
                        break;
                    }
                }
                
                return $badge_match || $title_match;
            });
            
            if (!empty($found_products)) 
            {
                break;
            }
        }
    }

    return !empty($found_products) ? $found_products : [];
}

function searchByPartCategory($search_term, $products) 
{
    $parts_mapping = [
        'коленчатый вал' => ['коленчатый вал', 'коленвал', 'коленчатый'],
        'прокладки двигателя' => ['прокладки двигателя', 'прокладки', 'прокладка двигателя'],
        'топливный насос' => ['топливный насос', 'бензонасос', 'топливный'],
        'распределительный вал' => ['распределительный вал', 'распредвал', 'распределительный'],
        'тормозной цилиндр' => ['тормозной цилиндр', 'тормозной', 'цилиндр'],
        'тормозные колодки' => ['тормозные колодки', 'колодки тормозные', 'колодки'],
        'стабилизатор' => ['стабилизатор', 'стойка стабилизатора'],
        'тормозные суппорта' => ['тормозные суппорта', 'суппорта', 'суппорт'],
        'топливный фильтр' => ['топливный фильтр', 'фильтр топливный'],
        'тормозные диски' => ['тормозные диски', 'диски тормозные', 'тормозной диск'],
        'цапфа' => ['цапфа'],
        'сальники' => ['сальники', 'сальник']
    ];
    
    $search_lower = strtolower(trim($search_term));
    $found_products = [];
    
    foreach ($parts_mapping as $part_name => $keywords) 
    {
        if (in_array($search_lower, $keywords)) 
        {
            $found_products = array_filter($products, function($product) use ($keywords) 
            {
                $title_lower = strtolower($product['title']);
                
                foreach ($keywords as $keyword) 
                {
                    if (strpos($title_lower, $keyword) !== false) 
                    {
                        return true;
                    }
                }

                return false;
            });
            
            if (!empty($found_products)) 
            {
                break;
            }
        }
    }
    
    return !empty($found_products) ? $found_products : [];
}

$items_per_page = 12;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$all_products = [
    ['category' => 'фильтры', 'title' => 'Фильтр масляный Audi A4 B8 2.0 TFSI', 'price' => 1250, 'badge' => 'Для Audi'],
    ['category' => 'тормозная система', 'title' => 'Тормозные колодки Audi A6 C7', 'price' => 3890, 'old_price' => 4500, 'badge' => 'Для Audi'],
    ['category' => 'двигатель', 'title' => 'Свечи зажигания Audi Q5 2.0 TDI', 'price' => 850, 'badge' => 'Для Audi'],
    ['category' => 'трансмиссия', 'title' => 'Сцепление Audi A3 8V', 'price' => 12500, 'badge' => 'Для Audi'],
    ['category' => 'электрика', 'title' => 'Генератор Audi A4 B9', 'price' => 15600, 'badge' => 'Для Audi'],
    ['category' => 'фильтры', 'title' => 'Воздушный фильтр Audi Q7 4L', 'price' => 2100, 'badge' => 'Для Audi'],
    ['category' => 'кузовные детали', 'title' => 'Фара передняя BMW 3 series F30', 'price' => 18700, 'badge' => 'Для BMW'],
    ['category' => 'тормозная система', 'title' => 'Тормозные диски BMW X5 E70', 'price' => 8900, 'badge' => 'Для BMW'],
    ['category' => 'электрика', 'title' => 'Аккумулятор BMW 5 series F10', 'price' => 12500, 'badge' => 'Для BMW'],
    ['category' => 'двигатель', 'title' => 'Ремень ГРМ BMW 7 series G11', 'price' => 3200, 'badge' => 'Для BMW'],
    ['category' => 'фильтры', 'title' => 'Масляный фильтр BMW X3 G01', 'price' => 1450, 'badge' => 'Для BMW'],
    ['category' => 'тормозная система', 'title' => 'Тормозные колодки BMW 1 series F20', 'price' => 5200, 'badge' => 'Для BMW'],
    ['category' => 'электрика', 'title' => 'Аккумулятор Mercedes-Benz E-class W213', 'price' => 12500, 'badge' => 'Для Mercedes'],
    ['category' => 'тормозная система', 'title' => 'Тормозные колодки Mercedes C-class W205', 'price' => 4500, 'badge' => 'Для Mercedes'],
    ['category' => 'фильтры', 'title' => 'Воздушный фильтр Mercedes GLC X253', 'price' => 1850, 'badge' => 'Для Mercedes'],
    ['category' => 'двигатель', 'title' => 'Свечи зажигания Mercedes E-class W212', 'price' => 1200, 'badge' => 'Для Mercedes'],
    ['category' => 'трансмиссия', 'title' => 'Сцепление Mercedes A-class W176', 'price' => 13800, 'badge' => 'Для Mercedes'],
    ['category' => 'двигатель', 'title' => 'Ремень ГРМ Toyota Camry XV70', 'price' => 3200, 'badge' => 'Для Toyota'],
    ['category' => 'фильтры', 'title' => 'Масляный фильтр Toyota RAV4 XA50', 'price' => 950, 'badge' => 'Для Toyota'],
    ['category' => 'ходовая часть', 'title' => 'Амортизатор Toyota Corolla E210', 'price' => 3800, 'badge' => 'Для Toyota'],
    ['category' => 'тормозная система', 'title' => 'Тормозные колодки Toyota Land Cruiser 200', 'price' => 6700, 'badge' => 'Для Toyota'],
    ['category' => 'электрика', 'title' => 'Стартер Toyota Prius XW30', 'price' => 14200, 'badge' => 'Для Toyota'],
    ['category' => 'фильтры', 'title' => 'Воздушный фильтр Ford Focus MK4', 'price' => 950, 'badge' => 'Для Ford'],
    ['category' => 'тормозная система', 'title' => 'Тормозные колодки Ford Kuga II', 'price' => 2900, 'badge' => 'Для Ford'],
    ['category' => 'кузовные детали', 'title' => 'Бампер передний Ford Fiesta MK7', 'price' => 15600, 'badge' => 'Для Ford'],
    ['category' => 'двигатель', 'title' => 'Турбина Ford Mondeo MK5', 'price' => 23400, 'badge' => 'Для Ford'],
    ['category' => 'электрика', 'title' => 'Генератор Ford Explorer U502', 'price' => 16700, 'badge' => 'Для Ford'],
    ['category' => 'ходовая часть', 'title' => 'Амортизатор Hyundai Solaris II', 'price' => 3800, 'badge' => 'Для Hyundai'],
    ['category' => 'тормозная система', 'title' => 'Тормозные колодки Hyundai Tucson TL', 'price' => 2900, 'badge' => 'Для Hyundai'],
    ['category' => 'электрика', 'title' => 'Генератор Hyundai Santa Fe TM', 'price' => 13400, 'badge' => 'Для Hyundai'],
    ['category' => 'фильтры', 'title' => 'Топливный фильтр Hyundai Elantra MD', 'price' => 1250, 'badge' => 'Для Hyundai'],
    ['category' => 'двигатель', 'title' => 'Ремень ГРМ Hyundai Creta', 'price' => 2800, 'badge' => 'Для Hyundai'],
    ['category' => 'тормозная система', 'title' => 'Тормозные колодки Kia Rio X-Line', 'price' => 2900, 'badge' => 'Для Kia'],
    ['category' => 'двигатель', 'title' => 'Турбина Kia Sportage QL', 'price' => 28900, 'badge' => 'Для Kia'],
    ['category' => 'фильтры', 'title' => 'Топливный фильтр Kia Sorento UM', 'price' => 1850, 'badge' => 'Для Kia'],
    ['category' => 'кузовные детали', 'title' => 'Фара противотуманная Kia Optima JF', 'price' => 7800, 'badge' => 'Для Kia'],
    ['category' => 'электрика', 'title' => 'Стартер Kia Cerato YD', 'price' => 11500, 'badge' => 'Для Kia'],
    ['category' => 'кузовные детали', 'title' => 'Бампер передний Honda Civic FK7', 'price' => 15600, 'badge' => 'Для Honda'],
    ['category' => 'двигатель', 'title' => 'Распределительный вал Honda CR-V RW', 'price' => 8900, 'badge' => 'Для Honda'],
    ['category' => 'фильтры', 'title' => 'Масляный фильтр Honda Accord', 'price' => 1100, 'badge' => 'Для Honda'],
    ['category' => 'тормозная система', 'title' => 'Тормозные диски Honda Pilot', 'price' => 9200, 'badge' => 'Для Honda'],
    ['category' => 'электрика', 'title' => 'Генератор Volkswagen Golf MK7', 'price' => 13400, 'badge' => 'Для Volkswagen'],
    ['category' => 'тормозная система', 'title' => 'Тормозные диски Volkswagen Passat B8', 'price' => 6700, 'badge' => 'Для Volkswagen'],
    ['category' => 'двигатель', 'title' => 'Свечи зажигания Volkswagen Tiguan', 'price' => 950, 'badge' => 'Для Volkswagen'],
    ['category' => 'фильтры', 'title' => 'Воздушный фильтр Volkswagen Polo', 'price' => 1200, 'badge' => 'Для Volkswagen'],
    ['category' => 'двигатель', 'title' => 'Турбина Nissan Qashqai J11', 'price' => 28900, 'badge' => 'Для Nissan'],
    ['category' => 'тормозная система', 'title' => 'Суппорт тормозной Nissan X-Trail T32', 'price' => 11200, 'badge' => 'Для Nissan'],
    ['category' => 'фильтры', 'title' => 'Масляный фильтр Nissan Juke', 'price' => 1050, 'badge' => 'Для Nissan'],
    ['category' => 'электрика', 'title' => 'Аккумулятор Nissan Murano Z51', 'price' => 13800, 'badge' => 'Для Nissan'],
    ['category' => 'тормозная система', 'title' => 'Суппорт тормозной Chevrolet Cruze J300', 'price' => 11200, 'badge' => 'Для Chevrolet'],
    ['category' => 'двигатель', 'title' => 'Коленчатый вал Chevrolet Aveo T300', 'price' => 15600, 'badge' => 'Для Chevrolet'],
    ['category' => 'фильтры', 'title' => 'Топливный фильтр Chevrolet Orlando', 'price' => 1350, 'badge' => 'Для Chevrolet'],
    ['category' => 'кузовные детали', 'title' => 'Капот Chevrolet Captiva C100', 'price' => 24300, 'badge' => 'Для Chevrolet'],
    ['category' => 'двигатель', 'title' => 'Коленчатый вал двигателя 2.0 TSI', 'price' => 18700, 'badge' => 'Хит'],
    ['category' => 'двигатель', 'title' => 'Прокладки двигателя комплект V8', 'price' => 4500, 'badge' => 'Акция'],
    ['category' => 'двигатель', 'title' => 'Топливный насос высокого давления', 'price' => 8900, 'badge' => 'Новинка'],
    ['category' => 'двигатель', 'title' => 'Распределительный вал 16V', 'price' => 12300, 'badge' => 'Хит'],
    ['category' => 'тормозная система', 'title' => 'Тормозной цилиндр главный', 'price' => 3400, 'badge' => 'Акция'],
    ['category' => 'тормозная система', 'title' => 'Тормозные колодки керамические', 'price' => 5600, 'badge' => 'Хит'],
    ['category' => 'ходовая часть', 'title' => 'Стабилизатор поперечной устойчивости', 'price' => 6700, 'badge' => 'Новинка'],
    ['category' => 'тормозная система', 'title' => 'Тормозные суппорта передние', 'price' => 12800, 'badge' => 'Акция'],
    ['category' => 'фильтры', 'title' => 'Топливный фильтр тонкой очистки', 'price' => 2100, 'badge' => 'Хит'],
    ['category' => 'тормозная система', 'title' => 'Тормозные диски вентилируемые', 'price' => 7800, 'badge' => 'Новинка'],
    ['category' => 'ходовая часть', 'title' => 'Цапфа поворотная', 'price' => 4500, 'badge' => 'Акция'],
    ['category' => 'двигатель', 'title' => 'Сальники коленвала комплект', 'price' => 3200, 'badge' => 'Хит'],

    ['category' => 'фильтры', 'title' => 'Фильтр масляный Mann W914/2', 'price' => 1250, 'badge' => 'Новинка'],
    ['category' => 'тормозная система', 'title' => 'Тормозные колодки Brembo P85115', 'price' => 3890, 'old_price' => 4500, 'badge' => 'Акция'],
    ['category' => 'двигатель', 'title' => 'Свечи зажигания NGK BKR6E', 'price' => 850, 'badge' => 'Хит'],
    ['category' => 'трансмиссия', 'title' => 'Сцепление SACHS 3000 951 515', 'price' => 12500],
    ['category' => 'ходовая часть', 'title' => 'Амортизатор KYB 334302', 'price' => 4200, 'old_price' => 5100, 'badge' => 'Акция'],
    ['category' => 'электрика', 'title' => 'Аккумулятор VARTA Blue Dynamic E11', 'price' => 8900],
    ['category' => 'кузовные детали', 'title' => 'Фара правая универсальная', 'price' => 15300, 'badge' => 'Новинка'],
    ['category' => 'масла и жидкости', 'title' => 'Моторное масло Mobil 1 5W-30', 'price' => 3800],
    ['category' => 'фильтры', 'title' => 'Воздушный фильтр Bosch F026400224', 'price' => 1100],
    ['category' => 'тормозная система', 'title' => 'Тормозной диск TRW DF4261', 'price' => 6700],
    ['category' => 'двигатель', 'title' => 'Ремень ГРМ Gates T420', 'price' => 2900, 'badge' => 'Хит'],
    ['category' => 'электрика', 'title' => 'Генератор Valeo 439730', 'price' => 18500],
    ['category' => 'система охлаждения', 'title' => 'Радиатор охлаждения Nissens 94170', 'price' => 11200, 'badge' => 'Новинка'],
    ['category' => 'рулевое управление', 'title' => 'Рулевая рейка ZF 800195058', 'price' => 24500],
    ['category' => 'система выпуска', 'title' => 'Глушитель Walker 55487', 'price' => 8900, 'badge' => 'Акция'],
    ['category' => 'фильтры', 'title' => 'Салонный фильтр Mahle LAK 521', 'price' => 950],
    ['category' => 'двигатель', 'title' => 'Термостат Wahler 3076.82D', 'price' => 3200],
    ['category' => 'трансмиссия', 'title' => 'Масло трансмиссионное Motul Gear 300 75W90', 'price' => 2800],
    ['category' => 'ходовая часть', 'title' => 'Стойка стабилизатора Lemforder 30672 01', 'price' => 1800],
    ['category' => 'тормозная система', 'title' => 'Тормозная жидкость ATE TYP 200', 'price' => 1200],
    ['category' => 'электрика', 'title' => 'Стартер Bosch 0986010280', 'price' => 14200, 'badge' => 'Хит'],
    ['category' => 'кузовные детали', 'title' => 'Бампер передний универсальный', 'price' => 18700],
    ['category' => 'масла и жидкости', 'title' => 'Антифриз G12++ Felix Prolong 5L', 'price' => 2100],
    ['category' => 'система охлаждения', 'title' => 'Помпа водяная Gates 42137', 'price' => 5400],
    ['category' => 'рулевое управление', 'title' => 'Наконечник рулевой тяги TRW JTE799', 'price' => 3200],
    ['category' => 'фильтры', 'title' => 'Топливный фильтр Knecht KL 169/2', 'price' => 1850],
    ['category' => 'двигатель', 'title' => 'Прокладка ГБЦ Victor Reinz 71-99718-01', 'price' => 6700],
    ['category' => 'трансмиссия', 'title' => 'Подшипник выжимной SACHS 3152 160 141', 'price' => 2900],
    ['category' => 'ходовая часть', 'title' => 'Пружина подвески Kilen 30221', 'price' => 8200],
    ['category' => 'тормозная система', 'title' => 'Суппорт тормозной ATE 24.0130-5701.2', 'price' => 15300],
    ['category' => 'электрика', 'title' => 'Катушка зажигания Bosch 0221504470', 'price' => 4100, 'badge' => 'Акция'],
    ['category' => 'кузовные детали', 'title' => 'Зеркало боковое левое универсальное', 'price' => 8900],
    ['category' => 'масла и жидкости', 'title' => 'Масло для ГУР Ravenol PSF', 'price' => 1650],
    ['category' => 'система выпуска', 'title' => 'Лямбда-зонд Bosch 0258006546', 'price' => 11200],
    ['category' => 'рулевое управление', 'title' => 'Рулевой наконечник Lemforder 20275 01', 'price' => 3800],
    ['category' => 'фильтры', 'title' => 'Масляный фильтр Mahle OX 395D', 'price' => 950],
    ['category' => 'двигатель', 'title' => 'Ремень генератора Contitech 6PK1885', 'price' => 3200],
    ['category' => 'трансмиссия', 'title' => 'Фланец полуоси GKN 980112', 'price' => 12800],
    ['category' => 'ходовая часть', 'title' => 'Сайлентблок передний Febi 21372', 'price' => 2100],
    ['category' => 'тормозная система', 'title' => 'Тормозной шланг TRW BHA 513', 'price' => 2900],
    ['category' => 'электрика', 'title' => 'Датчик ABS Hella 6PT 009 107-791', 'price' => 5400],
    ['category' => 'кузовные детали', 'title' => 'Капот универсальный', 'price' => 23400],
    ['category' => 'масла и жидкости', 'title' => 'Тормозная жидкость Bosch ENV6', 'price' => 850],
    ['category' => 'система охлаждения', 'title' => 'Вентилятор радиатора Hella 8FV 003 501-021', 'price' => 16700],
    ['category' => 'рулевое управление', 'title' => 'Рулевая тяга Lemforder 24713 01', 'price' => 6200],
    ['category' => 'фильтры', 'title' => 'Воздушный фильтр Mann C 3698', 'price' => 1850],
    ['category' => 'двигатель', 'title' => 'Крышка клапана Elring 024.492', 'price' => 4900],
    ['category' => 'трансмиссия', 'title' => 'Поддон АКПП ZF 8HP', 'price' => 8900],
    ['category' => 'ходовая часть', 'title' => 'Опорный подшипник SKF VKBA 3564', 'price' => 3200]
];

$filtered_products = $all_products;
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

if ($search_term !== '' || $category_filter !== '') 
{
    if ($search_term !== '') 
    {
        $brand_results = enhanceBrandSearch($search_term, $all_products);

        if (!empty($brand_results)) 
        {
            $filtered_products = $brand_results;
        } 
        else 
        {
            $part_results = searchByPartCategory($search_term, $all_products);

            if (!empty($part_results)) 
            {
                $filtered_products = $part_results;
            } 
            else 
            {
                $filtered_products = array_filter($all_products, function($product) use ($search_term) 
                {
                    $title_lower = strtolower($product['title']);
                    $search_lower = strtolower($search_term);
                    return strpos($title_lower, $search_lower) !== false;
                });
            }
        }
    }

    if ($category_filter !== '' && $category_filter !== 'все категории') 
    {
        $filtered_products = array_filter($filtered_products, function($product) use ($category_filter) 
        {
            return $product['category'] === $category_filter;
        });
    }

    $filtered_products = array_values($filtered_products);
}

$total_items = count($filtered_products);
$total_pages = ceil($total_items / $items_per_page);
$start_index = ($current_page - 1) * $items_per_page;
$end_index = min($start_index + $items_per_page, $total_items);

$show_pagination = $total_pages > 1;

function buildQueryString($page, $search, $category) 
{
    $params = [];

    if ($page > 1) 
    {
        $params['page'] = $page;
    }

    if (!empty($search)) 
    {
        $params['search'] = $search;
    }

    if (!empty($category) && $category !== 'все категории') 
    {
        $params['category'] = $category;
    }

    return http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ассортимент - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/notifications-styles.css">
    <link rel="stylesheet" href="../css/assortment-styles.css">
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
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-4">
    <div class="hero-section text-center mb-5" style="padding-top: 105px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Каталог автозапчастей</h1>
        <p class="lead text-muted mb-4">Более 1000 наименований оригинальных и качественных аналогов</p>
        <?php if ($search_term !== '') 
        {
        ?>
        <div class="alert alert-info d-inline-block">
            <i class="bi bi-search me-2"></i>
            <?php 
            if ($search_term !== '' && $category_filter !== '' && $category_filter !== 'все категории') 
            {
                echo 'Поиск: "' . htmlspecialchars($search_term) . '" в категории "' . htmlspecialchars($category_filter) . '"';
            } 
            else if ($search_term !== '') 
            {
                echo 'Результаты поиска: "' . htmlspecialchars($search_term) . '"';
            }
            ?>
        </div>
        <?php 
        } 
        ?>
    </div>     
    <div class="row mb-4">
        <div class="col-md-5 col-lg-6">
            <div class="search-container position-relative">
                <input type="text" id="partsSearch" placeholder="Поиск по каталогу..." class="form-control" 
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button class="btn btn-link search-clear" type="button" style="display: none;">
                    <i class="bi bi-x"></i>
                </button>
                <i class="bi bi-search search-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <select id="categoryFilter" class="form-select">
                <option value="все категории" <?php echo $category_filter === '' || $category_filter === 'все категории' ? 'selected' : ''; ?>>Все категории</option>
                <option value="двигатель" <?php echo $category_filter === 'двигатель' ? 'selected' : ''; ?>>Двигатель</option>
                <option value="трансмиссия" <?php echo $category_filter === 'трансмиссия' ? 'selected' : ''; ?>>Трансмиссия</option>
                <option value="ходовая часть" <?php echo $category_filter === 'ходовая часть' ? 'selected' : ''; ?>>Ходовая часть</option>
                <option value="тормозная система" <?php echo $category_filter === 'тормозная система' ? 'selected' : ''; ?>>Тормозная система</option>
                <option value="электрика" <?php echo $category_filter === 'электрика' ? 'selected' : ''; ?>>Электрика</option>
                <option value="кузовные детали" <?php echo $category_filter === 'кузовные детали' ? 'selected' : ''; ?>>Кузовные детали</option>
                <option value="фильтры" <?php echo $category_filter === 'фильтры' ? 'selected' : ''; ?>>Фильтры</option>
                <option value="масла и жидкости" <?php echo $category_filter === 'масла и жидкости' ? 'selected' : ''; ?>>Масла и жидкости</option>
                <option value="система охлаждения" <?php echo $category_filter === 'система охлаждения' ? 'selected' : ''; ?>>Система охлаждения</option>
                <option value="система выпуска" <?php echo $category_filter === 'система выпуска' ? 'selected' : ''; ?>>Система выпуска</option>
                <option value="рулевое управление" <?php echo $category_filter === 'рулевое управление' ? 'selected' : ''; ?>>Рулевое управление</option>
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
            <button id="searchButton" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Найти
            </button>
        </div>
    </div>
    <?php 
    if ($search_term !== '' || $category_filter !== '') 
    {
    ?>
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info py-2">
                <?php 
                if ($search_term !== '' && $category_filter !== '' && $category_filter !== 'все категории') 
                {
                    echo 'Найдено ' . $total_items . ' товаров по запросу "' . htmlspecialchars($search_term) . '" в категории "' . htmlspecialchars($category_filter) . '"';
                } 
                else if ($search_term !== '') 
                {
                    echo 'Найдено ' . $total_items . ' товаров по запросу "' . htmlspecialchars($search_term) . '"';
                } 
                else if ($category_filter !== '' && $category_filter !== 'все категории') 
                {
                    echo 'Найдено ' . $total_items . ' товаров в категории "' . htmlspecialchars($category_filter) . '"';
                }
                ?>
                <?php 
                if ($search_term !== '' || ($category_filter !== '' && $category_filter !== 'все категории'))
                {
                ?>
                    <a href="assortment.php" class="btn btn-sm btn-outline-secondary ms-2">Показать все</a>
                <?php 
                }
                ?>
            </div>
        </div>
    </div>
    <?php 
    } 
    ?>
    <div class="row g-3" id="productsContainer">
        <?php 
        if ($total_items > 0)
        { 
        ?>
            <?php 
            for ($i = $start_index; $i < $end_index; $i++)
            {
            ?>
                <?php $product = $filtered_products[$i]; ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="product-card">
                        <?php 
                        if (isset($product['badge'])) 
                        {
                        ?>
                            <div class="product-badge"><?php echo $product['badge']; ?></div>
                        <?php 
                        }
                        ?>
                        <div class="product-image">
                            <img src="../img/no-image.png" class="product-img" alt="<?php echo htmlspecialchars($product['title']); ?>">
                        </div>
                        <div class="product-body">
                            <h6 class="product-title"><?php echo $product['title']; ?></h6>
                            <div class="product-price">
                                <?php 
                                if (isset($product['old_price']))
                                {
                                ?>
                                    <span class="current-price"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</span>
                                    <span class="old-price"><?php echo number_format($product['old_price'], 0, '', ' '); ?> ₽</span>
                                <?php 
                                }
                                else
                                { 
                                ?>
                                    <span class="current-price"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</span>
                                <?php 
                                }
                                ?>
                            </div>
                            <div class="product-actions">
                                <?php 
                                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                                {
                                ?>
                                    <form method="POST" action="../profile.php" class="d-inline me-1">
                                        <input type="hidden" name="wishlist_action" value="1">
                                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['title']) ?>">
                                        <input type="hidden" name="product_image" value="../img/no-image.png">
                                        <input type="hidden" name="price" value="<?= $product['price'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="cart.php" class="d-inline add-to-cart-form">
                                        <input type="hidden" name="product_id" value="<?= isset($product['id']) ? $product['id'] : 0 ?>">
                                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['title']) ?>">
                                        <input type="hidden" name="product_image" value="../img/no-image.png">
                                        <input type="hidden" name="price" value="<?= $product['price'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-sm add-to-cart-btn">
                                            <span class="btn-text">
                                                <i class="bi bi-cart-plus"></i> В корзину
                                            </span>
                                        </button>
                                    </form>
                                <?php 
                                }
                                else
                                {
                                ?>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        <i class="bi bi-cart-plus"></i> В корзину
                                    </button>
                                <?php 
                                }
                                ?>
                                <button class="btn btn-outline-secondary btn-sm">Подробнее</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
            }
            ?>
        <?php 
        }
        else
        { 
        ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search display-4 text-muted mb-3"></i>
                <h4 class="text-muted">Товары не найдены</h4>
                <p class="text-muted mb-3">Попробуйте изменить параметры поиска</p>
                <a href="assortment.php" class="btn btn-primary">Показать все товары</a>
            </div>
        <?php 
        }
        ?>
    </div>
    <?php 
    if ($show_pagination) 
    {
    ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo buildQueryString($current_page - 1, $search_term, $category_filter); ?>">Назад</a>
            </li>
            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $start_page + 4);

            if ($end_page - $start_page < 4) 
            {
                $start_page = max(1, $end_page - 4);
            }
            
            for ($page = $start_page; $page <= $end_page; $page++) 
            {
            ?>
                <li class="page-item <?php echo $page == $current_page ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo buildQueryString($page, $search_term, $category_filter); ?>"><?php echo $page; ?></a>
                </li>
            <?php 
            }
            ?>
            <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo buildQueryString($current_page + 1, $search_term, $category_filter); ?>">Вперед</a>
            </li>
        </ul>
    </nav>
    <div class="text-center text-muted mt-2">
        Страница <?php echo $current_page; ?> из <?php echo $total_pages; ?> | Показано <?php echo ($end_index - $start_index); ?> из <?php echo $total_items; ?> товаров
    </div>
    <?php 
    }
    else 
    {
    ?>
        <?php 
        if ($total_items > 0) 
        {
        ?>
        <div class="text-center text-muted mt-3">
            Найдено <?php echo $total_items; ?> товаров
        </div>
        <?php 
        }
        ?>
    <?php 
    } 
    ?>
</div>
<div id="cartNotification" class="notification">
    <i class="bi bi-check-circle-fill"></i>
    <span>Товар добавлен в корзину!</span>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    function performSearch() 
    {
        let searchTerm = document.getElementById('partsSearch').value.trim();
        let categoryFilter = document.getElementById('categoryFilter').value;
        let params = new URLSearchParams();

        if (searchTerm) 
        {
            params.set('search', searchTerm);
        }

        if (categoryFilter && categoryFilter !== 'все категории') 
        {
            params.set('category', categoryFilter);
        }
        else 
        {
            document.getElementById('categoryFilter').value = 'все категории';
        }
        
        if (categoryFilter && categoryFilter !== 'все категории') 
        {
            window.location.href = '?' + params.toString();
        }
        else 
        {
            window.location.href = 'assortment.php';
        }
    }

    function resetSearch() 
    {
        document.getElementById('partsSearch').value = '';
        document.getElementById('categoryFilter').value = 'все категории';
        document.querySelector('.search-clear').style.display = 'none';
        window.location.href = 'assortment.php';
    }

    document.getElementById('searchButton').addEventListener('click', performSearch);
        
    document.getElementById('partsSearch').addEventListener('keypress', function(e) 
    {
        if (e.key === 'Enter') 
        {
            performSearch();
        }
    });

    document.getElementById('categoryFilter').addEventListener('change', function() 
    {
        performSearch();
    });

    document.querySelector('.search-clear').addEventListener('click', resetSearch);

    document.getElementById('partsSearch').addEventListener('input', function() 
    {
        document.querySelector('.search-clear').style.display = this.value ? 'block' : 'none';
    });

    if (document.getElementById('partsSearch').value) 
    {
        document.querySelector('.search-clear').style.display = 'block';
    }

    let addToCartForms = document.querySelectorAll('.add-to-cart-form');

    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) 
        {
            e.preventDefault();
            
            let submitButton = this.querySelector('.add-to-cart-btn');

            if (!submitButton) 
            {
                return;
            }

            let originalWidth = submitButton.offsetWidth + 'px';
            let originalHeight = submitButton.offsetHeight + 'px';
            let originalHtml = submitButton.innerHTML;
            let originalDisabled = submitButton.disabled;

            submitButton.style.minWidth = originalWidth;
            submitButton.style.minHeight = originalHeight;
            submitButton.style.width = originalWidth;
            submitButton.classList.add('btn-loading');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="btn-text">Добавляем...</span>';
            
            showNotification('Товар добавляется...', 'info');
            
            let formData = new FormData(this);

            fetch('ajax_add_to_cart.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) 
                {
                    showNotification(data.message, 'success');
                    updateCartCounter(data.cart_count);
                } 
                else 
                {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Ошибка сети', 'error');
            })
            .finally(() => {
                setTimeout(() => {
                    submitButton.classList.remove('btn-loading');
                    submitButton.disabled = originalDisabled;
                    submitButton.innerHTML = originalHtml;
                    submitButton.style.minWidth = '';
                    submitButton.style.minHeight = '';
                    submitButton.style.width = '';
                }, 1500);
            });
        });
    });
    
    function showNotification(message, type = 'success') 
    {
        let notification = document.getElementById('cartNotification');
        
        if (!notification) 
        {
            notification = document.createElement('div');
            notification.id = 'cartNotification';
            notification.className = 'notification';
            document.body.appendChild(notification);
        }

        let icon = 'bi-check-circle-fill';
        let bgColor = '#28a745';
        let textColor = 'white';
        
        if (type === 'error') 
        {
            icon = 'bi-exclamation-triangle-fill';
            bgColor = '#dc3545';
        } 
        else if (type === 'info') 
        {
            icon = 'bi-info-circle-fill';
            bgColor = '#17a2b8';
        }
        
        notification.innerHTML = `<i class="bi ${icon}"></i><span>${message}</span>`;
        notification.style.background = bgColor;
        notification.style.color = textColor;
        notification.classList.add('show');
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
    
    function updateCartCounter(newCount = null) 
    {
        let cartCounter = document.getElementById('cartCounter');

        if (cartCounter) 
        {
            if (newCount !== null) 
            {
                cartCounter.textContent = newCount;
            } 
            else 
            {
                let currentCount = parseInt(cartCounter.textContent) || 0;
                cartCounter.textContent = currentCount + 1;
            }

            cartCounter.style.transform = 'scale(1.3)';
            
            setTimeout(() => {
                cartCounter.style.transform = 'scale(1)';
            }, 300);
        }
    }
});
</script>
</body>
</html>