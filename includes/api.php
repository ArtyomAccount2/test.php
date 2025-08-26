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
            header("Location: " . $_SERVER['REQUEST_URI']);
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
    <title>API для разработчиков - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/api-styles.css">
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
            <h1 class="mb-3" style="padding-top: 60px;">API для разработчиков</h1>
            <p class="lead">Интегрируйте наш каталог в ваши приложения</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="api-documentation">
                <section class="mb-5">
                    <h2 class="mb-4"><i class="bi bi-key"></i> Начало работы</h2>
                    <p>Для доступа к API необходимо получить API ключ. Зарегистрируйте приложение и получите ключ для доступа к данным.</p>
                    <div class="alert alert-info">
                        <strong>Базовый URL API:</strong> https://api.lal-auto.ru/v1/
                    </div>
                    <h4 class="mt-4">Получение API ключа</h4>
                    <ol>
                        <li>Зарегистрируйтесь на сайте</li>
                        <li>Перейдите в личный кабинет → Настройки разработчика</li>
                        <li>Создайте новое приложение</li>
                        <li>Получите API ключ и секрет</li>
                    </ol>
                </section>
                <section class="mb-5">
                    <h2 class="mb-4"><i class="bi bi-code-slash"></i> Эндпоинты API</h2>
                    <div class="api-endpoint">
                        <h4>Получение списка товаров</h4>
                        <div class="endpoint-info">
                            <span class="method get">GET</span>
                            <code>/products</code>
                        </div>
                        <p>Возвращает список товаров с пагинацией и фильтрацией.</p>
                        <h5>Параметры:</h5>
                        <ul>
                            <li><code>page</code> - номер страницы (по умолчанию: 1)</li>
                            <li><code>limit</code> - количество товаров на странице (по умолчанию: 20)</li>
                            <li><code>category</code> - фильтр по категории</li>
                            <li><code>brand</code> - фильтр по бренду</li>
                            <li><code>search</code> - поисковый запрос</li>
                        </ul>
                        <h5>Пример запроса:</h5>
                        <pre><code>GET https://api.lal-auto.ru/v1/products?page=1&limit=10&brand=bosch</code></pre>
                    </div>
                    <div class="api-endpoint">
                        <h4>Получение информации о товаре</h4>
                        <div class="endpoint-info">
                            <span class="method get">GET</span>
                            <code>/products/{id}</code>
                        </div>
                        <p>Возвращает подробную информацию о конкретном товаре.</p>

                        <h5>Пример запроса:</h5>
                        <pre><code>GET https://api.lal-auto.ru/v1/products/12345</code></pre>
                    </div>
                </section>
                <section class="mb-5">
                    <h2 class="mb-4"><i class="bi bi-shield"></i> Аутентификация</h2>
                    <p>Все запросы к API должны содержать API ключ в заголовках:</p>
                    <pre><code>Authorization: Bearer YOUR_API_KEY</code></pre>
                    <div class="alert alert-warning">
                        <strong>Внимание:</strong> Не передавайте API ключ в открытом виде на клиентской стороне.
                    </div>
                </section>
                <section class="mb-5">
                    <h2 class="mb-4"><i class="bi bi-graph-up"></i> Лимиты и квоты</h2>
                    <p>Для предотвращения злоупотреблений установлены следующие лимиты:</p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Тип аккаунта</th>
                                    <th>Запросов в минуту</th>
                                    <th>Запросов в день</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Бесплатный</td>
                                    <td>60</td>
                                    <td>1,000</td>
                                </tr>
                                <tr>
                                    <td>Профессиональный</td>
                                    <td>120</td>
                                    <td>10,000</td>
                                </tr>
                                <tr>
                                    <td>Предприятие</td>
                                    <td>Неограниченно</td>
                                    <td>Неограниченно</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                <section class="mb-5">
                    <h2 class="mb-4"><i class="bi bi-code-square"></i> Примеры кода</h2>
                    <div class="code-example">
                        <h5>JavaScript (Fetch)</h5>
                        <pre><code>let apiKey = 'YOUR_API_KEY';
let url = 'https://api.lal-auto.ru/v1/products?limit=5';

fetch(url, {
    headers: {
        'Authorization': `Bearer ${apiKey}`,
        'Content-Type': 'application/json'
    }
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error('Error:', error));
                        </code></pre>
                    </div>
                    <div class="code-example">
                        <h5>Python (Requests)</h5>
                        <pre><code>import requests
api_key = 'YOUR_API_KEY'
url = 'https://api.lal-auto.ru/v1/products'

headers = {
    'Authorization': f'Bearer {api_key}',
    'Content-Type': 'application/json'
}

response = requests.get(url, headers=headers, params={'limit': 5})
data = response.json()
print(data)
                        </code></pre>
                    </div>
                </section>
                <section class="support-section">
                    <h2 class="mb-4"><i class="bi bi-question-circle"></i> Поддержка разработчиков</h2>
                    <p>Если у вас возникли вопросы по работе с API, обратитесь в нашу службу поддержки:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="support-contact">
                                <h5><i class="bi bi-envelope"></i> Email</h5>
                                <p>api-support@lal-auto.ru</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="support-contact">
                                <h5><i class="bi bi-chat"></i> Форум</h5>
                                <p><a href="#">Сообщество разработчиков</a></p>
                            </div>
                        </div>
                    </div>
                </section>
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