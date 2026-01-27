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

$requisites_by_category = [];
$categories = [
    'general' => ['icon' => 'building', 'title' => 'Общая информация'],
    'bank' => ['icon' => 'bank', 'title' => 'Банковские реквизиты'],
    'address' => ['icon' => 'geo-alt', 'title' => 'Адреса и контакты'],
    'management' => ['icon' => 'person', 'title' => 'Руководство']
];

foreach ($categories as $category => $category_info) 
{
    $stmt = $conn->prepare("SELECT title, value, copy_value FROM company_requisites WHERE category = ? ORDER BY display_order");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $requisites_by_category[$category] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$documents = [];
$stmt = $conn->prepare("SELECT title, description, file_name, file_size FROM company_documents ORDER BY display_order");
$stmt->execute();
$result = $stmt->get_result();
$documents = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$phone = '+7 (4012) 65-65-65';
$email = 'info@lal-auto.ru';

foreach ($requisites_by_category['address'] as $item) 
{
    if ($item['title'] === 'Телефон') 
    {
        $phone = $item['value'];
    }

    if ($item['title'] === 'Email') 
    {
        $email = $item['value'];
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реквизиты - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/requisites-styles.css">
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
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold text-primary mb-3" style="padding-top: 60px;">Реквизиты компании</h1>
            <p class="lead fs-5 text-muted">Официальная информация о компании ООО "Лал-Авто"</p>
        </div>
    </div>
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
        <div>
            <strong>Актуальная информация:</strong> Реквизиты действительны на <?php echo date('d.m.Y'); ?> года. 
            Для получения печатной версии обратитесь в бухгалтерию.
        </div>
    </div>
    <div class="row g-4 requisites-row">
        <?php 
        foreach ($categories as $category => $category_info)
        {
        ?>
            <?php 
            if (!empty($requisites_by_category[$category]))
            {
            ?>
                <div class="col-lg-12">
                    <div class="requisites-card w-100">
                        <div class="card-header-custom">
                            <i class="bi bi-<?php echo $category_info['icon']; ?> fs-2 me-3"></i>
                            <h3 class="mb-0"><?php echo $category_info['title']; ?></h3>
                        </div>
                        <div class="requisites-list">
                            <?php 
                            foreach ($requisites_by_category[$category] as $item)
                            {
                            ?>
                                <div class="requisite-item">
                                    <div class="requisite-content">
                                        <span class="requisite-label"><?php echo htmlspecialchars($item['title']); ?>:</span>
                                        <span class="requisite-value"><?php echo htmlspecialchars($item['value']); ?></span>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary copy-btn" 
                                            data-copy="<?php echo htmlspecialchars($item['copy_value'] ?? $item['value']); ?>"
                                            onclick="copyText(this)">
                                        <i class="bi bi-copy"></i>
                                    </button>
                                </div>
                            <?php 
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php 
            }
            ?>
        <?php 
        }
        ?>
    </div>

    <?php 
    if (!empty($documents))
    {
    ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="documents-section">
                <div class="section-header text-center mb-5">
                    <h2 class="display-5 fw-bold text-primary"><i class="bi bi-file-earmark-text me-3"></i>Документы компании</h2>
                    <p class="lead text-muted">Официальные документы для скачивания</p>
                </div>
                <div class="row g-4">
                    <?php 
                    foreach ($documents as $document)
                    {
                    ?>
                        <div class="col-lg-4">
                            <div class="document-card">
                                <div class="document-icon">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </div>
                                <h5><?php echo htmlspecialchars($document['title']); ?></h5>
                                <p class="document-meta">PDF, <?php echo htmlspecialchars($document['file_size']); ?></p>
                                <p class="document-desc"><?php echo htmlspecialchars($document['description']); ?></p>
                                <a href="#" class="btn btn-primary download-btn" 
                                   data-filename="<?php echo htmlspecialchars($document['file_name']); ?>"
                                   onclick="downloadFile(this)">
                                    <i class="bi bi-download me-2"></i>Скачать
                                </a>
                            </div>
                        </div>
                    <?php 
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php 
    }
    ?>
    
    <div class="row mt-5">
        <div class="col-12">
            <div class="support-section bg-primary text-white rounded-3 p-5 text-center position-relative overflow-hidden">
                <div class="position-relative z-1">
                    <h3 class="mb-3 fw-bold">Нужна помощь с реквизитами?</h3>
                    <p class="lead mb-4 opacity-90">Наши специалисты готовы помочь с оформлением документов и ответить на все вопросы</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $phone); ?>" class="btn btn-light btn-lg fw-bold px-4 py-2">
                            <i class="bi bi-telephone me-2"></i><?php echo htmlspecialchars($phone); ?>
                        </a>
                        <a href="mailto:<?php echo htmlspecialchars($email); ?>" class="btn btn-outline-light btn-lg fw-bold px-4 py-2">
                            <i class="bi bi-envelope me-2"></i>Написать на почту
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>

<script>
function copyText(button) 
{
    let textToCopy = button.getAttribute('data-copy');
    let textArea = document.createElement('textarea');

    textArea.value = textToCopy;
    document.body.appendChild(textArea);

    textArea.select();
    textArea.setSelectionRange(0, 99999);
    
    try 
    {
        let successful = document.execCommand('copy');

        if (successful) 
        {
            let originalHTML = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check"></i>';
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-success');

            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
        } 
        else 
        {
            alert('Не удалось скопировать текст. Попробуйте выделить и скопировать вручную.');
        }
    } 
    catch (err) 
    {
        alert('Ошибка при копировании. Текст для копирования: ' + textToCopy);
    }
    
    document.body.removeChild(textArea);
}

function downloadFile(link) 
{
    let fileName = link.getAttribute('data-filename');
    let originalHTML = link.innerHTML;

    link.innerHTML = '<i class="bi bi-download"></i> Скачивание...';
    link.classList.add('disabled');

    setTimeout(() => {
        window.location.href = '../downloads/' + fileName;
        
        alert('Файл "' + fileName + '" будет скачан');

        link.innerHTML = originalHTML;
        link.classList.remove('disabled');
    }, 1000);
    
    return false;
}

document.addEventListener('DOMContentLoaded', function() 
{
    let copyButtons = document.querySelectorAll('.copy-btn');
    copyButtons.forEach(btn => {
        btn.setAttribute('title', 'Копировать в буфер обмена');
    });
    
    let downloadButtons = document.querySelectorAll('.download-btn');
    downloadButtons.forEach(btn => {
        btn.setAttribute('title', 'Скачать документ');
    });
});
</script>
</body>
</html>