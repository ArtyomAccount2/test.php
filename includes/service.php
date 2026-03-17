<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");
require_once("../config/check_auth.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
}

$selected_service_id = isset($_GET['service_id']) ? $_GET['service_id'] : '';
$selected_service_name = isset($_GET['service_name']) ? urldecode($_GET['service_name']) : '';
$selected_service_price = isset($_GET['service_price']) ? $_GET['service_price'] : '';

$services_list = [];
$services_query = "SELECT id, name, category, price FROM services WHERE status = 'active' ORDER BY category, name";
$services_result = $conn->query($services_query);

if ($services_result && $services_result->num_rows > 0) 
{
    while ($service_row = $services_result->fetch_assoc()) 
    {
        $services_list[] = $service_row;
    }
}

if (!empty($selected_service_id) || !empty($selected_service_name)) 
{
    $_SESSION['locked_service'] = [
        'id' => $selected_service_id,
        'name' => $selected_service_name,
        'price' => $selected_service_price
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    if (isset($_POST['login']) && isset($_POST['password'])) 
    {
        $login = $_POST['login'];
        $password = $_POST['password'];

        if (strtolower($login) === 'admin' && strtolower($password) === 'admin') 
        {
            $_SESSION['login_error'] = true;
            $_SESSION['error_message'] = "Неверный логин или пароль!";
            header("Location: " . $_SERVER['REQUEST_URI']);
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
                $_SESSION['user_id'] = $row['id_users'];
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
    
    if (isset($_POST['service_submit'])) 
    {
        $form_token = md5(serialize($_POST));
        
        if (isset($_SESSION['last_service_form_token']) && $_SESSION['last_service_form_token'] === $form_token) 
        {
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $car = trim($_POST['car'] ?? '');
        $service_id = $_POST['service'] ?? '';
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $message = trim($_POST['message'] ?? '');
        $consent = isset($_POST['consent']) ? 1 : 0;
        
        $errors = [];
        
        if (empty($name)) 
        {
            $errors[] = "Пожалуйста, введите ваше имя";
        }
        
        if (empty($phone)) 
        {
            $errors[] = "Пожалуйста, введите номер телефона";
        } 
        else if (!preg_match('/^[\+\-\d\s\(\)]{10,}$/', $phone)) 
        {
            $errors[] = "Пожалуйста, введите корректный номер телефона";
        }
        
        if (empty($car)) 
        {
            $errors[] = "Пожалуйста, укажите автомобиль";
        }
        
        if (empty($service_id)) 
        {
            $errors[] = "Пожалуйста, выберите услугу";
        }
        
        if (empty($date)) 
        {
            $errors[] = "Пожалуйста, выберите дату";
        }
        
        if (empty($time)) 
        {
            $errors[] = "Пожалуйста, выберите время";
        }
        
        if (!$consent) 
        {
            $errors[] = "Необходимо ваше согласие на обработку персональных данных";
        }

        if (empty($errors)) 
        {
            $service_name = '';
            $service_price = '';
            
            if (!empty($service_id)) 
            {
                $stmt = $conn->prepare("SELECT name, price FROM services WHERE id = ?");
                $stmt->bind_param("i", $service_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) 
                {
                    $service_name = $row['name'];
                    $service_price = $row['price'];
                }

                $stmt->close();
            }
            
            $insert_query = "INSERT INTO service_requests (name, phone, car, service_id, service_name, service_price, request_date, request_time, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'new')";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sssisssss", $name, $phone, $car, $service_id, $service_name, $service_price, $date, $time, $message);
            
            if ($stmt->execute()) 
            {
                $_SESSION['last_service_form_token'] = $form_token;
                $_SESSION['show_service_success_modal'] = true;
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } 
            else 
            {
                $error_message = "Произошла ошибка при отправке заявки: " . $conn->error;
            }
            $stmt->close();
        } 
        else 
        {
            $error_message = implode("<br>", $errors);
        }
    }
}

$show_success_modal = false;

if (isset($_SESSION['show_service_success_modal']) && $_SESSION['show_service_success_modal'] === true) 
{
    $show_success_modal = true;
    unset($_SESSION['show_service_success_modal']);
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Автосервис - Лал-Авто</title>
    <link rel="icon" href="../img/iconAuto.png" type="image/png" height="32">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/service-styles.css">
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

        if (!empty($selected_service_id) || !empty($selected_service_name))
        {
        ?>
        setTimeout(function() 
        {
            let serviceSelect = document.getElementById('service');

            if (serviceSelect && serviceSelect.disabled) 
            {
                serviceSelect.classList.add('bg-light');

                serviceSelect.addEventListener('keydown', function(e) 
                {
                    e.preventDefault();
                    return false;
                });

                let tooltip = new bootstrap.Tooltip(serviceSelect, {
                    title: 'Для смены услуги перейдите в каталог услуг',
                    placement: 'top'
                });
            }

            if (serviceSelect) 
            {
                <?php 
                if (!empty($selected_service_id))
                {
                ?>
                    for (let option of serviceSelect.options) 
                    {
                        if (option.value == '<?= $selected_service_id ?>') 
                        {
                            option.selected = true;
                            break;
                        }
                    }
                <?php 
                }
                else
                { 
                ?>
                    for (let option of serviceSelect.options) 
                    {
                        if (option.text.includes('<?= addslashes($selected_service_name) ?>')) 
                        {
                            option.selected = true;
                            break;
                        }
                    }
                <?php 
                }
                ?>
            }
            
            document.getElementById('serviceForm').scrollIntoView({ behavior: 'smooth' });

            let serviceItems = document.querySelectorAll('.service-item');

            serviceItems.forEach(item => {
                let itemName = item.querySelector('h6').textContent;

                if (itemName.includes('<?= addslashes($selected_service_name) ?>')) 
                {
                    item.style.backgroundColor = '#e7f1ff';
                    item.style.border = '2px solid #0d6efd';
                    item.style.transition = 'all 0.3s ease';
                }
            });
        }, 500);
        <?php 
        }

        if (!empty($error_message))
        {
        ?>
            setTimeout(function() 
            {
                alert('<?= addslashes($error_message) ?>');
            }, 100);
        <?php 
        }

        if ($show_success_modal)
        {
        ?>
            setTimeout(function() 
            {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            }, 100);
        <?php 
        }
        ?>

        let today = new Date().toISOString().split('T')[0];
        let dateInput = document.getElementById('date');

        if (dateInput) 
        {
            dateInput.min = today;
        }

        function equalizeCardHeights() 
        {
            let cards = document.querySelectorAll('.service-main-card');
            let maxHeight = 0;
            
            cards.forEach(card => {
                card.style.height = 'auto';
                let height = card.offsetHeight;

                if (height > maxHeight) 
                {
                    maxHeight = height;
                }
            });
            
            cards.forEach(card => {
                card.style.height = maxHeight + 'px';
            });
        }
        
        window.addEventListener('load', equalizeCardHeights);
        window.addEventListener('resize', equalizeCardHeights);
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5">
    <div class="hero-section text-center mb-5" style="padding-top: 85px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Профессиональный автосервис</h1>
        <p class="lead text-muted mb-4">Комплексное обслуживание и ремонт автомобилей любой сложности</p>
        <a href="services_list.php" class="btn btn-outline-primary btn-lg">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>Смотреть все услуги
            <span class="badge bg-primary ms-2">20+</span>
        </a>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="service-main-card h-100">
                <div class="service-card h-100">
                    <div class="card-header-custom bg-primary text-white">
                        <div class="d-flex align-items-center">
                            <div class="header-icon me-3">
                                <i class="bi bi-tools"></i>
                            </div>
                            <h2 class="mb-0">Наши услуги</h2>
                        </div>
                    </div>
                    <div class="card-body-custom p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="service-item h-100">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-primary me-3">
                                            <i class="bi bi-search text-white"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Компьютерная диагностика</h6>
                                            <p class="small text-muted mb-2">Полная проверка систем автомобиля</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 1500₽</span>
                                        <span class="service-time small text-muted">20-40 мин</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="service-item h-100">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-primary me-3">
                                            <i class="bi bi-gear-fill text-white"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Техобслуживание</h6>
                                            <p class="small text-muted mb-2">Регулярное ТО по регламенту</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 3000₽</span>
                                        <span class="service-time small text-muted">1-2 часа</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="service-item h-100">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-primary me-3">
                                            <i class="bi bi-lightning-charge text-white"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Ремонт двигателя</h6>
                                            <p class="small text-muted mb-2">Диагностика и ремонт ДВС</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 5000₽</span>
                                        <span class="service-time small text-muted">2-6 часов</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="service-item h-100">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-primary me-3">
                                            <i class="bi bi-car-front text-white"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Ходовая часть</h6>
                                            <p class="small text-muted mb-2">Ремонт подвески и рулевого управления</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 2500₽</span>
                                        <span class="service-time small text-muted">1-3 часа</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="service-item h-100">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-primary me-3">
                                            <i class="bi bi-cpu text-white"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Автоэлектрика</h6>
                                            <p class="small text-muted mb-2">Диагностика и ремонт электронных систем</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 2000₽</span>
                                        <span class="service-time small text-muted">1-4 часа</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="service-item h-100">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-primary me-3">
                                            <i class="bi bi-circle text-white"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Шиномонтаж</h6>
                                            <p class="small text-muted mb-2">Балансировка и сезонная смена шин</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 1000₽</span>
                                        <span class="service-time small text-muted">30-60 мин</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="service-alert mt-4">
                            <div class="alert alert-warning mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-lightning-charge-fill me-3 fs-5"></i>
                                    <div>
                                        <strong>Срочный выездной ремонт!</strong><br>
                                        <small>Выезд мастера в течение 60 минут по Калининграду</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="express-services mt-4">
                            <h6 class="mb-3"><i class="bi bi-clock-fill text-primary me-2"></i>Экспресс-услуги</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="express-service">
                                        <span class="service-name">Замена АКБ</span>
                                        <span class="service-duration">30 мин</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="express-service">
                                        <span class="service-name">Замена ламп</span>
                                        <span class="service-duration">15 мин</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="express-service">
                                        <span class="service-name">Диагностика</span>
                                        <span class="service-duration">20 мин</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="express-service">
                                        <span class="service-name">Шиномонтаж</span>
                                        <span class="service-duration">45 мин</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="service-main-card h-100">
                <div class="service-card h-100">
                    <div class="card-header-custom bg-primary text-white">
                        <div class="d-flex align-items-center">
                            <div class="header-icon me-3">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <h2 class="mb-0">Онлайн-запись</h2>
                        </div>
                    </div>
                    <div class="card-body-custom p-4">
                        <div class="special-offer mb-4">
                            <?php 
                            if (!empty($selected_service_name))
                            {
                            ?>
                                <div class="alert alert-primary mb-0">
                                    <div class="d-flex align-items-center">
                                    <i class="bi bi-info-circle me-2"></i>Вы выбрали:⠀<strong><?= htmlspecialchars($selected_service_name) ?></strong>
                                        <?php 
                                        if (!empty($selected_service_price))
                                        {
                                        ?>
                                            <span class="badge bg-primary ms-2">от <?= number_format($selected_service_price, 0, ',', ' ') ?> ₽</span>
                                        <?php 
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php 
                            }
                            else
                            { 
                            ?>
                            <div class="alert alert-info mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-gift-fill me-3"></i>
                                    <div>
                                        <strong>Скидка 10% при онлайн-записи!</strong><br>
                                        <small>Действует на все виды диагностики</small>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            }
                            ?>
                        </div>
                        <?php 
                        if (!empty($error_message))
                        {
                        ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $error_message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php 
                        }
                        ?>
                        <form id="serviceForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="needs-validation" novalidate>
                            <input type="hidden" name="service_submit" value="1">
                            <?php 
                            if (!empty($selected_service_id))
                            {
                            ?>
                                <input type="hidden" name="locked_service_id" value="<?= htmlspecialchars($selected_service_id) ?>">
                            <?php 
                            }
                            ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-semibold">
                                        <i class="bi bi-person me-1"></i>Ваше имя<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Иван Иванов" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                    <div class="invalid-feedback">Пожалуйста, введите ваше имя</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold">
                                        <i class="bi bi-phone me-1"></i>Телефон<span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="+7 (900) 123-45-67" required pattern="[\+\-\d\s\(\)]{10,}" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                    <div class="invalid-feedback">Пожалуйста, введите корректный телефон</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="car" class="form-label fw-semibold">
                                    <i class="bi bi-car-front me-1"></i>Автомобиль<span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="car" name="car" placeholder="Марка, модель, год" required value="<?php echo htmlspecialchars($_POST['car'] ?? ''); ?>">
                                </div>
                                <div class="invalid-feedback">Пожалуйста, укажите автомобиль</div>
                            </div>
                            <div class="mb-3">
                                <label for="service" class="form-label fw-semibold">
                                    <i class="bi bi-tools me-1"></i>Услуга<span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="service" name="service" required 
                                <?php echo (!empty($selected_service_id) || !empty($selected_service_name)) ? 'disabled' : ''; ?>>
                                    <option value="" <?= empty($selected_service_id) ? 'selected' : '' ?> disabled>Выберите услугу</option>
                                <?php 
                                if (!empty($services_list))
                                {
                                ?>
                                    <?php 
                                    foreach ($services_list as $service)
                                    {
                                        $selected = '';

                                        if ($selected_service_id == $service['id'] || $selected_service_name == $service['name']) 
                                        {
                                            $selected = 'selected';
                                        } 
                                        else if (isset($_POST['service']) && $_POST['service'] == $service['id']) 
                                        {
                                            $selected = 'selected';
                                        }
                                    ?>
                                        <option value="<?= $service['id'] ?>" data-price="<?= $service['price'] ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($service['name']) ?> (от <?= number_format($service['price'], 0, ',', ' ') ?> ₽)
                                        </option>
                                    <?php 
                                    }
                                    ?>
                                <?php 
                                }
                                ?>
                                </select>
                                <?php 
                                if (!empty($selected_service_id) || !empty($selected_service_name))
                                {
                                ?>
                                    <input type="hidden" name="service" value="<?= htmlspecialchars($selected_service_id) ?>">
                                    <div class="form-text text-info mt-2">
                                        <i class="bi bi-lock-fill me-1"></i>Услуга заблокирована для изменения. 
                                        <a href="service.php" class="text-primary">Снять блокировку</a>
                                    </div>
                                <?php 
                                }
                                ?>
                                <div class="invalid-feedback">Пожалуйста, выберите услугу</div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date" class="form-label fw-semibold">
                                        <i class="bi bi-calendar me-1"></i>Дата<span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar-date"></i>
                                        </span>
                                        <input type="date" class="form-control" id="date" name="date" required value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>">
                                    </div>
                                    <div class="invalid-feedback">Пожалуйста, выберите дату</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="time" class="form-label fw-semibold">
                                        <i class="bi bi-clock me-1"></i>Время<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="time" name="time" required>
                                        <option value="" <?php echo empty($_POST['time']) ? 'selected' : ''; ?> disabled>Выберите время</option>
                                        <option value="09:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '09:00') ? 'selected' : ''; ?>>09:00</option>
                                        <option value="10:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '10:00') ? 'selected' : ''; ?>>10:00</option>
                                        <option value="11:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '11:00') ? 'selected' : ''; ?>>11:00</option>
                                        <option value="12:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '12:00') ? 'selected' : ''; ?>>12:00</option>
                                        <option value="13:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '13:00') ? 'selected' : ''; ?>>13:00</option>
                                        <option value="14:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '14:00') ? 'selected' : ''; ?>>14:00</option>
                                        <option value="15:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '15:00') ? 'selected' : ''; ?>>15:00</option>
                                        <option value="16:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '16:00') ? 'selected' : ''; ?>>16:00</option>
                                        <option value="17:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '17:00') ? 'selected' : ''; ?>>17:00</option>
                                        <option value="18:00" <?php echo (isset($_POST['time']) && $_POST['time'] == '18:00') ? 'selected' : ''; ?>>18:00</option>
                                    </select>
                                    <div class="invalid-feedback">Пожалуйста, выберите время</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label fw-semibold">
                                    <i class="bi bi-chat-text me-1"></i>Описание проблемы
                                </label>
                                <textarea class="form-control" id="message" name="message" rows="2" placeholder="Опишите симптомы или проблему с автомобилем..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="consent" name="consent" required <?php echo (isset($_POST['consent']) || empty($_POST)) ? 'checked' : ''; ?>>
                                <label class="form-check-label small" for="consent">Согласен на обработку персональных данных</label>
                                <div class="invalid-feedback">Необходимо ваше согласие</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                <i class="bi bi-calendar-check me-2"></i>Записаться на сервис
                            </button>
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>Подтверждение записи поступит в течение 15 минут
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="service-advantages mb-5">
        <h2 class="text-center mb-4">Преимущества нашего сервиса</h2>
        <div class="row g-3">
            <div class="col-lg-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h6>Сертифицированные мастера</h6>
                    <p class="small text-muted mb-0">Специалисты с опытом работы от 5 лет</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h6>Гарантия 12 месяцев</h6>
                    <p class="small text-muted mb-0">На все виды работ и запчасти</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h6>Прозрачные цены</h6>
                    <p class="small text-muted mb-0">Фиксированная стоимость без доплат</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h6>Срочный ремонт</h6>
                    <p class="small text-muted mb-0">Выезд мастера в течение часа</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle-fill me-2"></i>Заявка отправлена
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Ваша заявка успешно отправлена! Мы свяжемся с вами в ближайшее время.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    var form = document.getElementById('serviceForm');
    
    if (form) 
    {
        form.addEventListener('submit', function(event) 
        {
            if (!form.checkValidity()) 
            {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });
    }

    var phoneInput = document.getElementById('phone');

    if (phoneInput) 
    {
        phoneInput.addEventListener('input', function() 
        {
            this.value = this.value.replace(/[^\d\s\(\)\+\-]/g, '');
        });
    }

    var loginModal = document.getElementById('loginModal');

    if (loginModal) 
    {
        loginModal.addEventListener('show.bs.modal', function() 
        {
            var loginForm = document.querySelector('#loginModal form');

            if (loginForm) 
            {
                loginForm.reset();
            }
        });
    }
});
</script>
</body>
</html>