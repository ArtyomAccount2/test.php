<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лал-Авто - Автозапчасти</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
        <a class="navbar-brand" href="#"><img src="img/Auto.png" alt="Лал-Авто" height="50"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <button class="btn btn-outline-primary ml-2" data-toggle="modal" data-target="#menuModal">Меню</button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="#">Торговые марки</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Поддержка сайта</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Новости компании</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Оплата и доставка</a></li>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-2" type="search" placeholder="Поиск по каталогу" aria-label="Search">
                <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Найти</button>
            </form>
            <a href="#" class="btn btn-primary ml-3">Войти</a>
            <a href="#" class="btn btn-secondary ml-2">Зарегистрироваться</a>
        </div>
    </nav>

    <div class="modal fade" id="menuModal" tabindex="-1" role="dialog" aria-labelledby="menuModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="menuModalLabel">Меню</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <ul class="list-unstyled">
                        <li><a href="#">Магазины</a></li>
                        <li><a href="#">Автосервис</a></li>
                        <li><a href="#">Ассортимент</a></li>
                        <li><a href="#">Масла и тех. жидкости</a></li>
                        <li><a href="#">Аксессуары</a></li>
                        <li><a href="#">Покупателям</a></li>
                        <li><a href="#">Поставщикам</a></li>
                        <li><a href="#">Вакансии</a></li>
                        <li><a href="#">Контакты</a></li>
                        <li><a href="#">Отзывы</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div id="carouselExample" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/no-image.png" class="d-block w-100" alt="Слайд 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Лучшие автозапчасти</h5>
                    <p>Найдите запчасти для вашего автомобиля.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/no-image.png" class="d-block w-100" alt="Слайд 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Качество и надежность</h5>
                    <p>Мы предлагаем только проверенные запчасти.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/no-image.png" class="d-block w-100" alt="Слайд 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Быстрая доставка</h5>
                    <p>Получите свои запчасти в кратчайшие сроки.</p>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Предыдущий</span>
        </a>
        <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Следующий</span>
        </a>
    </div>

    <section class="container my-5">
        <h2 class="text-center">Поиск по марке</h2>
        <div class="row align-items-center">
            <button class="btn btn-secondary col-auto" onclick="scrollLeft('carBrands')">◀</button>
            <div id="carBrandsList" class="col overflow-hidden">
                <div class="row flex-nowrap" id="carBrandsBlock">
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Acura.png" class="card-img-top" alt="Марка 1">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Acura</h5>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Aixam.png" class="card-img-top" alt="Марка 1">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Acura</h5>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Alfa Romeo.png" class="card-img-top" alt="Марка 1">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Acura</h5>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Aston Martin.png" class="card-img-top" alt="Марка 1">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Acura</h5>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Audi.png" class="card-img-top" alt="Марка 1">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Acura</h5>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Bentley.png" class="card-img-top" alt="Марка 1">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Acura</h5>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/BMW.png" class="card-img-top" alt="Марка 1">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Acura</h5>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Buick.png" class="card-img-top" alt="Марка 1">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Acura</h5>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-secondary col-auto" onclick="scrollRight('carBrands')">▶</button>
        </div>
    </section>

    <section class="container my-5">
        <h2 class="text-center">Популярные запчасти</h2>
        <div class="row align-items-center">
            <button class="btn btn-secondary col-auto" onclick="scrollLeft('popularParts')">◀</button>
            <div id="popularParts" class="col overflow-hidden">
                <div class="row flex-nowrap" id="partsContainer">
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 part">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image1.png" class="card-img-top" alt="Коленчатый вал">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Коленчатый вал</h5>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 part">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image2.png" class="card-img-top" alt="Коленчатый вал">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Коленчатый вал</h5>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 part">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image3.png" class="card-img-top" alt="Коленчатый вал">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Коленчатый вал</h5>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 part">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image4.png" class="card-img-top" alt="Коленчатый вал">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Коленчатый вал</h5>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 part">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image5.png" class="card-img-top" alt="Коленчатый вал">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Коленчатый вал</h5>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 part">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image6.png" class="card-img-top" alt="Коленчатый вал">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Коленчатый вал</h5>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 part">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image7.png" class="card-img-top" alt="Коленчатый вал">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Коленчатый вал</h5>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 part">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image8.png" class="card-img-top" alt="Коленчатый вал">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Коленчатый вал</h5>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-secondary col-auto" onclick="scrollRight('popularParts')">▶</button>
        </div>
    </section>

    <footer class="bg-light text-center py-4">
        <div class="container">
            <p>© 2023 Лал-Авто. Все права защищены.</p>
            <p>Контактный телефон: +7 (4012) 65-65-65</p>
            <p><a href="#">Политика конфиденциальности</a> | <a href="#">Условия использования</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        let partsContainer = document.getElementById('partsContainer');
        let carBrandsBlock = document.getElementById('carBrandsBlock');
        let parts = partsContainer.innerHTML;
        let brands = carBrandsBlock.innerHTML;
        partsContainer.innerHTML += parts;
        carBrandsBlock.innerHTML += brands;

        function scrollLeft(id) 
        {
            let container = document.getElementById(id);
            container.scrollLeft -= 265;
        }

        function scrollRight(id) 
        {
            let container = document.getElementById(id);
            container.scrollLeft += 265;
        }

        function resetScroll(container) 
        {
            if (container.scrollLeft >= container.scrollWidth / 2) 
            {
                container.scrollLeft = 0;
            }
        }

        document.getElementById('popularParts').addEventListener('scroll', function() 
        {
            resetScroll(this);
        });

        document.getElementById('carBrandsList').addEventListener('scroll', function() 
        {
            resetScroll(this);
        });
    </script>
</body>
</html>