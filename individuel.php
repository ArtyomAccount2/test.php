<?php
    require_once("files/header.php");
?>

<div class="container my-5" style="padding-top: 85px;">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="shadow-lg border-0 rounded-3 overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-7 p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary">Регистрация для физических лиц</h2>
                            <p class="text-muted">Создайте личный кабинет для доступа ко всем возможностям</p>
                        </div>
                        <form action="files/registerForm.php" method="POST" id="registrationForm" onsubmit="return validateForm();">
                            <div class="mb-4">
                                <label class="form-label fw-semibold" for="fullName">ФИО<span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="surname" class="form-control" id="lastName" placeholder="Фамилия" required>
                                            <label for="lastName">Фамилия</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="username" class="form-control" id="firstName" placeholder="Имя" required>
                                            <label for="firstName">Имя</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="patronymic" class="form-control" id="middleName" placeholder="Отчество" required>
                                            <label for="middleName">Отчество</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Введите E-mail" required>
                                        <label for="email">E-mail<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="tel" name="phone" class="form-control" id="phone" placeholder="+7 911 123 45 67" required>
                                        <label for="phone">Мобильный телефон<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="login" class="form-control" id="login" placeholder="Введите логин" required>
                                        <label for="login">Логин<span class="text-danger">*</span></label>
                                        <div class="form-text">Минимум 4 символа</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" name="password" class="form-control" id="password" placeholder="Введите пароль" required>
                                        <label for="password">Пароль<span class="text-danger">*</span></label>
                                        <div class="form-text">Минимум 6 символов</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" placeholder="Повторите пароль" required>
                                <label for="confirmPassword">Повтор пароля<span class="text-danger">*</span></label>
                                <div class="invalid-feedback" id="passwordError">Пароли не совпадают</div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="discountCardCheck" name="hasDiscountCard">
                                    <label class="form-check-label fw-semibold" for="discountCardCheck">У меня есть карта скидок</label>
                                </div>
                            </div>
                            <div class="mb-3" id="discountCardNumberGroup" style="display: none;">
                                <div class="form-floating">
                                    <input type="text" name="discountCardNumber" class="form-control" id="discountCardNumber" placeholder="Введите номер карты" maxlength="6" pattern="[A-Za-z0-9]{6}">
                                    <label for="discountCardNumber">Номер карты скидок (6 символов)</label>
                                    <div class="form-text">Только буквы и цифры</div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="region" class="form-control" id="region" value="Калининградская область" readonly>
                                        <label for="region">Регион<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="city" class="form-control" id="city" placeholder="Введите город" required>
                                        <label for="city">Город<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" name="address" class="form-control" id="address" placeholder="Введите адрес" required>
                                <label for="address">Адрес<span class="text-danger">*</span></label>
                            </div>
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agreement" required>
                                    <label class="form-check-label" for="agreement">
                                        Я согласен с <a href="#" class="text-decoration-none">Пользовательским соглашением</a> и 
                                        <a href="#" class="text-decoration-none">Политикой конфиденциальности</a><span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-semibold py-3">
                                    <i class="bi bi-person-plus me-2"></i> Зарегистрироваться
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-5 bg-light p-4 p-lg-5">
                        <div class="sticky-top" style="top: 100px;">
                            <h4 class="fw-bold mb-4">Информация для новых клиентов</h4>
                            <div class="d-flex align-items-start mb-4">
                                <div class="bg-primary text-white rounded-circle p-2 me-3 mt-1">
                                    <i class="bi bi-question-circle"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold">Вы первый раз на сайте?</h6>
                                    <p class="small text-muted mb-0">Ознакомьтесь с инструкцией ниже</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <ol class="list-steps">
                                    <li class="mb-3">
                                        <span class="fw-semibold">Нет карты скидок?</span>
                                        <p class="small text-muted mb-0">Просто заполните форму слева для регистрации</p>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-semibold">Есть карта скидок, но не зарегистрированы?</span>
                                        <p class="small text-muted mb-0">Заполните форму и укажите номер карты</p>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-semibold">Уже зарегистрированы, но не видите скидку?</span>
                                        <p class="small text-muted mb-0">Добавьте номер карты в настройках профиля</p>
                                    </li>
                                </ol>
                            </div>
                            <div class="alert alert-warning">
                                <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Внимание!</h6>
                                <p class="small mb-2">При регистрации с указанием карты скидок вы сможете видеть цены на сайте согласно вашей скидке.</p>
                                <p class="small mb-0">Скидка появится на сайте примерно через сутки. Если этого не произошло, <a href="#" class="alert-link">напишите менеджеру</a>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    require_once("files/footer.php");
?>