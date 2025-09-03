<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-people me-2"></i>Управление пользователями
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=users_add" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Добавить пользователя</span>
        </a>
        <button class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i>
            <span class="d-none d-sm-inline">Экспорт</span>
        </button>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="Поиск пользователей...">
                    <button class="btn btn-outline-secondary" type="button">Найти</button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end gap-2">
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="bi bi-filter me-1"></i>
                        <span class="d-none d-sm-inline">Фильтр</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary">
                        <i class="bi bi-sliders me-1"></i>
                        <span class="d-none d-sm-inline">Колонки</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th class="d-none d-sm-table-cell">ФИО</th>
                        <th>Логин</th>
                        <th class="d-none d-md-table-cell">Email</th>
                        <th class="d-none d-lg-table-cell">Телефон</th>
                        <th class="d-none d-xl-table-cell">Регион</th>
                        <th>Тип</th>
                        <th width="100">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($users as $user)
                    { 
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id_users'] ?? '') ?></td>
                        <td class="d-none d-sm-table-cell">
                            <?php
                            $surname = htmlspecialchars($user['surname_users'] ?? '');
                            $name = htmlspecialchars($user['name_users'] ?? '');
                            $patronymic = htmlspecialchars($user['patronymic_users'] ?? '');
                            
                            if (empty($surname) || empty($name) || empty($patronymic)) 
                            {
                                echo 'Не указано';
                            } 
                            else 
                            {
                                echo trim(implode(' ', [$surname, $name, $patronymic]));
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($user['login_users'] ?? '') ?></td>
                        <td class="d-none d-md-table-cell"><?= htmlspecialchars($user['email_users'] ?? '') ?></td>
                        <td class="d-none d-lg-table-cell"><?= htmlspecialchars($user['phone_users'] ?? '') ?></td>
                        <td class="d-none d-xl-table-cell"><?= htmlspecialchars($user['region_users'] ?? '') ?></td>
                        <td>
                            <span class="badge bg-<?= empty($user['organization_users']) ? 'info' : 'warning' ?>">
                                <?= empty($user['organization_users']) ? 'Физ.' : 'Юр.' ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="files/edit_user.php?id=<?= $user['id_users'] ?>" class="btn btn-outline-primary" title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="files/delete_user.php?id=<?= $user['id_users'] ?>" class="btn btn-outline-danger" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <nav aria-label="Page navigation" class="mt-3">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
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