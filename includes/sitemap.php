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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Карта сайта - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/sitemap-styles.css">
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

        let searchInput = document.getElementById('sitemapSearch');
        let sitemapItems = document.querySelectorAll('.sitemap-list a');
        let sections = document.querySelectorAll('.sitemap-section');

        if(searchInput) 
        {
            searchInput.addEventListener('input', function(e) 
            {
                let searchTerm = e.target.value.toLowerCase().trim();
                let foundResults = false;

                sections.forEach(section => {
                    let sectionVisible = false;
                    let links = section.querySelectorAll('.sitemap-list a');
                    
                    links.forEach(link => {
                        let text = link.textContent.toLowerCase();

                        if(text.includes(searchTerm)) 
                        {
                            link.style.display = 'block';
                            sectionVisible = true;
                            foundResults = true;
                        } 
                        else 
                        {
                            link.style.display = 'none';
                        }
                    });

                    section.style.display = sectionVisible ? 'block' : 'none';
                });

                document.getElementById('noResults').style.display = foundResults ? 'none' : 'block';
                
                if (searchTerm.length > 0) 
                {
                    document.querySelector('.sitemap-content').classList.add('search-active');
                } 
                else 
                {
                    document.querySelector('.sitemap-content').classList.remove('search-active');
                }
            });
        }

        let stats = {
            totalPages: document.querySelectorAll('.sitemap-list a').length,
            totalSections: document.querySelectorAll('.sitemap-section').length,
            lastUpdate: new Date().toLocaleDateString('ru-RU')
        };

        document.getElementById('totalPages').textContent = stats.totalPages;
        document.getElementById('totalSections').textContent = stats.totalSections;
        document.getElementById('lastUpdate').textContent = stats.lastUpdate;
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5 pt-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-3" style="padding-top: 60px;">Карта сайта</h1>
            <p class="lead text-muted mb-4">Полная структура всех страниц сайта для удобной навигации</p>
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white border-primary">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="sitemapSearch" class="form-control border-primary" placeholder="Поиск по страницам сайта...">
                        <button class="btn btn-outline-primary" type="button" onclick="document.getElementById('sitemapSearch').value=''; document.getElementById('sitemapSearch').dispatchEvent(new Event('input'));">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-5 justify-content-center">
                <div class="col-md-3">
                    <div class="card border-primary h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-files text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5 class="card-title">Всего страниц</h5>
                            <h3 class="text-primary" id="totalPages">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-success h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-folder text-success mb-2" style="font-size: 2rem;"></i>
                            <h5 class="card-title">Разделов</h5>
                            <h3 class="text-success" id="totalSections">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-info h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-check text-info mb-2" style="font-size: 2rem;"></i>
                            <h5 class="card-title">Обновлено</h5>
                            <h3 class="text-info" id="lastUpdate">-</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="sitemap-content">
                <div id="noResults" class="alert alert-warning text-center" style="display: none;">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    По вашему запросу ничего не найдено. Попробуйте изменить поисковый запрос.
                </div>
                <div class="sitemap-section">
                    <h3 class="mb-4"><i class="bi bi-house-fill"></i> Главные страницы</h3>
                    <ul class="sitemap-list">
                        <li><a href="../index.php">Главная страница</a></li>
                        <li><a href="shops.php">Магазины</a></li>
                        <li><a href="service.php">Автосервис</a></li>
                        <li><a href="assortment.php">Ассортимент</a></li>
                        <li><a href="contacts.php">Контакты</a></li>
                    </ul>
                </div>
                <div class="sitemap-section">
                    <h3 class="mb-4"><i class="bi bi-cart-check"></i> Каталог и покупки</h3>
                    <ul class="sitemap-list">
                        <li><a href="oils.php">Масла и тех. жидкости</a></li>
                        <li><a href="accessories.php">Аксессуары</a></li>
                        <li><a href="brands.php">Торговые марки</a></li>
                        <li><a href="delivery.php">Оплата и доставка</a></li>
                        <li><a href="customers.php">Покупателям</a></li>
                    </ul>
                </div>
                <div class="sitemap-section">
                    <h3 class="mb-4"><i class="bi bi-info-circle-fill"></i> Информация</h3>
                    <ul class="sitemap-list">
                        <li><a href="about.php">О компании</a></li>
                        <li><a href="news.php">Новости</a></li>
                        <li><a href="reviews.php">Отзывы</a></li>
                        <li><a href="vacancies.php">Вакансии</a></li>
                        <li><a href="requisites.php">Реквизиты</a></li>
                        <li><a href="suppliers.php">Поставщикам</a></li>
                    </ul>
                </div>
                <div class="sitemap-section">
                    <h3 class="mb-4"><i class="bi bi-shield-check"></i> Правовая информация</h3>
                    <ul class="sitemap-list">
                        <li><a href="privacy.php">Политика конфиденциальности</a></li>
                        <li><a href="terms.php">Условия использования</a></li>
                        <li><a href="return.php">Возврат и обмен</a></li>
                        <li><a href="guarantee.php">Гарантия</a></li>
                    </ul>
                </div>
                <div class="sitemap-section">
                    <h3 class="mb-4"><i class="bi bi-question-circle-fill"></i> Помощь и поддержка</h3>
                    <ul class="sitemap-list">
                        <li><a href="faq.php">Частые вопросы (FAQ)</a></li>
                        <li><a href="support.php">Поддержка сайта</a></li>
                        <li><a href="api.php">API для разработчиков</a></li>
                    </ul>
                </div>
            </div>
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