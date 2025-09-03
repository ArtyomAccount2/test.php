<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-card-checklist me-2"></i>Каталог товаров
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=products_add" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Добавить товар</span>
        </a>
        <button class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i>
            <span class="d-none d-sm-inline">Экспорт</span>
        </button>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="Поиск товаров...">
                    <button class="btn btn-outline-secondary" type="button">Найти</button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end gap-2">
                    <select class="form-select" style="width: auto;">
                        <option>Все категории</option>
                        <option>Запчасти</option>
                        <option>Масла</option>
                        <option>Аксессуары</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="bi bi-filter me-1"></i>
                        Фильтр
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Цена</th>
                        <th>Остаток</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1001</td>
                        <td>Моторное масло 5W-40</td>
                        <td>Масла</td>
                        <td>2 500 ₽</td>
                        <td>45 шт.</td>
                        <td><span class="badge bg-success">В наличии</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>1002</td>
                        <td>Воздушный фильтр</td>
                        <td>Запчасти</td>
                        <td>800 ₽</td>
                        <td>23 шт.</td>
                        <td><span class="badge bg-warning">Мало</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>