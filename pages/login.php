<?php
require "connectdb.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $mysql->prepare("SELECT id, name, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $name, $password_hash);
    $stmt->fetch();

    if ($id && password_verify($password, $password_hash)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        header('Location: account.php');
        exit();
    } else {
        $error = 'Неверный email или пароль.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>УчетОнлайн - Вход</title>
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

    <!-- Форма входа -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Вход</h3>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="login-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="login-email" name="email" placeholder="Введите ваш email" required>
                            </div>
                            <div class="mb-3">
                                <label for="login-password" class="form-label">Пароль</label>
                                <input type="password" class="form-control" id="login-password" name="password" placeholder="Введите ваш пароль" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn custom-login-btn">Войти</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="register.php" class="text-success">Нет аккаунта? Зарегистрируйтесь</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
