<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-gear me-2"></i>Настройки системы
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-primary">
            <i class="bi bi-save me-1"></i>
            <span class="d-none d-sm-inline">Сохранить все</span>
        </button>
        <button class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise me-1"></i>
            <span class="d-none d-sm-inline">Сбросить</span>
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="list-group" id="settingsTabs" role="tablist">
            <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#general" role="tab">
                <i class="bi bi-house me-2"></i>Основные настройки
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#store" role="tab">
                <i class="bi bi-shop me-2"></i>Настройки магазина
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#notifications" role="tab">
                <i class="bi bi-bell me-2"></i>Уведомления
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#security" role="tab">
                <i class="bi bi-shield me-2"></i>Безопасность
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#backup" role="tab">
                <i class="bi bi-database me-2"></i>Резервные копии
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#payment" role="tab">
                <i class="bi bi-credit-card me-2"></i>Платежи
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#shipping" role="tab">
                <i class="bi bi-truck me-2"></i>Доставка
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#seo" role="tab">
                <i class="bi bi-search me-2"></i>SEO
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#api" role="tab">
                <i class="bi bi-code-slash me-2"></i>API
            </a>
            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#maintenance" role="tab">
                <i class="bi bi-tools me-2"></i>Тех. обслуживание
            </a>
        </div>
    </div>

    <div class="col-md-9">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-house me-2"></i>Основные настройки</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Название сайта<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="Лал-Авто" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email администратора<span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" value="admin@lal-auto.ru" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Описание сайта</label>
                                <textarea class="form-control" rows="3">Автозапчасти и автосервис - качественное обслуживание вашего автомобиля</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Телефон поддержки<span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" value="+7 (999) 123-45-67" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Время работы</label>
                                        <input type="text" class="form-control" value="Пн-Пт: 9:00-18:00, Сб: 10:00-16:00">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Язык по умолчанию</label>
                                        <select class="form-select">
                                            <option selected>Русский</option>
                                            <option>English</option>
                                            <option>Deutsch</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Валюта</label>
                                        <select class="form-select">
                                            <option selected>RUB - Российский рубль</option>
                                            <option>USD - Доллар США</option>
                                            <option>EUR - Евро</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="store" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Настройки магазина</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Минимальная сумма заказа</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" value="1000">
                                            <span class="input-group-text">₽</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">НДС</label>
                                        <select class="form-select">
                                            <option selected>20% (стандартный)</option>
                                            <option>10% (льготный)</option>
                                            <option>0% (без НДС)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Валютный курс (к рублю)</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">USD</span>
                                            <input type="number" class="form-control" value="90.5" step="0.01">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text">EUR</span>
                                            <input type="number" class="form-control" value="99.8" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Настройки инвентаря</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" checked id="lowStockAlert">
                                            <label class="form-check-label" for="lowStockAlert">Уведомлять о низком запасе</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="allowBackorder">
                                            <label class="form-check-label" for="allowBackorder">Разрешить предзаказ</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Политика возвратов</label>
                                <textarea class="form-control" rows="4">Возврат товара возможен в течение 14 дней с момента покупки при сохранении товарного вида и упаковки.</textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="notifications" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Настройки уведомлений</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <h6 class="mb-3">Email уведомления</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" checked id="emailOrders">
                                        <label class="form-check-label" for="emailOrders">Новые заказы</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" checked id="emailPayments">
                                        <label class="form-check-label" for="emailPayments">Оплаты</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="emailReviews">
                                        <label class="form-check-label" for="emailReviews">Новые отзывы</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" checked id="emailStock">
                                        <label class="form-check-label" for="emailStock">Низкий запас</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="emailNewsletter">
                                        <label class="form-check-label" for="emailNewsletter">Новостная рассылка</label>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">SMS уведомления</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="smsOrders">
                                        <label class="form-check-label" for="smsOrders">Статус заказа</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="smsDelivery">
                                        <label class="form-check-label" for="smsDelivery">Доставка</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" checked id="smsPromo">
                                        <label class="form-check-label" for="smsPromo">Акции и скидки</label>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Настройки SMTP</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">SMTP Сервер</label>
                                        <input type="text" class="form-control" value="smtp.gmail.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Порт</label>
                                        <input type="number" class="form-control" value="587">
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="security" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-shield me-2"></i>Настройки безопасности</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>Последнее обновление настроек безопасности: 15.01.2024
                            </div>
                            <h6 class="mb-3">Парольная политика</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Минимальная длина пароля</label>
                                        <input type="number" class="form-control" value="8" min="6">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Срок действия пароля (дней)</label>
                                        <input type="number" class="form-control" value="90" min="30">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" checked id="requireSpecialChar">
                                        <label class="form-check-label" for="requireSpecialChar">Требовать спец. символы</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" checked id="requireNumbers">
                                        <label class="form-check-label" for="requireNumbers">Требовать цифры</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="requireUpperLower">
                                        <label class="form-check-label" for="requireUpperLower">Верхний/нижний регистр</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" checked id="preventReuse">
                                        <label class="form-check-label" for="preventReuse">Запретить повторное использование</label>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Двухфакторная аутентификация</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="enable2FA">
                                        <label class="form-check-label" for="enable2FA">Включить 2FA для администраторов</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="enable2FAUsers">
                                        <label class="form-check-label" for="enable2FAUsers">2FA для пользователей</label>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Защита от brute-force</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Максимум попыток входа</label>
                                        <input type="number" class="form-control" value="5" min="3">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Блокировка на (минут)</label>
                                        <input type="number" class="form-control" value="30" min="5">
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="backup" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-database me-2"></i>Управление резервными копиями</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>Последнее резервное копирование: 14.01.2024 23:45
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="bi bi-database display-4 text-primary mb-3"></i>
                                        <h5>Размер базы данных</h5>
                                        <h3 class="text-primary">45.7 MB</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="bi bi-hdd display-4 text-success mb-3"></i>
                                        <h5>Свободное место</h5>
                                        <h3 class="text-success">15.2 GB</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3">Расписание резервного копирования</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Частота</label>
                                    <select class="form-select">
                                        <option>Ежедневно</option>
                                        <option selected>Еженедельно</option>
                                        <option>Ежемесячно</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Время выполнения</label>
                                    <input type="time" class="form-control" value="02:00">
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3">История резервных копий</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Тип</th>
                                        <th>Размер</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>14.01.2024 23:45</td>
                                        <td>Полная</td>
                                        <td>45.7 MB</td>
                                        <td><span class="badge bg-success">Успешно</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Скачать</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>07.01.2024 23:45</td>
                                        <td>Полная</td>
                                        <td>44.2 MB</td>
                                        <td><span class="badge bg-success">Успешно</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Скачать</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>31.12.2023 23:45</td>
                                        <td>Полная</td>
                                        <td>43.8 MB</td>
                                        <td><span class="badge bg-success">Успешно</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Скачать</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Создать резервную копию
                            </button>
                            <button class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Восстановить
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="payment" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Настройки платежей</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">Платежные системы</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Банковские карты</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </div>
                                        <small class="text-muted">Visa, Mastercard, Mir</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">ЮMoney</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </div>
                                        <small class="text-muted">Быстрые онлайн-платежи</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Сбербанк Онлайн</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                        </div>
                                        <small class="text-muted">Оплата через Сбербанк</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Наличные при получении</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </div>
                                        <small class="text-muted">Оплата курьеру</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3">Настройки комиссий</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Комиссия за обработку (%)</label>
                                    <input type="number" class="form-control" value="2.5" step="0.1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Минимальная комиссия (₽)</label>
                                    <input type="number" class="form-control" value="10">
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="shipping" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Настройки доставки</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">Способы доставки</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Курьерская доставка</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Стоимость:</label>
                                            <input type="number" class="form-control" value="300">
                                        </div>
                                        <small class="text-muted">Доставка по городу</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Самовывоз</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Стоимость:</label>
                                            <input type="number" class="form-control" value="0" disabled>
                                        </div>
                                        <small class="text-muted">Из пунктов выдачи</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Почта России</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Стоимость:</label>
                                            <input type="number" class="form-control" value="500">
                                        </div>
                                        <small class="text-muted">По всей России</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">СДЭК</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" checked>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Стоимость:</label>
                                            <input type="number" class="form-control" value="450">
                                        </div>
                                        <small class="text-muted">Курьерская служба</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3">Бесплатная доставка</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Минимальная сумма для бесплатной доставки</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" value="5000">
                                        <span class="input-group-text">₽</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Срок доставки (дней)</label>
                                    <input type="number" class="form-control" value="3">
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="seo" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-search me-2"></i>SEO настройки</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>Эти настройки влияют на поисковую оптимизацию вашего сайта.
                        </div>
                        <h6 class="mb-3">Мета-теги</h6>
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" class="form-control" value="Лал-Авто - Автозапчасти и автосервис">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea class="form-control" rows="3">Качественные автозапчасти и профессиональный автосервис. Широкий ассортимент, доступные цены, гарантия качества.</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keywords</label>
                            <input type="text" class="form-control" value="автозапчасти, автосервис, автомобильные запчасти, ремонт авто">
                        </div>
                        <h6 class="mb-3">Open Graph</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">OG Title</label>
                                    <input type="text" class="form-control" value="Лал-Авто">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">OG Image URL</label>
                                    <input type="url" class="form-control" placeholder="https://example.com/image.jpg">
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3">Настройки URL</h6>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" checked id="seoFriendlyUrls">
                            <label class="form-check-label" for="seoFriendlyUrls">ЧПУ (Человекопонятные URL)</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" checked id="generateSitemap">
                            <label class="form-check-label" for="generateSitemap">Автоматически генерировать sitemap.xml</label>
                        </div>
                        <h6 class="mb-3">Robots.txt</h6>
                        <div class="mb-3">
                            <textarea class="form-control" rows="4" placeholder="User-agent: *
