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
    <title>Условия использования - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/terms-styles.css">
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

<div class="container my-5 pt-4">
    <div class="row">
        <div class="col-12" style="padding-top: 60px;">
            <h1 class="display-5 fw-bold text-primary mb-3">Условия использования сайта</h1>
            <p class="lead text-muted">Последнее обновление: 29 октября 2025 года</p>
            <div class="terms-summary mb-5">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="summary-card text-center p-3 h-100">
                            <i class="bi bi-file-text text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5 class="fw-bold">Правовые условия</h5>
                            <p class="small">Ознакомьтесь с правилами использования нашего сайта</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="summary-card text-center p-3 h-100">
                            <i class="bi bi-shield-check text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5 class="fw-bold">Ваши обязательства</h5>
                            <p class="small">Узнайте о ваших правах и обязанностях при использовании сайта</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="summary-card text-center p-3 h-100">
                            <i class="bi bi-cart-check text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5 class="fw-bold">Порядок покупок</h5>
                            <p class="small">Условия оформления заказов и совершения покупок</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="terms-content">
                <section id="general" class="terms-section mb-5">
                    <h2 class="mb-3">1. Общие положения</h2>
                    <p>1.1. Настоящие Условия использования (далее — Условия) регулируют отношения между ООО "Лал-Авто" (далее — Компания) и пользователями официального сайта www.lal-auto.ru (далее — Сайт).</p>
                    <p>1.2. Используя Сайт, вы соглашаетесь с настоящими Условиями и обязуетесь их соблюдать.</p>
                    <p>1.3. Компания оставляет за собой право изменять Условия в любое время без предварительного уведомления.</p>
                    <div class="info-box p-3 mt-3">
                        <p class="mb-0"><strong>Важно:</strong> Продолжая использование Сайта после внесения изменений в Условия, вы соглашаетесь с новой редакцией.</p>
                    </div>
                </section>
                <section id="intellectual" class="terms-section mb-5">
                    <h2 class="mb-3">2. Интеллектуальная собственность</h2>
                    <p>2.1. Все материалы, размещенные на Сайте, включая тексты, изображения, логотипы, дизайн, являются собственностью Компании или используются с разрешения правообладателей.</p>
                    <p>2.2. Запрещается любое копирование, распространение, изменение материалов Сайта без письменного разрешения Компании.</p>
                    <p>2.3. Товарные знаки и логотипы, размещенные на Сайте, являются собственностью их владельцев.</p>
                    <div class="protection-grid mt-4">
                        <div class="protection-item">
                            <i class="bi bi-c-circle"></i>
                            <span>Авторские права</span>
                        </div>
                        <div class="protection-item">
                            <i class="bi bi-award"></i>
                            <span>Товарные знаки</span>
                        </div>
                        <div class="protection-item">
                            <i class="bi bi-palette"></i>
                            <span>Дизайн и контент</span>
                        </div>
                    </div>
                </section>
                <section id="usage" class="terms-section mb-5">
                    <h2 class="mb-3">3. Использование сайта</h2>
                    <p>3.1. Пользователь обязуется использовать Сайт только в законных целях.</p>
                    <p>3.2. Запрещается:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="styled-list">
                                <li>Размещать ложную или вводящую в заблуждение информацию</li>
                                <li>Нарушать работу Сайта или пытаться получить несанкционированный доступ</li>
                                <li>Размещать вредоносное программное обеспечение</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="styled-list">
                                <li>Совершать действия, нарушающие законодательство РФ</li>
                                <li>Использовать автоматизированные системы для сбора информации</li>
                                <li>Публиковать контент, нарушающий права третьих лиц</li>
                            </ul>
                        </div>
                    </div>
                    <div class="info-box p-3 mt-3">
                        <p class="mb-0"><strong>Примечание:</strong> При нарушении этих правил Компания вправе ограничить доступ к Сайту.</p>
                    </div>
                </section>
                <section id="registration" class="terms-section mb-5">
                    <h2 class="mb-3">4. Регистрация и учетная запись</h2>
                    <p>4.1. Для доступа к некоторым функциям Сайта требуется регистрация.</p>
                    <p>4.2. Пользователь обязан предоставлять достоверную информацию при регистрации.</p>
                    <p>4.3. Пользователь несет ответственность за сохранность своих учетных данных.</p>
                    <p>4.4. Компания вправе заблокировать учетную запись при нарушении Условий.</p>
                    <div class="account-rules mt-4">
                        <div class="rule-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Использовать реальные данные при регистрации</span>
                        </div>
                        <div class="rule-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Хранить логин и пароль в безопасном месте</span>
                        </div>
                        <div class="rule-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Немедленно сообщать о несанкционированном доступе</span>
                        </div>
                    </div>
                </section>
                <section id="orders" class="terms-section mb-5">
                    <h2 class="mb-3">5. Заказы и покупки</h2>
                    <p>5.1. Размещение заказа на Сайте является офертой к заключению договора купли-продажи.</p>
                    <p>5.2. Компания оставляет за собой право отказать в выполнении заказа без объяснения причин.</p>
                    <p>5.3. Цены на товары указаны в рублях и могут изменяться без предварительного уведомления.</p>
                    <p>5.4. Наличие товара и сроки доставки уточняются при оформлении заказа.</p>
                    <div class="order-process mt-4">
                        <div class="process-step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h6>Выбор товара</h6>
                                <p class="small mb-0">Добавьте товары в корзину и проверьте их наличие</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h6>Оформление заказа</h6>
                                <p class="small mb-0">Заполните форму заказа с вашими контактными данными</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h6>Подтверждение</h6>
                                <p class="small mb-0">Дождитесь подтверждения заказа и уточнения деталей</p>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="reviews" class="terms-section mb-5">
                    <h2 class="mb-3">6. Отзывы и комментарии</h2>
                    <p>6.1. Пользователи могут оставлять отзывы о товарах и услугах.</p>
                    <p>6.2. Компания оставляет за собой право модерировать и удалять отзывы, которые:</p>
                    <div class="moderation-grid">
                        <div class="moderation-item">
                            <i class="bi bi-x-circle text-danger"></i>
                            <span>Содержат ненормативную лексику</span>
                        </div>
                        <div class="moderation-item">
                            <i class="bi bi-x-circle text-danger"></i>
                            <span>Нарушают законодательство</span>
                        </div>
                        <div class="moderation-item">
                            <i class="bi bi-x-circle text-danger"></i>
                            <span>Содержат рекламу или спам</span>
                        </div>
                        <div class="moderation-item">
                            <i class="bi bi-x-circle text-danger"></i>
                            <span>Являются оскорбительными или клеветническими</span>
                        </div>
                    </div>
                    <div class="info-box p-3 mt-3">
                        <p class="mb-0"><strong>Рекомендация:</strong> Оставляйте конструктивные отзывы, которые помогут другим покупателям.</p>
                    </div>
                </section>
                <section id="responsibility" class="terms-section mb-5">
                    <h2 class="mb-3">7. Ограничение ответственности</h2>
                    <p>7.1. Компания не несет ответственности за:</p>
                    <div class="responsibility-grid">
                        <div class="responsibility-item">
                            <i class="bi bi-info-circle text-warning"></i>
                            <div>
                                <h6>Неточности в описании</h6>
                                <p class="small mb-0">Неточности в описании товаров</p>
                            </div>
                        </div>
                        <div class="responsibility-item">
                            <i class="bi bi-wifi-off text-warning"></i>
                            <div>
                                <h6>Доступность сайта</h6>
                                <p class="small mb-0">Временную недоступность Сайта</p>
                            </div>
                        </div>
                        <div class="responsibility-item">
                            <i class="bi bi-people text-warning"></i>
                            <div>
                                <h6>Действия третьих лиц</h6>
                                <p class="small mb-0">Действия третьих лиц</p>
                            </div>
                        </div>
                        <div class="responsibility-item">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            <div>
                                <h6>Финансовые убытки</h6>
                                <p class="small mb-0">Убытки, возникшие в результате использования Сайта</p>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3">7.2. Все рекомендации на Сайте носят информационный характер.</p>
                </section>
                <section id="links" class="terms-section mb-5">
                    <h2 class="mb-3">8. Ссылки на другие сайты</h2>
                    <p>8.1. Сайт может содержать ссылки на сторонние ресурсы.</p>
                    <p>8.2. Компания не несет ответственности за содержание и политику конфиденциальности сторонних сайтов.</p>
                    <p>8.3. Размещение ссылок не означает одобрения или рекомендации.</p>
                    <div class="external-links-info p-3 mt-3">
                        <p class="mb-0"><i class="bi bi-box-arrow-up-right me-2"></i> <strong>Внимание:</strong> При переходе по внешним ссылкам ознакомьтесь с политикой конфиденциальности соответствующего сайта.</p>
                    </div>
                </section>
                <section id="law" class="terms-section mb-5">
                    <h2 class="mb-3">9. Применимое право</h2>
                    <p>9.1. Все споры и разногласия регулируются законодательством Российской Федерации.</p>
                    <p>9.2. Компания и пользователь будут стремиться урегулировать все споры путем переговоров.</p>
                    <p>9.3. При невозможности урегулирования спора мирным путем, он подлежит рассмотрению в суде по месту нахождения Компании.</p>
                    <div class="dispute-resolution mt-4">
                        <div class="resolution-step">
                            <i class="bi bi-chat-dots text-primary"></i>
                            <span>Переговоры сторон</span>
                        </div>
                        <div class="resolution-step">
                            <i class="bi bi-envelope text-primary"></i>
                            <span>Письменная претензия</span>
                        </div>
                        <div class="resolution-step">
                            <i class="bi bi-building text-primary"></i>
                            <span>Судебное разбирательство</span>
                        </div>
                    </div>
                </section>
                <section id="contact" class="terms-section mb-5">
                    <h2 class="mb-3">10. Контактная информация</h2>
                    <p>10.1. По всем вопросам, связанным с настоящими Условиями, обращайтесь:</p>
                    <div class="contact-info-box p-4 mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="fw-bold mb-3">ООО "Лал-Авто"</h4>
                                <div class="contact-details">
                                    <div class="contact-item">
                                        <i class="bi bi-geo-alt"></i>
                                        <span>236000, г. Калининград, ул. Автомобильная, д. 12</span>
                                    </div>
                                    <div class="contact-item">
                                        <i class="bi bi-telephone"></i>
                                        <span>+7 (4012) 65-65-65</span>
                                    </div>
                                    <div class="contact-item">
                                        <i class="bi bi-envelope"></i>
                                        <span>info@lal-auto.ru</span>
                                    </div>
                                    <div class="contact-item">
                                        <i class="bi bi-clock"></i>
                                        <span>Пн-Пт: 9:00-18:00, Сб: 10:00-16:00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="support-info">
                                    <h5 class="fw-bold">Служба поддержки</h5>
                                    <p>По вопросам использования сайта и техническим проблемам:</p>
                                    <div class="contact-item">
                                        <i class="bi bi-headset"></i>
                                        <span>support@lal-auto.ru</span>
                                    </div>
                                    <div class="contact-item">
                                        <i class="bi bi-chat-left-dots"></i>
                                        <span>Онлайн-чат на сайте</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <div class="update-info p-3 text-center mt-5">
                    <p class="mb-0"><strong>Последнее обновление условий:</strong> 29 октября 2025 года</p>
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