<?php
require "connectdb.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Здесь можно добавить запрос к базе данных для получения данных о компании, связанных с пользователем
$user_id = $_SESSION['user_id'];
// $stmt = $conn->prepare("SELECT company_name, inn, kpp, address, phone, email, registration_date FROM companies WHERE user_id = ?");
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $stmt->bind_result($company_name, $inn, $kpp, $address, $phone, $email, $registration_date);
// $stmt->fetch();
// $stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>УчетОнлайн - Личный кабинет</title>
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
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="main_page.php">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Выход</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-3">
                <!-- Боковая панель -->
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active">Профиль</a>
                    <a href="#" class="list-group-item list-group-item-action">Финансы</a>
                    <a href="#" class="list-group-item list-group-item-action">Доходы</a>
                    <a href="#" class="list-group-item list-group-item-action">Расходы</a>
                </div>
            </div>
            <div class="col-md-9">
                <!-- Основной раздел -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Добро пожаловать, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h3>
                        <p>Здесь вы можете управлять своими финансами, просматривать отчёты, а также редактировать профиль.</p>
                    </div>
                </div>

                <!-- Раздел контента -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="card-title">Основные данные компании</h4>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th>Название компании</th>
                                    <td><?= htmlspecialchars($company_name ?? 'Нет данных') ?></td>
                                </tr>
                                <tr>
                                    <th>ИНН</th>
                                    <td><?= htmlspecialchars($inn ?? 'Нет данных') ?></td>
                                </tr>
                                <tr>
                                    <th>КПП</th>
                                    <td><?= htmlspecialchars($kpp ?? 'Нет данных') ?></td>
                                </tr>
                                <tr>
                                    <th>Юридический адрес</th>
                                    <td><?= htmlspecialchars($address ?? 'Нет данных') ?></td>
                                </tr>
                                <tr>
                                    <th>Телефон</th>
                                    <td><?= htmlspecialchars($phone ?? 'Нет данных') ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= htmlspecialchars($email ?? 'Нет данных') ?></td>
                                </tr>
                                <tr>
                                    <th>Дата регистрации</th>
                                    <td><?= htmlspecialchars($registration_date ?? 'Нет данных') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
