<?php
require "connectdb.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Получение текущей выбранной компании из сессии
$current_company_id = $_SESSION['current_company_id'] ?? null;


// Получение данных доходов/расходов для текущей компании
$stmt = $mysql->prepare("
    SELECT id, type, category, amount, date, description
    FROM financial_records
    WHERE user_id = ? AND company_id = ?
    ORDER BY id ASC
");
$stmt->bind_param("ii", $user_id, $current_company_id);
$stmt->execute();
$result = $stmt->get_result();

if ($current_company_id) {
    $stmt = $mysql->prepare("SELECT company_name FROM companies WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $current_company_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($current_company_name);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>УчетОнлайн - Финансы</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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
                    <a href="account.php" class="list-group-item list-group-item-action">Профиль</a>
                    <a href="finances.php" class="list-group-item list-group-item-action active">Финансы</a>
                </div>
            </div>
            <div class="col-md-9">
                <!-- Прямоугольник с описанием -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">
                            Финансы компании <span
                                class="highlighted-company"><?= htmlspecialchars($current_company_name ?? ' ') ?></span>
                        </h3>

                        <p>Здесь вы можете управлять записями доходов и расходов, а также добавлять новые данные.</p>
                    </div>
                </div>

                <!-- Таблица -->
                <div class="card mt-4">
                    <div class="card-body">
                        <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal"
                            data-bs-target="#addTransactionModal">Добавить запись</button>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">№</th>
                                    <th style="width: 10%;">Тип</th>
                                    <th style="width: 20%;">Категория</th>
                                    <th style="width: 15%;">Сумма</th>
                                    <th style="width: 15%;">Дата</th>
                                    <th style="width: 35%;">Описание</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $counter = 1; // Порядковый номер
                                while ($row = $result->fetch_assoc()):
                                    ?>
                                    <tr class="table-row">
                                        <td><?= $counter++ ?></td> <!-- Генерация порядкового номера -->
                                        <td><?= htmlspecialchars($row['type']) ?></td>
                                        <td><?= htmlspecialchars($row['category']) ?></td>
                                        <td><?= htmlspecialchars(number_format($row['amount'], 2)) ?> ₽</td>
                                        <td><?= htmlspecialchars((new DateTime($row['date']))->format('d.m.Y')) ?></td>
                                        <td><?= htmlspecialchars($row['description'] ?? '—') ?></td>
                                        <td>
                                            <form action="delete_transaction.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <!-- <button type="submit" class="btn btn-danger btn-sm delete-btn"
                                                    onclick="return confirm('Вы уверены, что хотите удалить эту запись?');">
                                                    Удалить
                                                </button> -->
                                                <!-- Кнопка для открытия модального окна -->
                                                <button type="button" class="btn btn-danger btn-sm delete-btn"
                                                    data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                                                    data-id="<?= $row['id'] ?>">
                                                    Удалить
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="text-end mb-5 mt-3">
                    <div class="text-end mb-5 mt-3">
                        <?php if ($result->num_rows > 0): // Проверяем, есть ли записи в таблице ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#calculateModal">
                                Рассчитать суммы
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно добавления записи -->
    <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_transaction.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTransactionModalLabel">Добавить запись</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="type" class="form-label">Тип</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="income">Доход</option>
                                <option value="expense">Расход</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Категория</label>
                            <input type="text" class="form-control" id="category" name="category" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Сумма</label>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Дата</label>
                            <!-- Поле с форматированием -->
                            <input type="text" id="datePicker" class="form-control" name="date" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="calculateModal" tabindex="-1" aria-labelledby="calculateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="calculateModalLabel">Результаты расчета</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Общая сумма доходов:</strong> <span id="totalIncome">0 ₽</span></p>
                    <p><strong>Общая сумма расходов:</strong> <span id="totalExpense">0 ₽</span></p>
                    <p><strong id="netProfitLabel">Чистая прибыль:</strong> <span id="netProfit">0 ₽</span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Модальное окно для подтверждения удаления -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Подтверждение удаления</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Вы уверены, что хотите удалить эту запись? Это действие нельзя отменить.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <form id="deleteForm" action="delete_transaction.php" method="POST">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">Удалить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.querySelector('[data-bs-target="#calculateModal"]').addEventListener('click', function () {
            let rows = document.querySelectorAll('.table tbody tr');
            let totalIncome = 0;
            let totalExpense = 0;

            rows.forEach(row => {
                let type = row.cells[1].textContent.trim(); // Тип: Доход или Расход
                let amount = parseFloat(row.cells[3].textContent.replace(/[^0-9.-]+/g, '')); // Сумма

                if (type === 'Доход') {
                    totalIncome += amount;
                } else if (type === 'Расход') {
                    totalExpense += amount;
                }
            });

            // Расчет чистой прибыли
            let netProfit = totalIncome - totalExpense;

            // Обновление значений в модальном окне
            document.getElementById('totalIncome').textContent = totalIncome.toLocaleString('ru-RU') + ' ₽';
            document.getElementById('totalExpense').textContent = totalExpense.toLocaleString('ru-RU') + ' ₽';

            // Проверка на прибыль или убыток
            if (netProfit > 0) {
                document.getElementById('netProfitLabel').textContent = 'Чистая прибыль:';
            } else {
                document.getElementById('netProfitLabel').textContent = 'Убыток:';
            }

            document.getElementById('netProfit').textContent = netProfit.toLocaleString('ru-RU') + ' ₽';
        });
    </script>

    <script>
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
            // Кнопка, которая вызвала модальное окно
            const button = event.relatedTarget;
            // Получение ID записи из data-id атрибута
            const id = button.getAttribute('data-id');
            // Установка ID в скрытое поле формы
            const deleteInput = document.getElementById('deleteId');
            deleteInput.value = id;
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#datePicker", {
                dateFormat: "d.m.Y",
                defaultDate: new Date(), // Устанавливает текущую дату
                allowInput: true,
                locale: {
                    firstDayOfWeek: 1 // Неделя начинается с понедельника
                }
            });
        });

    </script>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>