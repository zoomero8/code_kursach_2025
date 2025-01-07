<?php
require "connectdb.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_company_id = $_SESSION['current_company_id'] ?? null;

    if (!$current_company_id) {
        die('Ошибка: Компания не выбрана. Пожалуйста, выберите компанию перед добавлением записи.');
    }

    $type = $_POST['type']; // income или expense
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $description = $_POST['description'];

    // Преобразование типа для соответствия ENUM
    if ($type === 'income') {
        $type = 'Доход';
    } elseif ($type === 'expense') {
        $type = 'Расход';
    } else {
        die('Ошибка: Некорректный тип транзакции.');
    }

    // Преобразование формата даты
    try {
        $date = (new DateTime($date))->format('Y-m-d'); // Преобразование в формат MySQL (гггг-мм-дд)
    } catch (Exception $e) {
        die('Ошибка: Некорректный формат даты.');
    }

    $stmt = $mysql->prepare("
        INSERT INTO financial_records (user_id, company_id, type, category, amount, date, description)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisssss", $user_id, $current_company_id, $type, $category, $amount, $date, $description);

    if ($stmt->execute()) {
        header("Location: finances.php");
        exit();
    } else {
        echo "Ошибка: " . $stmt->error;
    }
}
?>