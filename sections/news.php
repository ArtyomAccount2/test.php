<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-newspaper me-2"></i>Управление новостями
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Добавить новость</span>
        </button>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Список новостей</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Заголовок</th>
                        <th>Дата</th>
                        <th>Автор</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Открытие нового магазина</td>
                        <td>15.01.2024</td>
                        <td>Админ</td>
                        <td><span class="badge bg-success">Опубликовано</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Новые поступления запчастей</td>
                        <td>10.01.2024</td>
                        <td>Админ</td>
                        <td><span class="badge bg-secondary">Черновик</span></td>
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