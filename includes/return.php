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

        let returnForm = document.getElementById('returnRequestForm');

        if (returnForm) 
        {
            returnForm.addEventListener('submit', function(e) 
            {
                e.preventDefault();

                if (this.checkValidity()) 
                {
                    let submitBtn = this.querySelector('button[type="submit"]');
                    let originalText = submitBtn.innerHTML;
                    
                    submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Заявка отправлена';
                    submitBtn.disabled = true;
                    
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('returnFormModal')).hide();
                        this.reset();
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 2000);
                }
            });
        }

        let observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        let observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) 
                {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.condition-card, .timeline-item, .document-item, .refund-method, .process-step').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-4 pt-4">
    <div class="row mb-4">
        <div class="col-12 text-center" style="padding-top: 85px;">
            <h1 class="display-5 fw-bold text-primary mb-3">Возврат и обмен товаров</h1>
            <p class="lead text-muted mb-4">Условия возврата и обмена товаров в соответствии с законодательством РФ</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="return-content">
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle me-3 fs-5"></i>
                        <div>
                            <strong>Важно:</strong> Согласно Закону "О защите прав потребителей" вы имеете право на возврат или обмен товара в течение 14 дней с момента покупки.
                        </div>
                    </div>
                </div>
                <section class="section-header mb-5">
                    <h2 class="mb-4"><i class="bi bi-arrow-left-right me-2"></i>Условия возврата и обмена</h2>
                    <div class="conditions-grid">
                        <div class="condition-card">
                            <div class="condition-icon text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h4>Товары надлежащего качества</h4>
                            <p class="mb-0">Можно вернуть или обменить в течение 14 дней при сохранении товарного вида и упаковки</p>
                        </div>
                        <div class="condition-card">
                            <div class="condition-icon text-danger">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <h4>Товары ненадлежащего качества</h4>
                            <p class="mb-0">Подлежат возврату или обмену в течение гарантийного срока при наличии дефектов</p>
                        </div>
                        <div class="condition-card">
                            <div class="condition-icon text-warning">
                                <i class="bi bi-exclamation-circle"></i>
                            </div>
                            <h4>Не подлежащие возврату</h4>
                            <p class="mb-0">Расходные материалы, автохимия, инструменты и другие товары из перечня</p>
                        </div>
                    </div>
                </section>
                <section class="section-header mb-5">
                    <h2 class="mb-4"><i class="bi bi-clock me-2"></i>Сроки возврата</h2>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="bi bi-1-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h5 class="mb-2">14 дней</h5>
                                <p class="mb-0">Для товаров надлежащего качества с момента покупки</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="bi bi-2-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h5 class="mb-2">Гарантийный срок</h5>
                                <p class="mb-0">Для товаров с дефектами или браком в течение гарантийного периода</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="bi bi-3-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <h5 class="mb-2">7-10 дней</h5>
                                <p class="mb-0">Обработка заявления и возврат денежных средств</p>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="section-header mb-5">
                    <h2 class="mb-4"><i class="bi bi-file-text me-2"></i>Необходимые документы</h2>
                    <div class="documents-list">
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <div class="document-info">
                                <h5 class="mb-2">Товарный чек или накладная</h5>
                                <p class="mb-0">Подтверждение покупки в нашем магазине</p>
                            </div>
                        </div>
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="bi bi-card-text"></i>
                            </div>
                            <div class="document-info">
                                <h5 class="mb-2">Заявление на возврат</h5>
                                <p class="mb-0">Заполненное по установленной форме</p>
                            </div>
                        </div>
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="document-info">
                                <h5 class="mb-2">Паспорт или удостоверение</h5>
                                <p class="mb-0">Документ, удостоверяющий личность</p>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="section-header mb-5">
                    <h2 class="mb-4"><i class="bi bi-currency-exchange me-2"></i>Возврат денежных средств</h2>
                    <div class="refund-methods">
                        <div class="refund-method">
                            <div class="method-icon text-success">
                                <i class="bi bi-cash"></i>
                            </div>
                            <h5 class="mb-2">Наличными</h5>
                            <p class="mb-0">При возврате в магазине в день обращения</p>
                        </div>
                        <div class="refund-method">
                            <div class="method-icon text-primary">
                                <i class="bi bi-credit-card"></i>
                            </div>
                            <h5 class="mb-2">На банковскую карту</h5>
                            <p class="mb-0">В течение 3-10 рабочих дней на карту, с которой была оплата</p>
                        </div>
                        <div class="refund-method">
                            <div class="method-icon text-info">
                                <i class="bi bi-bank"></i>
                            </div>
                            <h5 class="mb-2">Безналичный расчет</h5>
                            <p class="mb-0">Для юридических лиц на расчетный счет</p>
                        </div>
                    </div>
                </section>
                <section class="section-header mb-5">
                    <h2 class="mb-4"><i class="bi bi-list-check me-2"></i>Процедура возврата</h2>
                    <div class="process-steps">
                        <div class="process-step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h5 class="mb-2">Обращение в магазин</h5>
                                <p class="mb-0">Принесите товар с документами в любой из наших магазинов</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h5 class="mb-2">Проверка товара</h5>
                                <p class="mb-0">Наш специалист проверит состояние товара и документы</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h5 class="mb-2">Заполнение заявления</h5>
                                <p class="mb-0">Заполните заявление на возврат по установленной форме</p>
                            </div>
                        </div>
                        <div class="process-step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h5 class="mb-2">Получение средств</h5>
                                <p class="mb-0">Получите денежные средства выбранным способом</p>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="section-header mb-5">
                    <h2 class="mb-4"><i class="bi bi-question-circle me-2"></i>Частые вопросы</h2>
                    <div class="accordion" id="returnFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                                    Какие товары не подлежат возврату?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#returnFAQ">
                                <div class="accordion-body">
                                    <p class="mb-3">Согласно законодательству, не подлежат возврату:</p>
                                    <ul class="mb-0">
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
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                                    Что делать, если товар был в употреблении?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#returnFAQ">
                                <div class="accordion-body">
                                    <p class="mb-3">Товары, бывшие в употреблении, возврату не подлежат, за исключением случаев:</p>
                                    <ul class="mb-0">
                                        <li>Обнаружения производственного брака</li>
                                        <li>Несоответствия заявленным характеристикам</li>
                                        <li>Нарушения гарантийных обязательств</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                                    Как вернуть товар, купленный онлайн?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#returnFAQ">
                                <div class="accordion-body">
                                    <p class="mb-3">Для возврата товара, купленного онлайн:</p>
                                    <ol class="mb-0">
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
                    <div class="contact-card">
                        <div class="card-body text-center p-4">
                            <h3 class="mb-3">Нужна помощь с возвратом?</h3>
                            <p class="mb-4">Наши специалисты готовы помочь вам с процедурой возврата</p>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <a href="tel:+74012656565" class="btn btn-primary">
                                    <i class="bi bi-telephone me-2"></i>+7 (4012) 65-65-65
                                </a>
                                <a href="contacts.php" class="btn btn-outline-primary">
                                    <i class="bi bi-geo-alt me-2"></i>Наши магазины
                                </a>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#returnFormModal">
                                    <i class="bi bi-envelope me-2"></i>Онлайн-заявка
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="returnFormModal" tabindex="-1" aria-labelledby="returnFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnFormModalLabel">Заявка на возврат товара</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="returnRequestForm" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="returnName" class="form-label fw-semibold">Ваше имя<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="returnName" placeholder="Иванов Иван Иванович" required>
                            <div class="invalid-feedback">Пожалуйста, введите ваше имя</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="returnPhone" class="form-label fw-semibold">Телефон<span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="returnPhone" placeholder="+7 (900) 123-45-67" required>
                            <div class="invalid-feedback">Пожалуйста, введите корректный телефон</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="returnEmail" class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" id="returnEmail" placeholder="example@mail.ru">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="returnOrderNumber" class="form-label fw-semibold">Номер заказа<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="returnOrderNumber" placeholder="№123456" required>
                            <div class="invalid-feedback">Пожалуйста, введите номер заказа</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="returnProduct" class="form-label fw-semibold">Наименование товара<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="returnProduct" placeholder="Масло моторное 5W-30" required>
                            <div class="invalid-feedback">Пожалуйста, укажите товар</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="returnReason" class="form-label fw-semibold">Причина возврата<span class="text-danger">*</span></label>
                        <select class="form-select" id="returnReason" required>
                            <option value="" selected disabled>Выберите причину</option>
                            <option>Не подошел размер</option>
                            <option>Не подошел цвет</option>
                            <option>Не подошел фасон</option>
                            <option>Бракованный товар</option>
                            <option>Другая причина</option>
                        </select>
                        <div class="invalid-feedback">Пожалуйста, выберите причину</div>
                    </div>
                    <div class="mb-3">
                        <label for="returnDescription" class="form-label fw-semibold">Описание проблемы</label>
                        <textarea class="form-control" id="returnDescription" rows="3" placeholder="Опишите проблему с товаром..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Фотографии товара (если есть)</label>
                        <div class="file-upload">
                            <label for="returnPhotos" class="file-upload-btn">
                                <i class="bi bi-cloud-arrow-up fs-3"></i>
                                <p class="mb-1">Перетащите файлы сюда или нажмите для выбора</p>
                                <small class="text-muted">Форматы: JPG, PNG, PDF (до 5MB каждый)</small>
                            </label>
                            <input type="file" class="form-control d-none" id="returnPhotos" multiple accept="image/*,.pdf">
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="returnAgree" required>
                        <label class="form-check-label small" for="returnAgree">Я согласен на обработку персональных данных</label>
                        <div class="invalid-feedback">Необходимо ваше согласие</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="returnRequestForm" class="btn btn-primary w-100">
                    <i class="bi bi-send me-2"></i>Отправить заявку
                </button>
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