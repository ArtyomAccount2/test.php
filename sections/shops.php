<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-shop me-2"></i>Управление магазинами
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShopModal">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Добавить магазин</span>
        </button>
        <button class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i>
            <span class="d-none d-sm-inline">Экспорт</span>
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" placeholder="Поиск магазинов...">
                            <button class="btn btn-outline-secondary" type="button">Найти</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="regionFilter">
                            <option value="">Все регионы</option>
                            <option value="moscow">Москва</option>
                            <option value="spb">Санкт-Петербург</option>
                            <option value="kazan">Казань</option>
                            <option value="novosibirsk">Новосибирск</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="statusFilter">
                            <option value="">Все статусы</option>
                            <option value="active">Активные</option>
                            <option value="inactive">Неактивные</option>
                            <option value="maintenance">На обслуживании</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Список магазинов</h5>
                <span class="badge bg-primary">15 магазинов</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 397.5px; overflow-y: auto;">
                    <table class="table table-hover mb-0">
                        <thead class="table-light sticky-top" style="position: sticky; top: 0; z-index: 10; background: #f8f9fa;">
                            <tr>
                                <th style="position: sticky; left: 0; background: #f8f9fa; z-index: 11;">ID</th>
                                <th style="min-width: 200px;">Название</th>
                                <th style="min-width: 200px;">Адрес</th>
                                <th style="min-width: 120px;">Регион</th>
                                <th style="min-width: 150px;">Телефон</th>
                                <th style="min-width: 100px;">Статус</th>
                                <th style="min-width: 120px; position: sticky; right: 0; background: #f8f9fa; z-index: 11;">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 1;">1</td>
                                <td>
                                    <strong>Лал-Авто Центр</strong>
                                    <br><small class="text-muted">Основной магазин</small>
                                </td>
                                <td>ул. Ленина, 123, бизнес-центр "Северный", 5 этаж, офис 501</td>
                                <td>Москва</td>
                                <td>+7 (495) 123-45-67, +7 (495) 123-45-68</td>
                                <td><span class="badge bg-success">Активен</span></td>
                                <td style="position: sticky; right: 0; background: white; z-index: 1;">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="Редактировать">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-info" title="Просмотр">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Удалить">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 1;">2</td>
                                <td>
                                    <strong>Лал-Авто Север</strong>
                                    <br><small class="text-muted">Филиал</small>
                                </td>
                                <td>пр. Мира, 45, торговый комплекс "Европа", 2 этаж, секция 205</td>
                                <td>Санкт-Петербург</td>
                                <td>+7 (812) 987-65-43, +7 (812) 987-65-44</td>
                                <td><span class="badge bg-success">Активен</span></td>
                                <td style="position: sticky; right: 0; background: white; z-index: 1;">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 1;">3</td>
                                <td>
                                    <strong>Лал-Авто Восток</strong>
                                    <br><small class="text-muted">Филиал</small>
                                </td>
                                <td>ул. Баумана, 78, исторический центр, здание бывшей фабрики</td>
                                <td>Казань</td>
                                <td>+7 (843) 456-78-90, +7 (843) 456-78-91</td>
                                <td><span class="badge bg-warning">На обслуживании</span></td>
                                <td style="position: sticky; right: 0; background: white; z-index: 1;">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 1;">4</td>
                                <td>
                                    <strong>Лал-Авто Сибирь</strong>
                                    <br><small class="text-muted">Филиал</small>
                                </td>
                                <td>ул. Кирова, 32, деловой центр "Сибирь", 3 этаж, офис 301</td>
                                <td>Новосибирск</td>
                                <td>+7 (383) 234-56-78, +7 (383) 234-56-79</td>
                                <td><span class="badge bg-secondary">Неактивен</span></td>
                                <td style="position: sticky; right: 0; background: white; z-index: 1;">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 1;">5</td>
                                <td>
                                    <strong>Лал-Авто Юг</strong>
                                    <br><small class="text-muted">Филиал</small>
                                </td>
                                <td>пр. Победы, 89, торговый комплекс "Южные ворота", 1 этаж</td>
                                <td>Ростов-на-Дону</td>
                                <td>+7 (863) 345-67-89, +7 (863) 345-67-90</td>
                                <td><span class="badge bg-success">Активен</span></td>
                                <td style="position: sticky; right: 0; background: white; z-index: 1;">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 1;">6</td>
                                <td>
                                    <strong>Лал-Авто Урал</strong>
                                    <br><small class="text-muted">Филиал</small>
                                </td>
                                <td>ул. Ленина, 56, бизнес-центр "Урал", 4 этаж, офис 401</td>
                                <td>Екатеринбург</td>
                                <td>+7 (343) 456-78-90, +7 (343) 456-78-91</td>
                                <td><span class="badge bg-success">Активен</span></td>
                                <td style="position: sticky; right: 0; background: white; z-index: 1;">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 1;">7</td>
                                <td>
                                    <strong>Лал-Авто Дальний Восток</strong>
                                    <br><small class="text-muted">Филиал</small>
                                </td>
                                <td>ул. Советская, 123, торговый центр "Океан", 2 этаж</td>
                                <td>Владивосток</td>
                                <td>+7 (423) 567-89-01, +7 (423) 567-89-02</td>
                                <td><span class="badge bg-warning">На обслуживании</span></td>
                                <td style="position: sticky; right: 0; background: white; z-index: 1;">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="position: sticky; left: 0; background: white; z-index: 1;">8</td>
                                <td>
                                    <strong>Лал-Авто Крым</strong>
                                    <br><small class="text-muted">Филиал</small>
                                </td>
                                <td>ул. Набережная, 45, курортный комплекс "Черноморье"</td>
                                <td>Севастополь</td>
                                <td>+7 (869) 678-90-12, +7 (869) 678-90-13</td>
                                <td><span class="badge bg-success">Активен</span></td>
                                <td style="position: sticky; right: 0; background: white; z-index: 1;">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-info">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Page navigation" class="mt-3 p-3">
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Статистика магазинов</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Всего магазинов:</span>
                    <strong class="text-primary">15</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Активные:</span>
                    <strong class="text-success">12</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>На обслуживании:</span>
                    <strong class="text-warning">2</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Неактивные:</span>
                    <strong class="text-secondary">1</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Общая площадь:</span>
                    <strong>5 400 м²</strong>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Быстрые действия</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-geo-alt me-1"></i>Посмотреть на карте
                    </button>
                    <button class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-graph-up me-1"></i>Статистика продаж
                    </button>
                    <button class="btn btn-outline-info btn-sm">
                        <i class="bi bi-people me-1"></i>Сотрудники магазинов
                    </button>
                    <button class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-clock-history me-1"></i>Графики работы
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addShopModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить новый магазин</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Название магазина<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Введите название" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Тип магазина</label>
                                <select class="form-select">
                                    <option value="main">Основной</option>
                                    <option value="branch">Филиал</option>
                                    <option value="partner">Партнёрский</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Регион<span class="text-danger">*</span></label>
                                <select class="form-select" required>
                                    <option value="">Выберите регион</option>
                                    <option value="moscow">Москва</option>
                                    <option value="spb">Санкт-Петербург</option>
                                    <option value="kazan">Казань</option>
                                    <option value="novosibirsk">Новосибирск</option>
                                    <option value="other">Другой</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Телефон<span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" placeholder="+7 (XXX) XXX-XX-XX" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Адрес<span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="2" placeholder="Полный адрес магазина" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Площадь (м²)</label>
                                <input type="number" class="form-control" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Количество сотрудников</label>
                                <input type="number" class="form-control" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Статус</label>
                                <select class="form-select">
                                    <option value="active">Активен</option>
                                    <option value="inactive">Неактивен</option>
                                    <option value="maintenance">На обслуживании</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" rows="3" placeholder="Дополнительная информация о магазине"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">График работы</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="time" class="form-control" placeholder="Начало работы">
                            </div>
                            <div class="col-6">
                                <input type="time" class="form-control" placeholder="Окончание работы">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary">Добавить магазин</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let regionFilter = document.getElementById('regionFilter');
    let statusFilter = document.getElementById('statusFilter');
    
    if (regionFilter && statusFilter) 
    {
        [regionFilter, statusFilter].forEach(filter => {
            filter.addEventListener('change', function() 
            {
                applyFilters();
            });
        });
    }

    function applyFilters() 
    {
        console.log('Применение фильтров...');
    }

    let addShopModal = document.getElementById('addShopModal');

    if (addShopModal) 
    {
        addShopModal.addEventListener('shown.bs.modal', function() 
        {
            console.log('Модальное окно добавления магазина открыто');
        });
    }

    let tableContainer = document.querySelector('.table-responsive');

    if (tableContainer) 
    {
        tableContainer.addEventListener('scroll', function() 
        {
            let scrollLeft = this.scrollLeft;
            let fixedColumns = this.querySelectorAll('[style*="left: 0"], [style*="right: 0"]');
            
            fixedColumns.forEach(col => {
                if (col.style.left === '0px') 
                {
                    col.style.transform = `translateX(${scrollLeft}px)`;
                }
            });
        });
    }
});
</script>