<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>УчетОнлайн - Главная страница</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="main_page.php">УчетОнлайн</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Возможности</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Контакты</a>    
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="account.php">Аккаунт</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Выход</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Главный блок -->
    <header class="text-center">
        <div class="container">
            <h1 class="display-4">Добро пожаловать в УчетОнлайн!</h1>
            <p class="lead">Удобный инструмент для учета доходов и расходов вашего бизнеса.</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn btn-light btn-lg me-2">Войти</a>
                <a href="register.php" class="btn btn-secondary btn-lg">Зарегистрироваться</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Раздел возможностей -->
    <section id="features">
        <div class="container">
            <h2 class="text-center my-4">Возможности системы</h2>
            <div class="row justify-content-center">
                <div class="col-md-3 feature-box">
                    <h3>Учет доходов</h3>
                    <p>Отслеживайте все источники доходов в одном месте.</p>
                </div>
                <div class="col-md-3 feature-box">
                    <h3>Учет расходов</h3>
                    <p>Контролируйте траты и анализируйте финансовую статистику.</p>
                </div>
                <div class="col-md-3 feature-box">
                    <h3>Расчет чистой прибыли</h3>
                    <p>Автоматический расчет для понимания финансового результата.</p>
                </div>
            </div>
        </div>
    </section>
    

    <!-- Раздел контактов -->
    <section id="contact" class="bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Контакты</h2>
            <p class="text-center">Если у вас есть вопросы, свяжитесь с нами:</p>
            <div class="text-center">
                <p>Email: support@uchetonline.com</p>
                <p>Телефон: +7 (999) 111-11-11</p>
            </div>
        </div>
    </section>

    <!-- Подвал -->
    <footer class="text-center">
        <p>&copy; 2025 УчетОнлайн.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
