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
                            <li><a class="dropdown-item" href="oils.php?sort=default&page=1">Масла и тех. жидкости</a></li>
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
                <div class="ms-xl-3 ms-lg-2 ms-md-1">
                    <?php 
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
                    {
                    ?>
                        <div class="d-flex flex-column flex-md-row align-items-center">
                            <p class="mb-0 text-center text-md-end me-md-2" style="font-size: 0.9em; white-space: nowrap;">
                                <strong><?= htmlspecialchars($_SESSION['user']); ?></strong>
                            </p>
                            <a href="../profile.php" class="profile-button w-md-auto text-decoration-none">
                                <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                            </a>
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
                        <div class="d-flex gap-2 align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Войти
                            </button>
                            <a href="../includes/forgot_password.php" class="btn btn-outline-secondary">
                                <i class="bi bi-question-circle"></i> Забыли пароль?
                            </a>
                        </div>
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

    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="registerModalLabel">Регистрация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <a href="../individuel.php" type="button" class="btn btn-primary mb-2" id="individualsBtn">
                        <i class="bi bi-person-add"></i> Физические лица
                    </a>
                    <div id="individualsInfo" class="registration-info">
                        <p>- если Вы - физическое лицо, пройдите регистрацию. Регистрация возможна как при наличии карты скидок, так и при её отсутствии.</p>
                    </div>
                    <a href="../legalEntity.php" type="button" class="btn btn-primary mb-2" id="legalEntitiesBtn">
                        <i class="bi bi-person-add"></i> Юридические лица и ИП
                    </a>
                    <div id="legalEntitiesInfo" class="registration-info">
                        <p>- если Вы - представитель организации, учреждения, предприятия или фирмы, заполните данную форму регистрации.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="socialFloat" class="social-float-container">
        <button id="socialToggle" class="social-toggle-btn" title="Социальные сети">
            <i class="bi bi-chevron-up"></i>
        </button>
        <div class="social-icons-container">
            <a href="https://vk.com/lalauto" class="social-icon-float" target="_blank" title="ВКонтакте">
                <img src="../img/image 33.png" alt="VK" width="32" height="32">
            </a>
            <a href="https://t.me/s/lalauto" class="social-icon-float" target="_blank" title="Telegram">
                <img src="../img/image 32.png" alt="Telegram" width="32" height="32">
            </a>
        </div>
    </div>