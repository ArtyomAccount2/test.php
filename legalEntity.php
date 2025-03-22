<?php
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лал-Авто - Автозапчасти</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="flex-grow-1">

    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
        <a class="navbar-brand" href="#"><img src="img/Auto.png" alt="Лал-Авто" height="75"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <button class="btn btn-primary ml-2" data-toggle="modal" data-target="#menuModal">Меню</button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link text-dark" href="#">Торговые марки</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#">Поддержка сайта</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#">Новости компании</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#">Оплата и доставка</a></li>
            </ul>
            <a href="index.php" class="btn btn-secondary ml-3">Назад</a>
        </div>
    </nav>

    <div class="modal fade" id="menuModal" tabindex="-1" role="dialog" aria-labelledby="menuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="menuModalLabel">Меню</h5>
                </div>
                <div class="modal-body text-center">
                    <ul class="list-unstyled">
                        <li><a href="#">Магазины</a></li>
                        <li><a href="#">Автосервис</a></li>
                        <li><a href="#">Ассортимент</a></li>
                        <li><a href="#">Масла и тех. жидкости</a></li>
                        <li><a href="#">Аксессуары</a></li>
                        <li><a href="#">Покупателям</a></li>
                        <li><a href="#">Поставщикам</a></li>
                        <li><a href="#">Вакансии</a></li>
                        <li><a href="#">Контакты</a></li>
                        <li><a href="#">Отзывы</a></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5" style="padding-top: 60px;">
        <div class="row">
            <div class="col-md-6">
                <h2 class="text-center" style="padding-bottom: 20px;">Регистрация для юридических лиц и ИП</h2>
                <form action="/" method="POST">
                    <div class="form-group">
                        <label for="organizationType">Наименование организации*</label>
                        <div class="d-flex">
                            <select class="form-control w-25" style="margin-right: 10px;" id="organizationType" required>
                                <option value="#" disabled selected></option>
                                <option value="ООО">ООО</option>
                                <option value="ОАО">ОАО</option>
                                <option value="ЗАО">ЗАО</option>
                                <option value="КПКО">КПКО</option>
                                <option value="МУП">МУП</option>
                                <option value="ИП">ИП</option>
                            </select>
                            <input type="text" class="form-control w-75" id="organizationName" placeholder="Введите название организации" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inn">ИНН*</label>
                        <input type="text" class="form-control w-100" id="inn" placeholder="Введите ИНН" required>
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail*</label>
                        <input type="email" class="form-control w-100" id="email" placeholder="Введите E-mail" required>
                    </div>
                    <div class="form-group">
                        <label for="username">Логин*</label>
                        <input type="text" class="form-control w-100" id="username" placeholder="Введите логин" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль*</label>
                        <input type="password" class="form-control w-100" id="password" placeholder="Введите пароль" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Повтор пароля*</label>
                        <input type="password" class="form-control w-100" id="confirmPassword" placeholder="Повторите пароль" required>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="discountCardCheck">
                        <label class="form-check-label" for="discountCardCheck">Карта скидок</label>
                    </div>
                    <div class="form-group" id="discountCardNumberGroup" style="display: none;">
                        <label for="discountCardNumber">Номер карты скидок (6 символов)</label>
                        <input type="text" class="form-control w-100" id="discountCardNumber" placeholder="Введите номер карты" maxlength="6">
                    </div>
                    <div class="form-group">
                        <label for="region">Регион*</label>
                        <input type="text" class="form-control w-100" id="region" value="Калининград" readonly>
                    </div>
                    <div class="form-group">
                        <label for="city">Город*</label>
                        <input type="text" class="form-control w-100" id="city" placeholder="Введите город" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Адрес*</label>
                        <input type="text" class="form-control w-100" id="address" placeholder="Введите адрес" required>
                    </div>
                    <div class="form-group">
                        <label for="contactPerson">Имя контактного лица*</label>
                        <input type="text" class="form-control w-100" id="contactPerson" placeholder="Введите имя контактного лица" required>
                    </div>
                    <div class="form-group">
                        <label for="contactPhone">Телефон</label>
                        <input type="tel" class="form-control w-100" id="contactPhone">
                    </div>
                    <div class="form-group">
                        <label for="phone">Мобильный телефон*</label>
                        <input type="tel" class="form-control w-100" id="phone" placeholder="+7 911 ___ __" required>
                    </div>
                    <div class="form-group">
                        <label for="captcha">Введите проверочное значение*</label>
                        <input type="text" class="form-control w-100" id="captcha" placeholder="<?= rand(1, 10) . ' + ' . rand(1, 10) . ' + ' . rand(1, 10) . ' ='; ?>" required>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="agreement" required>
                        <label class="form-check-label" for="agreement">Я согласен с Пользовательским соглашением</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                </form>
            </div>
            <div class="col-md-6" style="padding-top: 50px;">
                <p>Вы первый раз на сайте?</p>
                <ol>
                    <li>Если у Вас нет карты скидок компании ЛАЛ-Авто - заполните форму слева.</li>
                    <li>Если у Вас есть карта скидок и Вы не зарегистрированы на нашем сайте - заполните форму слева.</li>
                    <li>Если у Вас есть карта скидок и Вы зарегистрированы на нашем сайте и не ведете свою скидку на сайте, тогда выполните следующие действия: - Добавьте номер карты скидок в одноименное поле в настройках профиля. В этом случае Вам будут доступны к просмотру цены уже с Вашей скидкой.</li>
                </ol>
                <h5>Внимание!</h5>
                <p>При регистрации с указанием карты скидок Вы сможете видеть цены на сайте согласно Вашей скидки. В данный момент размер скидки Вы можете уточнить у любого продавца сети магазинов ЛАЛ-АВТО.</p>
                <p>Увидеть скидку на сайте Вы сможете примерно через сутки. Если этого не произошло, напишите сообщение менеджеру, обязательно подробно опишите причину обращения. <a href="#">Написать сообщение менеджеру.</a></p>
            </div>
        </div>
    </div>

    <?php
        require_once("files/register.php");
    ?>