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
            header("Location: ../index.php");
            exit();
        } 
        else 
        {
            $_SESSION['login_error'] = true;
            $_SESSION['error_message'] = "Неверный логин или пароль!";
            $_SESSION['form_data'] = $_POST;
            header("Location: " . $_SERVER['REQUEST_URI']);
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
    <title>Возврат и обмен - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/return-styles.css">
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
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="mb-3" style="padding-top: 60px;">Возврат и обмен товаров</h1>
            <p class="lead">Условия возврата и обмена товаров в соответствии с законодательством РФ</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="return-content">
                <div class="alert alert-info">
                    <strong><i class="bi bi-info-circle"></i> Важно:</strong> Согласно Закону "О защите прав потребителей" вы имеете право на возврат или обмен товара в течение 14 дней с момента покупки.
                </div>
                <section class="mb-5">
                    <h2 class="mb-4"><i class="bi bi-arrow-left-right"></i> Условия возврата и обмена</h2>
                    <div class="conditions-grid">
                        <div class="condition-card">
                            <div class="condition-icon">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h4>Товары надлежащего качества</h4>
                            <p>Можно вернуть или обменить в течение 14 дней при сохранении товарного вида и упаковки</p>
                        </div>
                        <div class="condition-card">
                            <div class="condition-icon">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <h4>Товары ненадлежащего качества</h4>
                            <p>Подлежат возврату или обмену в течение гарантийного срока при наличии дефектов</p>
                        </div>
                        <div class="condition-card">
                            <div class="condition-icon">
                                <i class="bi bi-exclamation-circle"></i>
                            </div>
                            <h4>Не подлежащие возврату</h4>
                            <p>Расходные материалы, автохимия, инструменты и другие товары из перечня</p>
                        </div>
                    </div>
                </section>
                <section class="mb-5">
                    <h2 class="mb-4"><i class="bi bi-clock"></i> Сроки возврата</h2>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="bi bi-1-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h5>14 дней</h5>
                                <p>Для товаров надлежащего качества с момента покупки</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="bi bi-2-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h5>Гарантийный срок</h5>
                                <p>Для товаров с дефектами или браком в течение гарантийного периода</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="bi bi-3-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h5>7-10 дней</h5>
                                <p>Обработка заявления и возврат денежных средств</p>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="mb-5">
                    <h2 class="mb-4"><i class="bi bi-file-text"></i> Необходимые документы</h2>
                    <div class="documents-list">
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <div class="document-info">
                                <h5>Товарный чек или накладная</h5>
                                <p>Подтверждение покупки в нашем магазине</p>
                            </div>
                        </div>
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="bi bi-card-text"></i>
                            </div>
                            <div class="document-info">
                                <h5>Заявление на возврат</h5>
                                <p>Заполненное по установленной форме</p>
                            </div>
                        </div>
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="document-info">
                                <h5>Паспорт или удостоверение</h5>
                                <p>Документ, удостоверяющий личность</p>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="mb-5">
                    <h2 class="mb-4"><i class="bi bi-currency-exchange"></i> Возврат денежных средств</h2>
                    <div class="refund-methods">
                        <div class="refund-method">
                            <div class="method-icon">
                                <i class="bi bi-cash"></i>
                            </div>
                            <h5>Наличными</h5>
                            <p>При возврате в магазине в день обращения</p>
                        </div>
                        <div class="refund-method">
                            <div class="method-icon">
                                <i class="bi bi-credit-card"></i>
                            </div>
                            <h5>На банковскую карту</h5>
                            <p>В течение 3-10 рабочих дней на карту, с которой была оплата</p>
                        </div>
                        <div class="refund-method">
                            <div class="method-icon">
                                <i class="bi bi-bank"></i>
                            </div>
                            <h5>Безналичный расчет</h5>
                            <p>Для юридических лиц на расчетный счет</p>
                        </div>
                    </div>
                </section>
                <section class="mb-5">
                    <h2 class="mb-4"><i class="bi bi-list-check"></i> Процедура возврата</h2>
                    <div class="process-steps">
                        <div class="process-step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h5>Обращение в магазин</h5>
                                <p>Принесите товар с документами в любой из наших магазинов</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h5>Проверка товара</h5>
                                <p>Наш специалист проверит состояние товара и документы</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h5>Заполнение заявления</h5>
                                <p>Заполните заявление на возврат по установленной форме</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h5>Получение средств</h5>
                                <p>Получите денежные средства выбранным способом</p>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="mb-5">
                    <h2 class="mb-4"><i class="bi bi-question-circle"></i> Частые вопросы</h2>
                    <div class="accordion" id="returnFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Какие товары не подлежат возврату?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <p>Согласно законодательству, не подлежат возврату:</p>
                                    <ul>
                                        <li>Автомобильные масла и технические жидкости</li>
                                        <li>Автохимия и аэрозоли</li>
                                        <li>Фильтры и расходные материалы</li>
                                        <li>Инструменты и оборудование</li>
                                        <li>Товары, упакованные в герметичную упаковку</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Что делать, если товар был в употреблении?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Товары, бывшие в употреблении, возврату не подлежат, за исключением случаев:</p>
                                    <ul>
                                        <li>Обнаружения производственного брака</li>
                                        <li>Несоответствия заявленным характеристикам</li>
                                        <li>Нарушения гарантийных обязательств</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Как вернуть товар, купленный онлайн?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Для возврата товара, купленного онлайн:</p>
                                    <ol>
                                        <li>Свяжитесь с нами по телефону +7 (4012) 65-65-65</li>
                                        <li>Подготовьте товар и документы</li>
                                        <li>Отправьте товар через транспортную компанию или привезите в магазин</li>
                                        <li>После проверки получите возврат на карту или счет</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="contact-section">
                    <div class="bg-light">
                        <div class="card-body text-center p-4">
                            <h3 class="mb-3">Нужна помощь с возвратом?</h3>
                            <p class="mb-4">Наши специалисты готовы помочь вам с процедурой возврата</p>
                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                <a href="tel:+74012656565" class="btn btn-primary">
                                    <i class="bi bi-telephone"></i> +7 (4012) 65-65-65
                                </a>
                                <a href="contacts.php" class="btn btn-outline-primary">
                                    <i class="bi bi-geo-alt"></i> Наши магазины
                                </a>
                                <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#returnFormModal">
                                    <i class="bi bi-envelope"></i> Онлайн-заявка
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="returnFormModal" tabindex="-1" aria-labelledby="returnFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnFormModalLabel">Заявка на возврат товара</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="returnRequestForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="returnName" class="form-label">Ваше имя *</label>
                                <input type="text" class="form-control" id="returnName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="returnPhone" class="form-label">Телефон *</label>
                                <input type="tel" class="form-control" id="returnPhone" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="returnEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="returnEmail">
                    </div>
                    <div class="mb-3">
                        <label for="returnOrderNumber" class="form-label">Номер заказа *</label>
                        <input type="text" class="form-control" id="returnOrderNumber" required>
                    </div>
                    <div class="mb-3">
                        <label for="returnProduct" class="form-label">Наименование товара *</label>
                        <input type="text" class="form-control" id="returnProduct" required>
                    </div>
                    <div class="mb-3">
                        <label for="returnReason" class="form-label">Причина возврата *</label>
                        <select class="form-select" id="returnReason" required>
                            <option value="" selected disabled>Выберите причину</option>
                            <option>Не подошел размер</option>
                            <option>Не подошел цвет</option>
                            <option>Не подошел фасон</option>
                            <option>Бракованный товар</option>
                            <option>Другая причина</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="returnDescription" class="form-label">Описание проблемы</label>
                        <textarea class="form-control" id="returnDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="returnPhotos" class="form-label">Фотографии товара (если есть)</label>
                        <input type="file" class="form-control" id="returnPhotos" multiple accept="image/*">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="returnAgree" required>
                        <label class="form-check-label" for="returnAgree">Я согласен на обработку персональных данных</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Отправить заявку</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>