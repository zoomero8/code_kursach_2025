<?php
require "connectdb.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $type = $_POST['type'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $description = $_POST['description'];

    $stmt = $mysql->prepare("
        INSERT INTO financial_records (user_id, type, category, amount, date, description)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssss", $user_id, $type, $category, $amount, $date, $description);

    if ($stmt->execute()) {
        header("Location: finances.php");
        exit();
    } else {
        echo "Ошибка: " . $stmt->error;
    }
}
?>
