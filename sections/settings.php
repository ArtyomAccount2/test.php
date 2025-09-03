<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-gear me-2"></i>Настройки системы
    </h2>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action active">Основные настройки</a>
            <a href="#" class="list-group-item list-group-item-action">Настройки магазина</a>
            <a href="#" class="list-group-item list-group-item-action">Уведомления</a>
            <a href="#" class="list-group-item list-group-item-action">Безопасность</a>
            <a href="#" class="list-group-item list-group-item-action">Резервные копии</a>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Основные настройки</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Название сайта</label>
                                <input type="text" class="form-control" value="Лал-Авто" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email администратора</label>
                                <input type="email" class="form-control" value="admin@lal-auto.ru" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание сайта</label>
                        <textarea class="form-control" rows="3">Автозапчасти и автосервис</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Телефон поддержки</label>
                        <input type="tel" class="form-control" value="+7 (999) 123-45-67">
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>