<?php
require "connectdb.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = 'Пароли не совпадают!';
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $mysql->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Пользователь с таким email уже существует!';
        } else {
            $stmt = $mysql->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            if ($stmt->execute()) {
                header('Location: login.php');
                exit();
            } else {
                $error = 'Ошибка регистрации. Попробуйте снова.';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>УчетОнлайн - Регистрация</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="main_page.php">УчетОнлайн</a>
        </div>
    </nav>

    <!-- Форма регистрации -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Регистрация</h3>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="register-name" class="form-label">Имя</label>
                                <input type="text" class="form-control" id="register-name" name="name" placeholder="Введите ваше имя" required>
                            </div>
                            <div class="mb-3">
                                <label for="register-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="register-email" name="email" placeholder="Введите ваш email" required>
                            </div>
                            <div class="mb-3">
                                <label for="register-password" class="form-label">Пароль</label>
                                <input type="password" class="form-control" id="register-password" name="password" placeholder="Введите ваш пароль" required>
                            </div>
                            <div class="mb-3">
                                <label for="register-confirm-password" class="form-label">Подтверждение пароля</label>
                                <input type="password" class="form-control" id="register-confirm-password" name="confirm_password" placeholder="Подтвердите ваш пароль" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn custom-register-btn">Зарегистрироваться</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="login.php" class="text-primary">Уже есть аккаунт? Войти</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
