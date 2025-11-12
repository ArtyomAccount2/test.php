<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
}

$reviews_per_page = 4;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($current_page < 1) 
{
    $current_page = 1;
}

$total_reviews_stmt = $conn->prepare("SELECT COUNT(*) as total FROM reviews WHERE status = 'approved'");
$total_reviews_stmt->execute();
$total_reviews_result = $total_reviews_stmt->get_result();
$total_reviews = $total_reviews_result->fetch_assoc()['total'];

$total_pages = ceil($total_reviews / $reviews_per_page);

if ($current_page > $total_pages && $total_pages > 0) 
{
    $current_page = $total_pages;
}

$offset = ($current_page - 1) * $reviews_per_page;

$reviews_stmt = $conn->prepare("SELECT * FROM reviews WHERE status = 'approved' ORDER BY created_at DESC LIMIT ? OFFSET ?");
$reviews_stmt->bind_param("ii", $reviews_per_page, $offset);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();

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
    <title>Отзывы - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/reviews-styles.css">
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

        let stars = document.querySelectorAll('.rating-star');
        let ratingValue = document.getElementById('ratingValue');
        
        stars.forEach(star => {
            star.addEventListener('click', function() 
            {
                let rating = this.getAttribute('data-rating');
                ratingValue.value = rating;
                
                stars.forEach((s, index) => {
                    if (index < rating) 
                    {
                        s.classList.add('bi-star-fill', 'text-warning');
                        s.classList.remove('bi-star');
                    } 
                    else 
                    {
                        s.classList.add('bi-star');
                        s.classList.remove('bi-star-fill', 'text-warning');
                    }
                });
            });
            
            star.addEventListener('mouseover', function() 
            {
                let hoverRating = this.getAttribute('data-rating');

                stars.forEach((s, index) => {
                    if (index < hoverRating && !ratingValue.value) 
                    {
                        s.classList.add('bi-star-fill', 'text-warning');
                        s.classList.remove('bi-star');
                    }
                });
            });
            
            star.addEventListener('mouseout', function() 
            {
                if (!ratingValue.value) 
                {
                    stars.forEach(s => {
                        s.classList.add('bi-star');
                        s.classList.remove('bi-star-fill', 'text-warning');
                    });
                } 
                else 
                {
                    let currentRating = ratingValue.value;

                    stars.forEach((s, index) => {
                        if (index < currentRating) 
                        {
                            s.classList.add('bi-star-fill', 'text-warning');
                            s.classList.remove('bi-star');
                        } 
                        else 
                        {
                            s.classList.add('bi-star');
                            s.classList.remove('bi-star-fill', 'text-warning');
                        }
                    });
                }
            });
        });

        let reviewForm = document.getElementById('reviewForm');

        if (reviewForm) 
        {
            reviewForm.addEventListener('submit', function(e) 
            {
                e.preventDefault();
                
                if (ratingValue.value === '0') 
                {
                    alert('Пожалуйста, поставьте оценку');
                    return;
                }
                
                if (!document.getElementById('agreePolicy').checked) 
                {
                    alert('Необходимо согласие на обработку персональных данных');
                    return;
                }

                let formData = new FormData();
                formData.append('name', document.getElementById('reviewName').value);
                formData.append('email', document.getElementById('reviewEmail').value);
                formData.append('rating', ratingValue.value);
                formData.append('text', document.getElementById('reviewText').value);
                formData.append('action', 'add_review');

                fetch('../includes/handle_review.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) 
                    {
                        alert('Спасибо за ваш отзыв! После модерации он будет опубликован.');
                        reviewForm.reset();
                        ratingValue.value = '0';

                        stars.forEach(s => {
                            s.classList.add('bi-star');
                            s.classList.remove('bi-star-fill', 'text-warning');
                        });

                        location.reload();
                    } 
                    else 
                    {
                        alert('Произошла ошибка: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка при отправке отзыва');
                });
            });
        }

        function equalizeReviewHeights() 
        {
            let reviewCards = document.querySelectorAll('.review-card');
            let maxHeight = 0;

            reviewCards.forEach(card => {
                card.style.height = 'auto';
            });

            for (let i = 0; i < reviewCards.length; i += 2) 
            {
                let rowCards = [reviewCards[i]];
                
                if (reviewCards[i + 1])
                { 
                    rowCards.push(reviewCards[i + 1]);
                }
                
                let rowMaxHeight = 0;
                
                rowCards.forEach(card => {
                    let height = card.offsetHeight;

                    if (height > rowMaxHeight) 
                    {
                        rowMaxHeight = height;
                    }
                });
                
                rowCards.forEach(card => {
                    card.style.height = rowMaxHeight + 'px';
                });
            }
        }

        window.addEventListener('load', equalizeReviewHeights);
        window.addEventListener('resize', equalizeReviewHeights);
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-4">
    <div class="reviews-hero text-center mb-5" style="padding-top: 125px;">
        <h1 class="reviews-title display-5 mb-3">Отзывы наших клиентов</h1>
        <p class="reviews-subtitle text-muted">Мы ценим каждого клиента и стремимся стать лучше благодаря вашим отзывам</p>
    </div>
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <div class="review-form-container">
                <div class="review-form-header text-center mb-4">
                    <h3 class="mb-2"><i class="bi bi-pencil-square me-2"></i>Оставить отзыв</h3>
                    <p class="text-muted mb-0">Поделитесь вашим опытом сотрудничества с нами</p>
                </div>
                <form id="reviewForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="reviewName" class="form-label fw-semibold">Ваше имя<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="reviewName" required placeholder="Как к вам обращаться?">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reviewEmail" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="reviewEmail" placeholder="example@mail.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Оценка<span class="text-danger">*</span></label>
                        <div class="rating-container">
                            <div class="rating-stars">
                                <i class="bi bi-star rating-star" data-rating="1"></i>
                                <i class="bi bi-star rating-star" data-rating="2"></i>
                                <i class="bi bi-star rating-star" data-rating="3"></i>
                                <i class="bi bi-star rating-star" data-rating="4"></i>
                                <i class="bi bi-star rating-star" data-rating="5"></i>
                            </div>
                            <input type="hidden" id="ratingValue" name="rating" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reviewText" class="form-label fw-semibold">Текст отзыва<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reviewText" rows="4" required placeholder="Поделитесь вашими впечатлениями..."></textarea>
                    </div>
                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="agreePolicy" required>
                            <label class="form-check-label small" for="agreePolicy">
                                Я согласен на обработку персональных данных
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-send me-2"></i> Отправить отзыв
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="reviews-section mb-5">
        <h2 class="section-title text-center mb-4">Что говорят наши клиенты</h2>
        <div class="row g-4" id="reviewsContainer">
            <?php 
            if ($reviews_result->num_rows > 0) 
            {
                $animation_delay = 0;
                while($review = $reviews_result->fetch_assoc()) 
                {
                    $animation_delay += 0.1;
            ?>
                <div class="col-md-6">
                    <div class="review-card" style="animation-delay: <?php echo $animation_delay; ?>s">
                        <div class="review-header">
                            <div class="review-avatar">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <div class="review-info">
                                <div class="review-author"><?php echo htmlspecialchars($review['name']); ?></div>
                                <div class="review-date"><?php echo date('d.m.Y', strtotime($review['created_at'])); ?></div>
                            </div>
                        </div>
                        <div class="review-rating">
                            <?php 
                            for($i = 1; $i <= 5; $i++) 
                            {
                                if ($i <= $review['rating']) 
                                {
                                    echo '<i class="bi bi-star-fill text-warning"></i>';
                                }
                                else 
                                {
                                    echo '<i class="bi bi-star text-muted"></i>';
                                } 
                            }
                            ?>
                        </div>
                        <div class="review-text">
                            <p><?php echo nl2br(htmlspecialchars($review['text'])); ?></p>
                        </div>
                    </div>
                </div>
            <?php 
                } 
            }
            else
            { 
            ?>
                <div class="col-12 text-center">
                    <div class="no-reviews">
                        <i class="bi bi-chat-quote fs-1 text-muted mb-3"></i>
                        <p class="text-muted mb-0">Пока нет отзывов. Будьте первым, кто оставит отзыв!</p>
                    </div>
                </div>
            <?php 
            } 
            ?>
        </div>
    </div>
    <?php 
    if ($total_pages > 1) 
    {
    ?>
    <div class="reviews-pagination">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
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
                
                for ($i = $start_page; $i <= $end_page; $i++)
                { 
                ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php 
                }
                ?>
                <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php 
    }
    ?>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>