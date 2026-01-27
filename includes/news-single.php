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

$news_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$query = "SELECT * FROM news WHERE id = ? AND status = 'published'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) 
{
    header("Location: news.php?page=" . $page);
    exit();
}

$news = $result->fetch_assoc();

$date = date('d F Y', strtotime($news['created_at']));

$badge = '';
$title_lower = strtolower($news['title']);
$content_lower = strtolower($news['content']);

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

$stmt->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']) ?> - Лал-Авто</title>
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

<main class="news-single-page">
    <section class="py-5" style="margin-top: 70px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <a href="news.php?page=<?= $page ?>" class="back-to-news">
                        <i class="bi bi-arrow-left"></i> Назад к новостям
                    </a>
                    <article class="news-single-article">
                        <div class="news-single-img mb-5 position-relative">
                            <img src="../img/no-image.png" class="img-fluid rounded-3" alt="<?= htmlspecialchars($news['title']) ?>">
                            <?php 
                            if($badge)
                            {
                            ?>
                                <div class="news-badge <?= $badge == 'Акция' ? 'news-badge-sale' : '' ?>">
                                    <?= $badge ?>
                                </div>
                            <?php 
                            }
                            ?>
                        </div>
                        <div class="news-meta mb-4">
                            <div class="news-meta-item">
                                <i class="bi bi-calendar"></i>
                                <span><?= $date ?></span>
                            </div>
                            <?php 
                            if($news['author'])
                            {
                            ?>
                            <div class="news-meta-item">
                                <i class="bi bi-person"></i>
                                <span><?= htmlspecialchars($news['author']) ?></span>
                            </div>
                            <?php 
                            }
                            ?>
                        </div>                   
                        <h1 class="news-single-title mb-4"><?= htmlspecialchars($news['title']) ?></h1>                       
                        <div class="news-single-content">
                            <?= nl2br(htmlspecialchars($news['content'])) ?>
                        </div>
                        <div class="news-tags mt-5 pt-4">
                            <span class="me-2"><i class="bi bi-tags"></i> Метки:</span>
                            <a href="#" class="btn btn-sm btn-outline-secondary me-2 mb-2">Новости</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary me-2 mb-2">Автомобили</a>
                            <a href="#" class="btn btn-sm btn-outline-secondary me-2 mb-2">Обновления</a>
                        </div>
                    </article>
                </div>
            </div>
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