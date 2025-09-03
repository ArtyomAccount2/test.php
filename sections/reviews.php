<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-chat-square-text me-2"></i>Управление отзывами
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary">
            <i class="bi bi-filter me-1"></i>
            <span class="d-none d-sm-inline">Фильтр</span>
        </button>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Список отзывов</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th>Текст отзыва</th>
                        <th>Оценка</th>
                        <th>Дата</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Иван Иванов</td>
                        <td>Отличный сервис, быстро починили машину!</td>
                        <td>⭐️⭐️⭐️⭐️⭐️</td>
                        <td>12.01.2024</td>
                        <td><span class="badge bg-success">Одобрено</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-success"><i class="bi bi-check"></i></button>
                                <button class="btn btn-outline-danger"><i class="bi bi-x"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Петр Петров</td>
                        <td>Жду moderation...</td>
                        <td>⭐️⭐️⭐️</td>
                        <td>14.01.2024</td>
                        <td><span class="badge bg-warning">На модерации</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-success"><i class="bi bi-check"></i></button>
                                <button class="btn btn-outline-danger"><i class="bi bi-x"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>