<?php
include('config.php');

use Parse\ParseQuery;

session_start();

$query = new ParseQuery("SystemAdmin");
if (isset($_POST['btnsubmit'])) {
    $username = $_POST['username'];
    $password = $_POST['pass'];
    $query->equalTo('username', $username);
    $query->equalTo('password', $password);
    $result = $query->find();
    if ($result == null) {
        $_SESSION['message'] = 'Wrong password and username';
        $_SESSION['msg_type'] = 'danger';
        header('location:index.php');
        exit();
    } else {
        for ($i = 0; $i < count($result); $i++) {
            $object = $result[$i];
            $id = $object->getObjectId();
            $query->equalTo('username', 'admin');
            $results = $query->find();
            if (!$results == null) {
                $_SESSION['id'] = $id;
                $_SESSION['islogin'] = 'active';
                header('location:admin/dashboard.php');
                exit();
            } else {
                $_SESSION['message'] = 'Wrong password and username';
                $_SESSION['msg_type'] = 'danger';
                header('location:index.php');
                exit();
                // $_SESSION['id'] = $id;
                // header('location:user/dashboard.php');
                // exit();
            }
        }
    }
}


if (isset($_SESSION['id'])) {
    header('location:admin/dashboard.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laoac Superstore</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script>
        window.history.forward();

        function preventBack() {
            window.history.forward();
        }
    </script>
</head>

<body>
    <div class="container">
        <form action="index.php" method="post">
            <img src="logo/logo.svg" alt="">
            <?php if (isset($_SESSION['message'])) : ?>
                <div class="alert alert-<?= $_SESSION['msg_type'] ?>">
                    <i class="material-icons">error</i>
                    <?php echo $_SESSION['message'];
                    unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <input type="text" name="username" id="username" placeholder="username" required>
            </div>
            <div class="form-group">
                <input type="password" name="pass" id="pass" placeholder="password" required>
            </div>
            <div class="form-group">
                <button type="submit" id="btnlogin" name="btnsubmit">Login</button>
            </div>
        </form>
    </div>

</body>

</html>