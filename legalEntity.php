<?php
    require_once("files/header.php");
?>

<div class="container my-5" style="padding-top: 85px;">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="shadow-lg border-0 rounded-3 overflow-hidden">
                <div class="row g-0">
                    <div class="col-lg-7 p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary">Регистрация для юридических лиц и ИП</h2>
                            <p class="text-muted">Создайте корпоративный аккаунт для бизнеса</p>
                        </div>
                        <form action="files/registerForm.php" method="POST" id="registrationForm" onsubmit="return validateForm();">
                            <div class="mb-4">
                                <label class="form-label fw-semibold" for="organizationType">Наименование организации<span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select name="organizationType" class="form-select" id="organizationType" required>
                                                <option value="" disabled selected></option>
                                                <option value="ООО">ООО</option>
                                                <option value="ОАО">ОАО</option>
                                                <option value="ЗАО">ЗАО</option>
                                                <option value="КПКО">КПКО</option>
                                                <option value="МУП">МУП</option>
                                                <option value="ИП">ИП</option>
                                            </select>
                                            <label for="organizationType">Тип</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-floating">
                                            <input type="text" name="organization" class="form-control" id="organizationName" placeholder="Введите название организации" required>
                                            <label for="organizationName">Название организации</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" name="TIN" class="form-control" id="inn" placeholder="Введите ИНН" required pattern="\d{10,12}">
                                <label for="inn">ИНН<span class="text-danger">*</span></label>
                                <div class="form-text">10 цифр для юрлиц, 12 для ИП</div>
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
                            <div class="form-floating mb-3">
                                <input type="text" name="person" class="form-control" id="contactPerson" placeholder="Введите имя контактного лица" required>
                                <label for="contactPerson">Имя контактного лица<span class="text-danger">*</span></label>
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
                                    <label class="form-check-label fw-semibold" for="discountCardCheck">У нас есть карта скидок</label>
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
                                    <i class="bi bi-building me-2"></i> Зарегистрировать организацию
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-5 bg-light p-4 p-lg-5">
                        <div style="top: 100px;">
                            <h4 class="fw-bold mb-4">Преимущества для бизнеса</h4>
                            <div class="d-flex align-items-start mb-4">
                                <div class="bg-primary text-white rounded-circle p-2 me-3 mt-1">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold">Корпоративные скидки</h6>
                                    <p class="small text-muted mb-0">Специальные условия для организаций</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3">Процесс регистрации:</h6>
                                <ol class="list-steps">
                                    <li class="mb-3">
                                        <span class="fw-semibold">Заполните данные организации</span>
                                        <p class="small text-muted mb-0">Укажите полное наименование и ИНН</p>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-semibold">Укажите контактные данные</span>
                                        <p class="small text-muted mb-0">Для связи и получения документов</p>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-semibold">Создайте учетную запись</span>
                                        <p class="small text-muted mb-0">Логин и пароль для входа в систему</p>
                                    </li>
                                </ol>
                            </div>
                            <div class="alert alert-info">
                                <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Для бизнеса</h6>
                                <p class="small mb-2">После регистрации наш менеджер свяжется с вами для подтверждения данных и активации корпоративного аккаунта.</p>
                            </div>
                            <div class="alert alert-warning">
                                <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Карта скидок</h6>
                                <p class="small mb-0">При регистрации с указанием карты скидок вы сможете видеть цены на сайте согласно вашей скидке.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('discountCardCheck').addEventListener('change', function() 
{
    document.getElementById('discountCardNumberGroup').style.display = this.checked ? 'block' : 'none';
});
</script>

<?php
    require_once("files/footer.php");
?>