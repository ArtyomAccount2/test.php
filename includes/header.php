<div class="flex-grow-1">
    <nav class="navbar navbar-expand-xl navbar-light bg-light shadow-sm fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php"><img src="../img/Auto.png" alt="Лал-Авто" height="75"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="../index.php">Главная</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-dark dropdown-toggle" href="#" id="navbarDropdownMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Меню
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenu">
                            <li><a class="dropdown-item" href="shops.php">Магазины</a></li>
                            <li><a class="dropdown-item" href="service.php">Автосервис</a></li>
                            <li><a class="dropdown-item" href="assortment.php">Ассортимент</a></li>
                            <li><a class="dropdown-item" href="oils.php">Масла и тех. жидкости</a></li>
                            <li><a class="dropdown-item" href="accessories.php">Аксессуары</a></li>
                            <li><a class="dropdown-item" href="customers.php">Покупателям</a></li>
                            <li><a class="dropdown-item" href="requisites.php">Реквизиты</a></li>
                            <li><a class="dropdown-item" href="suppliers.php">Поставщикам</a></li>
                            <li><a class="dropdown-item" href="vacancies.php">Вакансии</a></li>
                            <li><a class="dropdown-item" href="contacts.php">Контакты</a></li>
                            <li><a class="dropdown-item" href="reviews.php">Отзывы</a></li>
                            <li><a class="dropdown-item" href="delivery.php">Оплата и доставка</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="brands.php">Торговые марки</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="support.php">Поддержка сайта</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="news.php">Новости компании</a>
                    </li>
                </ul>
                <form id="catalogSearchForm" class="d-flex align-items-center me-3">
                    <div class="input-group">
                        <input class="form-control me-2 search-input" type="search" placeholder="Поиск по каталогу" aria-label="Search" id="catalogSearchInput">
                        <button class="btn btn-outline-primary button-link search-button" type="submit">
                            <i class="bi bi-search"></i>
                            <span class="search-text">Найти</span>
                        </button>
                    </div>
                </form>
                <div class="ms-xl-3 ms-lg-2 ms-md-1">
                    <?php 
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
                    {
                    ?>
                        <div class="d-flex flex-column flex-md-row align-items-center">
                            <p class="mb-0 text-center text-md-end me-md-2" style="font-size: 0.9em; white-space: nowrap;">
                                <strong><?= htmlspecialchars($_SESSION['user']); ?></strong>
                            </p>
                            <button class="profile-button w-md-auto" data-bs-toggle="modal" data-bs-target="#accountModal">
                                <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                            </button>
                        </div>
                    <?php 
                    } 
                    else 
                    {
                    ?>
                        <div class="d-flex flex-wrap flex-md-nowrap">
                            <a href="#" class="btn btn-primary button-link w-md-auto mx-1" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Войти
                            </a>
                            <a href="#" class="btn btn-primary button-link w-md-auto" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="bi bi-r-circle"></i>
                                Зарегистрироваться
                            </a>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="loginModalLabel">Авторизация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label">Логин</label>
                            <input type="text" name="login" class="form-control" id="username" placeholder="Введите логин" required value="<?= htmlspecialchars($form_data['login'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Введите пароль" required value="<?= htmlspecialchars($form_data['password'] ?? '') ?>">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="rememberMe" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                        </div>
                        <?php 
                        if (isset($_SESSION['error_message'])) 
                        {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($_SESSION['error_message']); ?>
                            </div>
                        <?php 
                            unset($_SESSION['error_message']);
                        }
                        ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Войти
                        </button>
                        <a href="#" class="btn btn-link">Забыли пароль?</a>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="max-height: 90vh;">
                <div class="modal-header bg-gradient-primary text-white border-0 rounded-top-3">
                    <div class="w-100 text-center position-relative">
                        <h4 class="modal-title fw-bold mb-1">Личный кабинет</h4>
                        <p class="mb-0 opacity-90">Добро пожаловать, дорогой пользователь!</p>
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-4" style="overflow-y: auto;">
                    <div class="user-card bg-light p-4 rounded-3 mb-4 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-3">
                                <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; border-radius: 50%;">
                                    <i class="bi bi-person-fill" style="font-size: 1.8rem;"></i>
                                </div>
                            </div>
                            <div class="user-info flex-grow-1">
                                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($_SESSION['user']); ?></h5>
                                <p class="text-muted mb-0 small">Премиум статус</p>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="user-stats text-end">
                                <div class="badge bg-success rounded-pill px-3 py-2">
                                    <i class="bi bi-star-fill me-1"></i>
                                    256 баллов
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="account-menu">
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded-3 mb-3 bg-hover-light transition-all">
                            <div class="icon-wrapper bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-cart3 text-primary" style="font-size: 1.3rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-semibold mb-1 text-dark">Мои заказы</h6>
                                <p class="text-muted mb-0 small">Просмотр истории заказов</p>
                            </div>
                            <div class="badge bg-primary rounded-pill px-2 py-1 me-2">3</div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded-3 mb-3 bg-hover-light transition-all">
                            <div class="icon-wrapper bg-info bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-person text-info" style="font-size: 1.3rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-semibold mb-1 text-dark">Профиль</h6>
                                <p class="text-muted mb-0 small">Редактирование личных данных</p>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded-3 mb-3 bg-hover-light transition-all">
                            <div class="icon-wrapper bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-bell text-warning" style="font-size: 1.3rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-semibold mb-1 text-dark">Уведомления</h6>
                                <p class="text-muted mb-0 small">Настройка оповещений</p>
                            </div>
                            <div class="badge bg-danger rounded-pill px-2 py-1 me-2">5</div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded-3 mb-3 bg-hover-light transition-all">
                            <div class="icon-wrapper bg-success bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="bi bi-shield-lock text-success" style="font-size: 1.3rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-semibold mb-1 text-dark">Безопасность</h6>
                                <p class="text-muted mb-0 small">Смена пароля и защита</p>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    </div>
                    <div class="quick-actions mt-4 pt-3 border-top">
                        <h6 class="fw-semibold mb-3 text-dark">Быстрые действия</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="#" class="btn btn-outline-primary w-100 py-2">
                                    <i class="bi bi-heart me-1"></i>
                                    Избранное
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#" class="btn btn-outline-secondary w-100 py-2">
                                    <i class="bi bi-clock-history me-1"></i>
                                    История
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <div class="w-100 d-flex gap-2">
                        <form action="../files/logout.php" method="POST" class="flex-grow-1">
                            <button type="submit" class="btn btn-outline-danger w-100 py-2">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Выйти
                            </button>
                        </form>
                        <button type="button" class="btn btn-secondary py-2" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>
                            Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="registerModalLabel">Регистрация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <a href="individuel.php" type="button" class="btn btn-primary mb-2" id="individualsBtn">
                        <i class="bi bi-person-add"></i>
                        Физические лица
                    </a>
                    <div id="individualsInfo" class="registration-info">
                        <p>- если Вы - физическое лицо, пройдите регистрацию. Регистрация возможна как при наличии карты скидок, так и при её отсутствии.</p>
                    </div>
                    <a href="legalEntity.php" type="button" class="btn btn-primary mb-2" id="legalEntitiesBtn">
                        <i class="bi bi-person-add"></i>
                        Юридические лица и ИП
                    </a>
                    <div id="legalEntitiesInfo" class="registration-info">
                        <p>- если Вы - представитель организации, учреждения, предприятия или фирмы, заполните данную форму регистрации.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>