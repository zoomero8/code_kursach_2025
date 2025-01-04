<?php
require "connectdb.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    $description = $_POST['description'];

    $stmt = $mysql->prepare("
        UPDATE companies
        SET phone = ?, email = ?, website = ?, description = ?
        WHERE user_id = ?
    ");
    $stmt->bind_param("ssssi", $phone, $email, $website, $description, $user_id);

    if ($stmt->execute()) {
        header("Location: account.php");
        exit();
    } else {
        echo "Ошибка: " . $stmt->error;
    }
}
?>