Disallow: /admin/
Disallow: /cart/
Sitemap: https://lal-auto.ru/sitemap.xml">User-agent: *
Disallow: /admin/
Disallow: /cart/
Allow: /public/
Sitemap: https://lal-auto.ru/sitemap.xml</textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="api" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-code-slash me-2"></i>API настройки</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Будьте осторожны при работе с API ключами. Не передавайте их третьим лицам.
                        </div>
                        <h6 class="mb-3">API Ключи</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Название</th>
                                        <th>Ключ</th>
                                        <th>Создан</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Мобильное приложение</td>
                                        <td><code>sk_live_1234567890abcdef</code></td>
                                        <td>10.01.2024</td>
                                        <td><span class="badge bg-success">Активен</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger">Отозвать</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Веб-сайт партнера</td>
                                        <td><code>sk_test_9876543210fedcba</code></td>
                                        <td>05.01.2024</td>
                                        <td><span class="badge bg-warning">Тестовый</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger">Отозвать</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary mb-4">
                            <i class="bi bi-plus-circle me-1"></i>Создать новый API ключ
                        </button>
                        <h6 class="mb-3">Настройки доступа</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" checked id="apiEnabled">
                                    <label class="form-check-label" for="apiEnabled">Включить REST API</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="graphqlEnabled">
                                    <label class="form-check-label" for="graphqlEnabled">Включить GraphQL API</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Лимит запросов в минуту</label>
                                    <input type="number" class="form-control" value="100">
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3">Webhooks</h6>
                        <div class="mb-3">
                            <label class="form-label">URL для webhook уведомлений</label>
                            <input type="url" class="form-control" placeholder="https://example.com/webhook">
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="maintenance" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Техническое обслуживание</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-octagon me-2"></i>Внимание! Эти настройки влияют на доступность сайта для пользователей.
                        </div>
                        <h6 class="mb-3">Режим обслуживания</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="maintenanceMode">
                                    <label class="form-check-label" for="maintenanceMode">Включить режим обслуживания</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Сообщение для пользователей</label>
                                    <textarea class="form-control" rows="3">Сайт временно недоступен. Ведутся технические работы. Приносим извинения за неудобства.</textarea>
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3">Очистка кэша</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Размер кэша</h6>
                                        <h4 class="text-primary">12.3 MB</h4>
                                        <button class="btn btn-sm btn-outline-danger w-100">
                                            <i class="bi bi-trash me-1"></i>Очистить кэш
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Логи системы</h6>
                                        <h4 class="text-warning">8.7 MB</h4>
                                        <button class="btn btn-sm btn-outline-warning w-100">
                                            <i class="bi bi-trash me-1"></i>Очистить логи
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3">Системная информация</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Версия PHP</label>
                                    <input type="text" class="form-control" value="8.2.12" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Версия MySQL</label>
                                    <input type="text" class="form-control" value="8.0.33" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Версия системы</label>
                                    <input type="text" class="form-control" value="2.1.0" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Последнее обновление</label>
                                    <input type="text" class="form-control" value="10.01.2024" disabled>
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3">Проверка обновлений</h6>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>У вас установлена последняя версия системы.
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise me-1"></i>Проверить обновления
                            </button>
                            <button class="btn btn-outline-danger">
                                <i class="bi bi-download me-1"></i>Обновить систему
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let settingsTabs = document.getElementById('settingsTabs');

    if (settingsTabs) 
    {
        let activeTab = localStorage.getItem('activeSettingsTab');

        if (activeTab) 
        {
            let tab = document.querySelector(`[href="${activeTab}"]`);

            if (tab) 
            {
                new bootstrap.Tab(tab).show();
            }
        }

        settingsTabs.addEventListener('click', function(e) 
        {
            if (e.target.hasAttribute('data-bs-toggle')) 
            {
                let target = e.target.getAttribute('href');
                localStorage.setItem('activeSettingsTab', target);
            }
        });
    }

    let maintenanceMode = document.getElementById('maintenanceMode');

    if (maintenanceMode) 
    {
        maintenanceMode.addEventListener('change', function() 
        {
            if (this.checked) 
            {
                if (!confirm('Включение режима обслуживания сделает сайт недоступным для пользователей. Продолжить?')) 
                {
                    this.checked = false;
                }
            }
        });
    }
});
</script>