<?php
require "connectdb.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Проверка наличия данных компании
$user_id = $_SESSION['user_id'];
$stmt = $mysql->prepare("SELECT id FROM companies WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_companies = $result->fetch_all(MYSQLI_ASSOC);
$dataExists = $result->num_rows > 0;
$stmt->close();

if (!is_array($user_companies)) {
    $user_companies = [];
}



// Получение данных компании для текущего пользователя
$stmt = $mysql->prepare("
    SELECT company_name, inn, kpp, legal_address, phone, email, registration_date, ogrn, website, description 
    FROM companies 
    WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($company_name, $inn, $kpp, $legal_address, $phone, $email, $registration_date, $ogrn, $website, $description);
$stmt->fetch();
$stmt->close();

// Получение текущей выбранной компании
$current_company_id = $_SESSION['current_company_id'] ?? null;

// Установить текущую компанию, если не выбрана
if (!$current_company_id && count($user_companies) > 0) {
    $current_company_id = $user_companies[0]['id'];
    $_SESSION['current_company_id'] = $current_company_id;
}

// Получение данных выбранной компании
if ($current_company_id) {
    $stmt = $mysql->prepare("
        SELECT company_name, inn, kpp, legal_address, phone, email, registration_date, ogrn, website, description 
        FROM companies 
        WHERE user_id = ? AND id = ?
    ");
    $stmt->bind_param("ii", $user_id, $current_company_id);
    $stmt->execute();
    $stmt->bind_result($company_name, $inn, $kpp, $legal_address, $phone, $email, $registration_date, $ogrn, $website, $description);
    $stmt->fetch();
    $stmt->close();
} else {
    $company_name = null;
    $inn = $kpp = $legal_address = $phone = $email = $ogrn = $website = $description = null;
    $registration_date = 'Нет данных';
}

$stmt = $mysql->prepare("SELECT id, company_name FROM companies WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_companies = $result->fetch_all(MYSQLI_ASSOC);


// Форматирование даты в формате РФ
if (!empty($registration_date) && strtotime($registration_date)) {
    $registration_date = (new DateTime($registration_date))->format('d.m.Y');
} else {
    $registration_date = 'Нет данных';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>УчетОнлайн - Личный кабинет</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="main_page.php">УчетОнлайн</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                    <a href="finances.php" class="list-group-item list-group-item-action">Финансы</a>
                </div>
            </div>
            <div class="col-md-9">
                <!-- Основной раздел -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Добро пожаловать, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h3>
                        <p>Здесь вы можете управлять своими финансами, просматривать отчёты, а также редактировать
                            профиль.</p>
                    </div>
                </div>

                <!-- Раздел контента -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="card-title d-flex align-items-center"> Данные компании
                            <div class="dropdown">
                                <?php
                                $current_company = array_filter($user_companies, function ($c) use ($current_company_id) {
                                    return $c['id'] == $current_company_id;
                                });
                                $current_company_name = $current_company ? reset($current_company)['company_name'] : "«Не выбрана»";
                                ?>
                                <button class="btn btn-outline-primary dropdown-toggle ms-2" type="button"
                                    id="companyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= htmlspecialchars($current_company_name) ?>
                                </button>

                                <ul class="dropdown-menu" aria-labelledby="companyDropdown">
                                    <?php foreach ($user_companies as $company): ?>
                                        <li>
                                            <a class="dropdown-item"
                                                href="change_company.php?company_id=<?= $company['id'] ?>">
                                                <?= htmlspecialchars($company['company_name']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <button type="button" class="btn btn-outline-success btn-sm ms-2" data-bs-toggle="modal"
                                data-bs-target="#addCompanyModal">
                                <i class="bi bi-plus"></i>
                            </button>
                        </h4>


                        <!-- Кнопка для добавления или изменения данных -->
                        <?php if ($dataExists): ?>
                            <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal"
                                data-bs-target="#editCompanyModal">
                                Изменить данные
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-success button-spacing" data-bs-toggle="modal"
                                data-bs-target="#addCompanyModal">
                                Добавить данные
                            </button>
                        <?php endif; ?>
                        <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#selectCompanyModal">
                            Выбрать компанию
                        </button> -->


                        <!-- Таблица данных компании -->
                        <?php if (isset($company_name)): ?>
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Название компании</th>
                                        <td><?= htmlspecialchars($company_name) ?></td>
                                    </tr>
                                    <tr>
                                        <th>ИНН</th>
                                        <td><?= htmlspecialchars($inn) ?></td>
                                    </tr>
                                    <tr>
                                        <th>КПП</th>
                                        <td><?= htmlspecialchars($kpp) ?></td>
                                    </tr>
                                    <tr>
                                        <th>ОГРН</th>
                                        <td><?= htmlspecialchars($ogrn ?? 'Нет данных') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Веб-сайт</th>
                                        <td><?= htmlspecialchars($website ?? 'Нет данных') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Описание</th>
                                        <td><?= htmlspecialchars($description ?? 'Нет данных') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Юридический адрес</th>
                                        <td><?= htmlspecialchars($legal_address) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Телефон</th>
                                        <td><?= htmlspecialchars($phone) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?= htmlspecialchars($email) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Дата регистрации</th>
                                        <td><?= htmlspecialchars($registration_date) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>Данные компании не найдены. Нажмите "Добавить данные", чтобы внести информацию.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно -->
    <div class="modal fade" id="addCompanyModal" tabindex="-1" aria-labelledby="addCompanyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_company.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCompanyModalLabel">Добавить данные компании</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger fw-bold">* Все поля обязательны для заполнения</p>
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Название компании</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="inn" class="form-label">ИНН</label>
                            <input type="text" class="form-control" name="inn" id="inn" placeholder="Введите 10 цифр"
                                pattern="\d{10}" title="ИНН должен состоять из 10 цифр" required>
                        </div>

                        <div class="mb-3">
                            <label for="kpp" class="form-label">КПП</label>
                            <input type="text" class="form-control" name="kpp" id="kpp" placeholder="Введите 9 цифр"
                                pattern="\d{9}" title="КПП должен состоять из 9 цифр" required>
                        </div>

                        <div class="mb-3">
                            <label for="ogrn" class="form-label">ОГРН</label>
                            <input type="text" class="form-control" name="ogrn" id="ogrn" placeholder="Введите 13 цифр"
                                pattern="\d{13}" title="ОГРН должен состоять из 13 цифр" required>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                // Проверка ИНН
                                document.getElementById('inn').addEventListener('input', function () {
                                    const value = this.value;
                                    if (!/^\d{0,10}$/.test(value)) {
                                        this.setCustomValidity('ИНН должен содержать только 10 цифр.');
                                    } else {
                                        this.setCustomValidity('');
                                    }
                                });

                                // Проверка КПП
                                document.getElementById('kpp').addEventListener('input', function () {
                                    const value = this.value;
                                    if (!/^\d{0,9}$/.test(value)) {
                                        this.setCustomValidity('КПП должен содержать только 9 цифр.');
                                    } else {
                                        this.setCustomValidity('');
                                    }
                                });

                                // Проверка ОГРН
                                document.getElementById('ogrn').addEventListener('input', function () {
                                    const value = this.value;
                                    if (!/^\d{0,13}$/.test(value)) {
                                        this.setCustomValidity('ОГРН должен содержать только 13 цифр.');
                                    } else {
                                        this.setCustomValidity('');
                                    }
                                });
                            });
                        </script>
                        <div class="mb-3">
                            <label for="website" class="form-label">Веб-сайт</label>
                            <input type="url" class="form-control" name="website" id="website" placeholder="Введите URL"
                                required>

                            <script>
                                document.getElementById('website').addEventListener('blur', function () {
                                    let value = this.value.trim();
                                    if (value && !/^https?:\/\//i.test(value)) {
                                        this.value = 'http://' + value;
                                    }
                                });
                            </script>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="legal_address" class="form-label">Юридический адрес</label>
                            <input type="text" class="form-control" id="legal_address" name="legal_address" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Телефон</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="registration_date" class="form-label">Дата регистрации</label>
                            <input type="date" class="form-control" id="registration_date" name="registration_date"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-danger"
                            data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary btn-success">Сохранить</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCompanyModal" tabindex="-1" aria-labelledby="editCompanyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="update_company.php">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCompanyModalLabel">Изменить данные компании</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Название компании</label>
                            <input type="text" name="company_name" class="form-control"
                                value="<?= htmlspecialchars($company_name) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="inn" class="form-label">ИНН</label>
                            <input type="text" name="inn" class="form-control" value="<?= htmlspecialchars($inn) ?>"
                                pattern="\d{10}" title="ИНН должен содержать 10 цифр" required>
                        </div>
                        <div class="mb-3">
                            <label for="kpp" class="form-label">КПП</label>
                            <input type="text" name="kpp" class="form-control" value="<?= htmlspecialchars($kpp) ?>"
                                pattern="\d{9}" title="КПП должен содержать 9 цифр" required>
                        </div>
                        <div class="mb-3">
                            <label for="ogrn" class="form-label">ОГРН</label>
                            <input type="text" name="ogrn" class="form-control" value="<?= htmlspecialchars($ogrn) ?>"
                                pattern="\d{13}" title="ОГРН должен содержать 13 цифр" required>
                        </div>
                        <div class="mb-3">
                            <label for="legal_address" class="form-label">Юридический адрес</label>
                            <input type="text" name="legal_address" class="form-control"
                                value="<?= htmlspecialchars($legal_address) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Телефон</label>
                            <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($email) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="website" class="form-label">Веб-сайт</label>
                            <input type="url" name="website" class="form-control"
                                value="<?= htmlspecialchars($website) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea name="description"
                                class="form-control"><?= htmlspecialchars($description) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="registration_date" class="form-label">Дата регистрации</label>
                            <input type="date" name="registration_date" class="form-control"
                                value="<?php echo htmlspecialchars((new DateTime($registration_date))->format('Y-m-d')); ?>"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>