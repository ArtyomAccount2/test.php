<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $surname = !empty($_POST['surname']) ? $_POST['surname'] : null;
    $name = !empty($_POST['name']) ? $_POST['name'] : null;
    $patronymic = !empty($_POST['patronymic']) ? $_POST['patronymic'] : null;
    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = !empty($_POST['email']) ? $_POST['email'] : null;
    $phone = !empty($_POST['phone']) ? $_POST['phone'] : null;
    $user_type = $_POST['user_type'];

    $stmt = $conn->prepare("INSERT INTO users (surname_users, name_users, patronymic_users, login_users, password_users, email_users, phone_users, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $surname, $name, $patronymic, $login, $password, $email, $phone, $user_type);
    
    if ($stmt->execute()) 
    {
        $_SESSION['success_message'] = 'Пользователь успешно добавлен';
        echo '<script>window.location.href = "admin.php?section=users_list";</script>';
        exit();
    } 
    else 
    {
        $error = "Ошибка при добавлении пользователя: " . $conn->error;
    }
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-person-plus me-2"></i>Добавление пользователя
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=users_list" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            <span class="d-none d-sm-inline">Назад к списку</span>
        </a>
    </div>
</div>

<?php 
if (isset($error)) 
{
?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $error ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php
}
?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Форма добавления пользователя</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">ФИО<span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-4">
                                <input type="text" class="form-control" name="surname" placeholder="Фамилия" required>
                            </div>
                            <div class="col-4">
                                <input type="text" class="form-control" name="name" placeholder="Имя" required>
                            </div>
                            <div class="col-4">
                                <input type="text" class="form-control" name="patronymic" placeholder="Отчество">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Контактная информация<span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="col-6">
                                <input type="tel" class="form-control" name="phone" placeholder="Телефон">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Логин и пароль<span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="text" class="form-control" name="login" placeholder="Логин" required>
                            </div>
                            <div class="col-6">
                                <input type="password" class="form-control" name="password" placeholder="Пароль" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Тип пользователя<span class="text-danger">*</span></label>
                        <select class="form-select" name="user_type" required>
                            <option value="physical">Физическое лицо</option>
                            <option value="legal">Юридическое лицо</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Добавить пользователя
                </button>
            </div>
        </form>
    </div>
</div>