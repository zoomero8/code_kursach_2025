    <?php
    session_start();
    require "connectdb.php";

    if (isset($_GET['company_id'])) {
        $_SESSION['current_company_id'] = $_GET['company_id'];
        header("Location: account.php");
        exit();
    } else {
        header("Location: account.php");
        exit();
    }
    ?>
