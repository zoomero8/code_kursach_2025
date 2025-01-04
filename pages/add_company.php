<?php
require "connectdb.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $company_name = $_POST['company_name'];
    $inn = $_POST['inn'];
    $kpp = $_POST['kpp'];
    $legal_address = $_POST['legal_address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $registration_date = $_POST['registration_date'];
    $ogrn = $_POST['ogrn'];
    $website = $_POST['website'];
    $description = $_POST['description'];

    // Проверка ИНН
    if (!preg_match('/^\d{10}$/', $inn)) {
        die('Ошибка: ИНН должен состоять из 10 цифр.');
    }

    // Проверка КПП
    if (!preg_match('/^\d{9}$/', $kpp)) {
        die('Ошибка: КПП должен состоять из 9 цифр.');
    }

    // Проверка ОГРН
    if (!preg_match('/^\d{13}$/', $ogrn)) {
        die('Ошибка: ОГРН должен состоять из 13 цифр.');
    }

    // Преобразование формата даты
    if (!empty($registration_date)) {
        try {
            $registration_date = (new DateTime($registration_date))->format('Y-m-d');
        } catch (Exception $e) {
            die('Ошибка: Неверный формат даты.');
        }
    } else {
        $registration_date = null;
    }

    // Выполнение SQL-запроса
    $stmt = $mysql->prepare("
        INSERT INTO companies (user_id, company_name, inn, kpp, legal_address, phone, email, registration_date, ogrn, website, description) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issssssssss", $user_id, $company_name, $inn, $kpp, $legal_address, $phone, $email, $registration_date, $ogrn, $website, $description);

    if ($stmt->execute()) {
        header("Location: account.php");
        exit();
    } else {
        echo "Ошибка: " . $stmt->error;
    }
}
?>
