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

$items_per_page = 3;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

$total_news_query = "SELECT COUNT(*) as total FROM news WHERE status = 'published'";
$total_news_result = $conn->query($total_news_query);
$total_news_row = $total_news_result->fetch_assoc();
$total_news = $total_news_row['total'];

$total_pages = ceil($total_news / $items_per_page);

$query = "SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $items_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

$current_news = [];
while ($row = $result->fetch_assoc()) 
{
    $badge = '';
    $title_lower = strtolower($row['title']);
    $content_lower = strtolower($row['content']);
    
    if (strpos($title_lower, 'акция') !== false || strpos($content_lower, 'скид') !== false || strpos($content_lower, 'акция') !== false) 
    {
        $badge = 'Акция';
    } 
    else if (strpos($title_lower, 'нов') !== false || strpos($content_lower, 'новый') !== false) 
    {
        $badge = 'Новое';
    } 
    else if (strpos($title_lower, 'техн') !== false || strpos($content_lower, 'технологи') !== false) 
    {
        $badge = 'Технологии';
    } 
    else if (strpos($title_lower, 'развитие') !== false || strpos($content_lower, 'развитие') !== false) 
    {
        $badge = 'Развитие';
    }

    $date = date('d F Y', strtotime($row['created_at']));
    $excerpt = strip_tags($row['content']);

    if (mb_strlen($excerpt) > 150) 
    {
        $excerpt = mb_substr($excerpt, 0, 150) . '...';
    }
    
    $current_news[] = [
        'id' => $row['id'],
        'date' => $date,
        'title' => htmlspecialchars($row['title']),
        'excerpt' => htmlspecialchars($excerpt),
        'badge' => $badge,
        'content' => $row['content'],
        'author' => $row['author']
    ];
}

$stmt->close();
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
                if (count($current_news) > 0) 
                {
                    foreach($current_news as $news) 
                    {
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="news-card">
                        <div class="news-img-container">
                            <img src="../img/no-image.png" class="news-img" alt="<?= $news['title'] ?>">
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
                            <h3 class="news-title"><?= $news['title'] ?></h3>
                            <p class="news-excerpt"><?= $news['excerpt'] ?></p>
                            <a href="news-single.php?id=<?= $news['id'] ?>&page=<?= $current_page ?>" class="read-more">
                                Читать далее <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } 
                else 
                {
                ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <h4>Новостей пока нет</h4>
                        <p>Следите за обновлениями, скоро появятся новые публикации!</p>
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