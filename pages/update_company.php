<?php
require "connectdb.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_company_id = $_SESSION['current_company_id'];

    $company_name = $_POST['company_name'];
    $inn = $_POST['inn'];
    $kpp = $_POST['kpp'];
    $ogrn = $_POST['ogrn'];
    $legal_address = $_POST['legal_address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    $description = $_POST['description'];
    $registration_date = $_POST['registration_date'];

    $stmt = $mysql->prepare("
        UPDATE companies 
        SET company_name = ?, inn = ?, kpp = ?, ogrn = ?, legal_address = ?, phone = ?, email = ?, website = ?, description = ?, registration_date = ? 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("ssssssssssii", $company_name, $inn, $kpp, $ogrn, $legal_address, $phone, $email, $website, $description, $registration_date, $current_company_id, $user_id);

    if ($stmt->execute()) {
        header("Location: account.php");
        exit();
    } else {
        echo "Ошибка: " . $stmt->error;
    }
}
?>
