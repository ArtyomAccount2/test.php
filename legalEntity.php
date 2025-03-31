    <?php
        require_once("files/header.php");
    ?>

    <div class="container my-5" style="padding-top: 75px;">
        <div class="row">
            <div class="col-md-6">
                <h2 class="text-center" style="padding-bottom: 20px;">Регистрация для юридических лиц и ИП</h2>
                <form action="files/registerForm.php" method="POST" onsubmit="return validateForm();">
                    <div class="form-group">
                        <label class="d-flex" id="label" for="organizationType">Наименование организации<p class="text-danger">*</p></label>
                        <div class="d-flex">
                            <select name="organizationType" class="form-control w-25" style="margin-right: 10px;" id="organizationType" required>
                                <option value="#" disabled selected></option>
                                <option value="ООО">ООО</option>
                                <option value="ОАО">ОАО</option>
                                <option value="ЗАО">ЗАО</option>
                                <option value="КПКО">КПКО</option>
                                <option value="МУП">МУП</option>
                                <option value="ИП">ИП</option>
                            </select>
                            <input type="text" name="organization" class="form-control w-75" id="organizationName" placeholder="Введите название организации" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="d-flex" id="label" for="inn">ИНН<p class="text-danger">*</p></label>
                        <input type="text" name="TIN" class="form-control w-100" id="inn" placeholder="Введите ИНН" required>
                    </div>
                    <div class="form-group">
                        <label class="d-flex" id="label" for="email">E-mail<p class="text-danger">*</p></label>
                        <input type="email" name="email" class="form-control w-100" id="email" placeholder="Введите E-mail" required>
                    </div>
                    <div class="form-group">
                        <label class="d-flex" id="label" for="login">Логин<p class="text-danger">*</p></label>
                        <input type="text" name="login" class="form-control w-100" id="login" placeholder="Введите логин" required>
                    </div>
                    <div class="form-group">
                        <label class="d-flex" id="label" for="password">Пароль<p class="text-danger">*</p></label>
                        <input type="password" name="password" class="form-control w-100" id="password" placeholder="Введите пароль" required>
                    </div>
                    <div class="form-group">
                        <label class="d-flex" id="label" for="confirmPassword">Повтор пароля<p class="text-danger">*</p></label>
                        <input type="password" name="password" class="form-control w-100" id="confirmPassword" placeholder="Повторите пароль" required>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="discountCardCheck">
                        <label class="form-check-label" for="discountCardCheck">Карта скидок</label>
                    </div>
                    <div class="form-group" id="discountCardNumberGroup" style="display: none;">
                        <label for="discountCardNumber">Номер карты скидок (6 символов)</label>
                        <input type="text" name="discountCardNumber" class="form-control w-100" id="discountCardNumber" placeholder="Введите номер карты" maxlength="6">
                    </div>
                    <div class="form-group">
                        <label class="d-flex" id="label" for="region">Регион<p class="text-danger">*</p></label>
                        <input type="text" name="region" class="form-control w-100" id="region" value="Калининградская область" readonly>
                    </div>
                    <div class="form-group">
                        <label class="d-flex" id="label" for="city">Город<p class="text-danger">*</p></label>
                        <input type="text" name="city" class="form-control w-100" id="city" placeholder="Введите город" required>
                    </div>
                    <div class="form-group">
                        <label class="d-flex" id="label" for="address">Адрес<p class="text-danger">*</p></label>
                        <input type="text" name="address" class="form-control w-100" id="address" placeholder="Введите адрес" required>
                    </div>
                    <div class="form-group">
                        <label class="d-flex" id="label" for="contactPerson">Имя контактного лица<p class="text-danger">*</p></label>
                        <input type="text" name="person" class="form-control w-100" id="contactPerson" placeholder="Введите имя контактного лица" required>
                    </div>
                    <div class="form-group">
                        <label class="d-flex" id="label" for="phone">Мобильный телефон<p class="text-danger">*</p></label>
                        <input type="tel" name="phone" class="form-control w-100" id="phone" placeholder="+7 911 ___ __" required>
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