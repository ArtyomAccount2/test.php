<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-plus-circle me-2"></i>Добавление товара
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=products_catalog" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            <span class="d-none d-sm-inline">Назад к каталогу</span>
        </a>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Форма добавления товара</h5>
    </div>
    <div class="card-body">
        <form>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Основная информация</label>
                        <input type="text" class="form-control mb-2" placeholder="Название товара" required>
                        <textarea class="form-control mb-2" placeholder="Описание" rows="3"></textarea>
                        <select class="form-select" required>
                            <option value="">Выберите категорию</option>
                            <option value="parts">Запчасти</option>
                            <option value="oils">Масла</option>
                            <option value="accessories">Аксессуары</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Цена и количество</label>
                        <div class="input-group mb-2">
                            <input type="number" class="form-control" placeholder="Цена" required>
                            <span class="input-group-text">₽</span>
                        </div>
                        <input type="number" class="form-control mb-2" placeholder="Количество на складе">
                        <input type="number" class="form-control" placeholder="Артикул">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Изображение товара</label>
                <input type="file" class="form-control" accept="image/*">
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Добавить товар
                </button>
            </div>
        </form>
    </div>
</div>