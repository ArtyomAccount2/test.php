<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-person-plus me-2"></i>Добавление пользователя
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=users_list" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            <span class="d-none d-sm-inline">Назад к списку</span>
        </a>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Форма добавления пользователя</h5>
    </div>
    <div class="card-body">
        <form>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">ФИО</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <input type="text" class="form-control" placeholder="Фамилия" required>
                            </div>
                            <div class="col-4">
                                <input type="text" class="form-control" placeholder="Имя" required>
                            </div>
                            <div class="col-4">
                                <input type="text" class="form-control" placeholder="Отчество">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Контактная информация</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="email" class="form-control" placeholder="Email" required>
                            </div>
                            <div class="col-6">
                                <input type="tel" class="form-control" placeholder="Телефон">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Логин и пароль</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="text" class="form-control" placeholder="Логин" required>
                            </div>
                            <div class="col-6">
                                <input type="password" class="form-control" placeholder="Пароль" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Тип пользователя</label>
                        <select class="form-select">
                            <option value="physical">Физическое лицо</option>
                            <option value="legal">Юридическое лицо</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Добавить пользователя
                </button>
            </div>
        </form>
    </div>
</div>