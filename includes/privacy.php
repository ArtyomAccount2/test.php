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
    <title>Политика конфиденциальности - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/privacy-styles.css">
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
            <h1 class="display-5 fw-bold text-primary mb-3">Политика конфиденциальности</h1>
            <p class="lead text-muted">Последнее обновление: 29 октября 2025 года</p>
            <div class="privacy-summary mb-5">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="summary-card text-center p-3 h-100">
                            <i class="bi bi-shield-check text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5 class="fw-bold">Защита данных</h5>
                            <p class="small">Мы обеспечиваем безопасность ваших персональных данных</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="summary-card text-center p-3 h-100">
                            <i class="bi bi-eye text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5 class="fw-bold">Прозрачность</h5>
                            <p class="small">Четко описываем как и зачем собираем информацию</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="summary-card text-center p-3 h-100">
                            <i class="bi bi-person-check text-primary mb-2" style="font-size: 2rem;"></i>
                            <h5 class="fw-bold">Ваши права</h5>
                            <p class="small">Вы всегда можете управлять своими данными</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="privacy-content">
                <section id="general" class="privacy-section mb-5">
                    <h2 class="mb-3">1. Общие положения</h2>
                    <p>1.1. Настоящая Политика конфиденциальности (далее — Политика) разработана в соответствии с Федеральным законом от 27.07.2006 № 152-ФЗ "О персональных данных" и определяет порядок обработки персональных данных и меры по обеспечению безопасности персональных данных в ООО "Лал-Авто" (далее — Оператор).</p>
                    <p>1.2. Оператор ставит своей важнейшей целью и условием осуществления своей деятельности соблюдение прав и свобод человека и гражданина при обработке его персональных данных, в том числе защиты прав на неприкосновенность частной жизни, личную и семейную тайну.</p>
                    <div class="info-box p-3 mt-3">
                        <p class="mb-0"><strong>Важно:</strong> Используя наш сайт и услуги, вы соглашаетесь с условиями данной Политики конфиденциальности.</p>
                    </div>
                </section>
                <section id="definitions" class="privacy-section mb-5">
                    <h2 class="mb-3">2. Основные понятия</h2>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Термин</th>
                                    <th>Определение</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Персональные данные</strong></td>
                                    <td>Любая информация, относящаяся к прямо или косвенно определенному или определяемому физическому лицу (субъекту персональных данных).</td>
                                </tr>
                                <tr>
                                    <td><strong>Обработка персональных данных</strong></td>
                                    <td>Любое действие (операция) или совокупность действий (операций), совершаемых с использованием средств автоматизации или без использования таких средств с персональными данными.</td>
                                </tr>
                                <tr>
                                    <td><strong>Конфиденциальность персональных данных</strong></td>
                                    <td>Обязательное для соблюдения Оператором или иным получившим доступ к персональным данным лицом требование не допускать их распространения без согласия субъекта персональных данных.</td>
                                </tr>
                                <tr>
                                    <td><strong>Оператор</strong></td>
                                    <td>ООО "Лал-Авто", самостоятельно или совместно с другими лицами организующее и (или) осуществляющее обработку персональных данных.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                <section id="purposes" class="privacy-section mb-5">
                    <h2 class="mb-3">3. Цели сбора персональных данных</h2>
                    <p>3.1. Оператор обрабатывает персональные данные пользователей в следующих целях:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="styled-list">
                                <li>Обработка заказов и предоставление услуг</li>
                                <li>Обратная связь с пользователями</li>
                                <li>Отправка информационных материалов и рекламных рассылок</li>
                                <li>Проведение маркетинговых исследований</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="styled-list">
                                <li>Улучшение качества обслуживания</li>
                                <li>Выполнение обязательств по договорам</li>
                                <li>Обеспечение безопасности услуг</li>
                                <li>Соблюдение требований законодательства</li>
                            </ul>
                        </div>
                    </div>
                    <div class="info-box p-3 mt-3">
                        <p class="mb-0"><strong>Примечание:</strong> Мы собираем только те данные, которые необходимы для достижения заявленных целей.</p>
                    </div>
                </section>
                <section id="data-composition" class="privacy-section mb-5">
                    <h2 class="mb-3">4. Состав персональных данных</h2>
                    <p>4.1. Оператор может обрабатывать следующие персональные данные:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="data-category mb-4">
                                <h5 class="fw-bold text-primary">Основные данные</h5>
                                <ul class="styled-list">
                                    <li>Фамилия, имя, отчество</li>
                                    <li>Контактный телефон</li>
                                    <li>Адрес электронной почты</li>
                                    <li>Почтовый адрес</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="data-category mb-4">
                                <h5 class="fw-bold text-primary">Дополнительные данные</h5>
                                <ul class="styled-list">
                                    <li>Реквизиты документов (для юридических лиц)</li>
                                    <li>Информация о заказах и покупках</li>
                                    <li>Технические данные (IP-адрес, cookies, данные браузера)</li>
                                    <li>История обращений в службу поддержки</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="principles" class="privacy-section mb-5">
                    <h2 class="mb-3">5. Принципы обработки персональных данных</h2>
                    <p>5.1. Обработка персональных данных осуществляется на основе следующих принципов:</p>
                    <div class="principles-grid">
                        <div class="principle-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Законности и справедливости</span>
                        </div>
                        <div class="principle-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Ограничения обработки достижением конкретных целей</span>
                        </div>
                        <div class="principle-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Соответствия содержания и объема обрабатываемых данных заявленным целям</span>
                        </div>
                        <div class="principle-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Достоверности и актуальности данных</span>
                        </div>
                        <div class="principle-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Хранения данных не дольше требуемого времени</span>
                        </div>
                        <div class="principle-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Конфиденциальности и безопасности</span>
                        </div>
                    </div>
                </section>
                <section id="storage" class="privacy-section mb-5">
                    <h2 class="mb-3">6. Сроки хранения персональных данных</h2>
                    <p>6.1. Сроки хранения персональных данных определяются в соответствии с целями обработки и требованиями законодательства.</p>
                    <p>6.2. Персональные данные уничтожаются при:</p>
                    <ul class="styled-list">
                        <li>Достижении целей обработки</li>
                        <li>Истечении сроков хранения</li>
                        <li>Отзыве согласия на обработку</li>
                        <li>Выявлении неправомерной обработки</li>
                        <li>Ликвидации Оператора</li>
                    </ul>
                    <div class="info-box p-3 mt-3">
                        <p class="mb-0"><strong>Сроки хранения:</strong> Персональные данные хранятся в течение 5 лет с момента последнего взаимодействия с пользователем, если иное не предусмотрено законодательством.</p>
                    </div>
                </section>
                <section id="rights" class="privacy-section mb-5">
                    <h2 class="mb-3">7. Права субъектов персональных данных</h2>
                    <p>7.1. Субъект персональных данных имеет право:</p>
                    <div class="rights-grid">
                        <div class="right-item">
                            <i class="bi bi-info-circle text-primary"></i>
                            <div>
                                <h6>На информацию</h6>
                                <p class="small mb-0">Получать информацию об обработке своих данных</p>
                            </div>
                        </div>
                        <div class="right-item">
                            <i class="bi bi-pencil-square text-primary"></i>
                            <div>
                                <h6>На уточнение</h6>
                                <p class="small mb-0">Требовать уточнения, блокирования или уничтожения данных</p>
                            </div>
                        </div>
                        <div class="right-item">
                            <i class="bi bi-x-circle text-primary"></i>
                            <div>
                                <h6>На отзыв согласия</h6>
                                <p class="small mb-0">Отозвать согласие на обработку персональных данных</p>
                            </div>
                        </div>
                        <div class="right-item">
                            <i class="bi bi-shield-exclamation text-primary"></i>
                            <div>
                                <h6>На обжалование</h6>
                                <p class="small mb-0">Обжаловать действия или бездействие Оператора</p>
                            </div>
                        </div>
                        <div class="right-item">
                            <i class="bi bi-arrow-right-circle text-primary"></i>
                            <div>
                                <h6>На доступ</h6>
                                <p class="small mb-0">Получать доступ к своим персональным данным</p>
                            </div>
                        </div>
                        <div class="right-item">
                            <i class="bi bi-file-earmark-text text-primary"></i>
                            <div>
                                <h6>На защиту прав</h6>
                                <p class="small mb-0">Защищать свои права законными способами</p>
                            </div>
                        </div>
                    </div>
                    <div class="info-box p-3 mt-3">
                        <p class="mb-0"><strong>Как реализовать права:</strong> Для реализации своих прав вы можете направить запрос по адресу: <a href="mailto:info@lal-auto.ru">info@lal-auto.ru</a>. Ответ будет предоставлен в течение 30 дней.</p>
                    </div>
                </section>
                <section id="protection" class="privacy-section mb-5">
                    <h2 class="mb-3">8. Меры защиты персональных данных</h2>
                    <p>8.1. Оператор принимает необходимые организационные и технические меры для защиты персональных данных от неправомерного или случайного доступа, уничтожения, изменения, блокирования, копирования, распространения.</p>
                    <p>8.2. Защита данных обеспечивается с помощью:</p>
                    <div class="protection-grid">
                        <div class="protection-item">
                            <i class="bi bi-lock-fill"></i>
                            <span>Систем контроля доступа</span>
                        </div>
                        <div class="protection-item">
                            <i class="bi bi-file-lock-fill"></i>
                            <span>Шифрования передаваемых данных</span>
                        </div>
                        <div class="protection-item">
                            <i class="bi bi-shield-fill"></i>
                            <span>Антивирусной защиты</span>
                        </div>
                        <div class="protection-item">
                            <i class="bi bi-hdd-fill"></i>
                            <span>Регулярного резервного копирования</span>
                        </div>
                        <div class="protection-item">
                            <i class="bi bi-person-badge-fill"></i>
                            <span>Обучения сотрудников</span>
                        </div>
                        <div class="protection-item">
                            <i class="bi bi-incognito"></i>
                            <span>Анонимизации данных при аналитике</span>
                        </div>
                    </div>
                </section>
                <section id="third-parties" class="privacy-section mb-5">
                    <h2 class="mb-3">9. Передача персональных данных третьим лицам</h2>
                    <p>9.1. Оператор может передавать персональные данные:</p>
                    <ul class="styled-list">
                        <li>Курьерским службам (для доставки заказов)</li>
                        <li>Платежным системам (для обработки платежей)</li>
                        <li>Государственным органам (по требованию закона)</li>
                        <li>Партнерам (для выполнения обязательств)</li>
                        <li>Сервисным компаниям (для технической поддержки)</li>
                    </ul>
                    <p>9.2. При передаче данных обеспечивается их конфиденциальность и безопасность.</p>
                    <div class="info-box p-3 mt-3">
                        <p class="mb-0"><strong>Важно:</strong> Мы передаем данные третьим лицам только в объеме, необходимом для выполнения конкретных задач, и требуем от них соблюдения конфиденциальности.</p>
                    </div>
                </section>
                <section id="cookies" class="privacy-section mb-5">
                    <h2 class="mb-3">10. Использование файлов cookie</h2>
                    <p>10.1. Наш сайт использует файлы cookie для улучшения пользовательского опыта и сбора аналитической информации.</p>
                    <p>10.2. Мы используем следующие типы cookies:</p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Тип cookies</th>
                                    <th>Назначение</th>
                                    <th>Срок хранения</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Необходимые</td>
                                    <td>Обеспечение работы основных функций сайта</td>
                                    <td>До закрытия браузера</td>
                                </tr>
                                <tr>
                                    <td>Функциональные</td>
                                    <td>Запоминание ваших предпочтений</td>
                                    <td>До 1 года</td>
                                </tr>
                                <tr>
                                    <td>Аналитические</td>
                                    <td>Сбор информации о использовании сайта</td>
                                    <td>До 2 лет</td>
                                </tr>
                                <tr>
                                    <td>Маркетинговые</td>
                                    <td>Показ релевантной рекламы</td>
                                    <td>До 1 года</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p>10.3. Вы можете управлять настройками cookies через параметры вашего браузера.</p>
                </section>
                <section id="final" class="privacy-section mb-5">
                    <h2 class="mb-3">11. Заключительные положения</h2>
                    <p>11.1. Настоящая Политика является общедоступной и подлежит размещению на официальном сайте Оператора.</p>
                    <p>11.2. Оператор вправе вносить изменения в настоящую Политику. Новая редакция вступает в силу с момента ее размещения на сайте.</p>
                    <p>11.3. Все предложения или вопросы по настоящей Политике следует направлять по адресу: <a href="mailto:info@lal-auto.ru">info@lal-auto.ru</a></p>
                    <p>11.4. В случае изменения законодательства в области защиты персональных данных, Политика будет приведена в соответствие с новыми требованиями.</p>
                    <div class="contact-info-box p-4 mt-4">
                        <h4 class="fw-bold mb-3">Контактная информация</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ООО "Лал-Авто"</strong></p>
                                <p><i class="bi bi-envelope me-2"></i> Email: <a href="mailto:info@lal-auto.ru">info@lal-auto.ru</a></p>
                                <p><i class="bi bi-telephone me-2"></i> Телефон: <a href="tel:+78001234567">8 (800) 123-45-67</a></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Ответственный за обработку персональных данных</strong></p>
                                <p><i class="bi bi-person me-2"></i> Иванов Иван Иванович</p>
                                <p><i class="bi bi-envelope me-2"></i> Email: <a href="mailto:privacy@lal-auto.ru">privacy@lal-auto.ru</a></p>
                            </div>
                        </div>
                    </div>
                </section>
                <div class="update-info p-3 text-center mt-5">
                    <p class="mb-0"><strong>Последнее обновление:</strong> 29 октября 2025 года</p>
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