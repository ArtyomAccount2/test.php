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

$items_per_page = 6;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

$all_news = [
    ['id' => 1, 'date' => '15 мая 2025', 'title' => 'Открытие нового магазина на Московском проспекте', 'excerpt' => 'Мы рады сообщить об открытии нашего нового магазина автозапчастей в центре Калининграда. Теперь у нас еще больше товаров и удобное расположение.', 'badge' => 'Новое'],
    ['id' => 2, 'date' => '3 мая 2025', 'title' => 'Специальная акция на моторные масла до конца месяца', 'excerpt' => 'Только в мае скидка 15% на все моторные масла Castrol и Mobil. Акция действует при покупке от 5 литров.', 'badge' => 'Акция'],
    ['id' => 3, 'date' => '28 апреля 2025', 'title' => 'Поступили новые запчасти для японских автомобилей', 'excerpt' => 'В нашем ассортименте появились оригинальные запчасти для Toyota, Honda и Nissan. Гарантия качества и доступные цены.', 'badge' => ''],
    ['id' => 4, 'date' => '20 апреля 2025', 'title' => 'Расширение ассортимента шин для внедорожников', 'excerpt' => 'Теперь в наличии широкий выбор всесезонных и зимних шин для внедорожников от ведущих производителей.', 'badge' => 'Новинка'],
    ['id' => 5, 'date' => '12 апреля 2025', 'title' => 'Бесплатная диагностика ходовой части', 'excerpt' => 'При покупке любых запчастей для подвески получите бесплатную диагностику ходовой части вашего автомобиля.', 'badge' => 'Акция'],
    ['id' => 6, 'date' => '5 апреля 2025', 'title' => 'Новые поставки фильтров для европейских авто', 'excerpt' => 'Поступили в продажу воздушные, масляные и топливные фильтры для автомобилей Volkswagen, BMW, Mercedes.', 'badge' => ''],
    ['id' => 7, 'date' => '28 марта 2025', 'title' => 'Скидки на тормозные колодки и диски', 'excerpt' => 'Специальное предложение на комплекты тормозных колодок и дисков - скидка до 20% до конца месяца.', 'badge' => 'Акция'],
    ['id' => 8, 'date' => '15 марта 2025', 'title' => 'Открытие онлайн-записи на сервис', 'excerpt' => 'Теперь вы можете записаться на техническое обслуживание через наш сайт. Быстро и удобно!', 'badge' => 'Новое'],
    ['id' => 9, 'date' => '8 марта 2025', 'title' => 'Весенняя распродажа автокосметики', 'excerpt' => 'Скидки до 30% на средства для ухода за автомобилем. Подготовьте машину к весне по выгодным ценам.', 'badge' => 'Акция'],
    ['id' => 10, 'date' => '1 марта 2025', 'title' => 'Новые поставки аккумуляторов', 'excerpt' => 'В наличии появились аккумуляторы различных емкостей для всех популярных марок автомобилей.', 'badge' => ''],
    ['id' => 11, 'date' => '22 февраля 2025', 'title' => 'Специальные условия для таксопарков', 'excerpt' => 'Предлагаем выгодные условия сотрудничества для таксопарков и автопредприятий. Индивидуальный подход.', 'badge' => ''],
    ['id' => 12, 'date' => '15 февраля 2025', 'title' => 'Обновление программы лояльности', 'excerpt' => 'Стали доступны новые бонусы и скидки для участников нашей программы лояльности. Присоединяйтесь!', 'badge' => 'Новое'],
    ['id' => 13, 'date' => '8 февраля 2025', 'title' => 'Новые технологии в обслуживании автомобилей', 'excerpt' => 'Внедрили современное диагностическое оборудование для более точного определения неисправностей.', 'badge' => 'Технологии'],
    ['id' => 14, 'date' => '1 февраля 2025', 'title' => 'Расширение складских помещений', 'excerpt' => 'Увеличили складские площади для хранения запчастей. Теперь мы можем предложить еще более широкий ассортимент.', 'badge' => 'Развитие'],
    ['id' => 15, 'date' => '25 января 2025', 'title' => 'Спецпредложение на зимнюю резину', 'excerpt' => 'Скидки до 25% на зимние шины всех размеров. Успейте подготовить автомобиль к зимнему сезону!', 'badge' => 'Акция'],
    ['id' => 16, 'date' => '18 января 2025', 'title' => 'Новые бренды в ассортименте', 'excerpt' => 'Теперь в продаже запчасти от новых производителей: Bosch, Mann, NGK и других проверенных брендов.', 'badge' => 'Новинка'],
    ['id' => 17, 'date' => '10 января 2025', 'title' => 'Бесплатный шиномонтаж при покупке шин', 'excerpt' => 'При покупке комплекта шин в нашем магазине получаете бесплатный шиномонтаж и балансировку.', 'badge' => 'Акция'],
    ['id' => 18, 'date' => '5 января 2025', 'title' => 'Новый год - новые возможности', 'excerpt' => 'Поздравляем всех с Новым годом! Готовим для вас много интересных предложений и акций в наступившем году.', 'badge' => 'Поздравление']
];

$total_news = count($all_news);
$total_pages = ceil($total_news / $items_per_page);

$current_news = array_slice($all_news, $offset, $items_per_page);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новости компании - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/news-styles.css">
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

<main class="news-page">
    <section class="news-hero py-5" style="margin-top: 105px;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="display-4 fw-bold mb-4">Новости компании</h1>
                    <p class="lead">Будьте в курсе последних событий и акций нашего автоцентра</p>
                </div>
            </div>
        </div>
    </section>
    <section class="news-list py-5">
        <div class="container">
            <div class="row g-4">
                <?php 
                foreach($current_news as $news) 
                {
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="news-card">
                        <div class="news-img-container">
                            <img src="../img/no-image.png" class="news-img" alt="<?= htmlspecialchars($news['title']) ?>">
                            <?php 
                            if($news['badge']) 
                            {
                            ?>
                                <div class="news-badge <?= $news['badge'] == 'Акция' ? 'news-badge-sale' : '' ?>">
                                    <?= $news['badge'] ?>
                                </div>
                            <?php 
                            } 
                            ?>
                        </div>
                        <div class="news-body">
                            <div class="news-date">
                                <i class="bi bi-calendar"></i> <?= $news['date'] ?>
                            </div>
                            <h3 class="news-title"><?= htmlspecialchars($news['title']) ?></h3>
                            <p class="news-excerpt"><?= htmlspecialchars($news['excerpt']) ?></p>
                            <a href="news-single.php?id=<?= $news['id'] ?>" class="read-more">
                                Читать далее <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php 
                } 
                ?>
            </div>
            
            <?php 
            if($total_pages > 1) 
            {
            ?>
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $current_page == 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $current_page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);

                    if ($start_page == 1) 
                    {
                        $end_page = min($total_pages, 5);
                    }
                    
                    if ($end_page == $total_pages) 
                    {
                        $start_page = max(1, $total_pages - 4);
                    }

                    if ($start_page > 1) 
                    {
                        echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';

                        if ($start_page > 2) 
                        {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }

                    for ($i = $start_page; $i <= $end_page; $i++) 
                    {
                        $active = $i == $current_page ? 'active' : '';
                        echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                    }
                    
                    if ($end_page < $total_pages) 
                    {
                        if ($end_page < $total_pages - 1) 
                        {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                    }
                    ?>
                    <li class="page-item <?= $current_page == $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $current_page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php 
            }
            ?>
        </div>
    </section>
</main>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>