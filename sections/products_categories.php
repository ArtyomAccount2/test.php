<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-tags me-2"></i>Управление категориями
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Добавить категорию</span>
        </button>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Список категорий</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Запчасти
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Масла
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Аксессуары
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Добавить категорию</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Название категории</label>
                        <input type="text" class="form-control" placeholder="Введите название" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" rows="3" placeholder="Описание категории"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>