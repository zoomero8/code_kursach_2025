<?php
require "connectdb.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $user_id = $_SESSION['user_id'];

    // Удаляем запись только для текущего пользователя
    $stmt = $mysql->prepare("DELETE FROM financial_records WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);

    if ($stmt->execute()) {
        header("Location: finances.php");
        exit();
    } else {
        echo "Ошибка при удалении: " . $stmt->error;
    }
}
?>
