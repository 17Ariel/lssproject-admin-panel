<?php
include('../config.php');

use Parse\ParseException;
use Parse\ParseObject;
use Parse\ParseQuery;

session_start();
if (!isset($_SESSION['id']) || (trim($_SESSION['id']) == '')) {
    header('location:../index.php');
    exit();
} else {
    $id = $_SESSION['id'];
    $query = new ParseQuery('SystemAdmin');
    $query->equalTo('objectId', $id);
    $results = $query->find();
}

$usernameChecker = new ParseQuery('SystemAdmin');
$nameChecker = new ParseQuery('SystemAdmin');
if (isset($_POST['submit'])) {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $names = $_POST['fullname'];
    $usernameChecker->equalTo('username', $user);
    $userResult = $usernameChecker->find();

    if ($userResult == $null) {
        $nameChecker->equalTo('fullname', $names);
        $nameResult = $nameChecker->find();
        if ($nameResult == $null) {
            $systemUser = new ParseObject('SystemAdmin');
            $systemUser->set('username', $user);
            $systemUser->set('password', $pass);
            $systemUser->set('fullname', $names);

            try {
                $systemUser->save();
                $_SESSION['message'] = 'New user has been set';
                $_SESSION['msg_type'] = 'success';
                header('location:settings.php');
                exit();
            } catch (ParseException $ex) {
                echo 'Failed to create new object, with error message:' . $ex->getMessage();
            }
        } else {
            $_SESSION['message'] = 'fullname already exist';
            $_SESSION['msg_type'] = 'warning';
            header('location:settings.php');
            exit();
        }
    } else {
        $_SESSION['message'] = 'user already exist';
        $_SESSION['msg_type'] = 'warning';
        header('location:settings.php');
        exit();
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = new ParseQuery('SystemAdmin');
    $query->equalTo('objectId', $id);
    $result = $query->find();
    for ($i = 0; $i < count($result); $i++) {
        $object = $result[$i];
        $object->destroy();
        $object->save();
        $_SESSION['message'] = 'User has been deleted by the admin';
        $_SESSION['msg_type'] = 'danger';
        header('location:settings.php');
        exit();
    }
}


$id = 0;
$users = '';
$fullname = '';
$password = '';
$update = false;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update = true;
    $queries = new ParseQuery('SystemAdmin');
    $queries->equalTo('objectId', $id);
    $results = $queries->find();
    for ($i = 0; $i < count($results); $i++) {
        $object = $results[$i];
        $id = $object->getObjectId();
        $users = $object->get('username');
        $password = $object->get('password');
        $fullname = $object->get('fullname');
    }
}

if (isset($_POST['update'])) {
    $query = new ParseQuery('SystemAdmin');
    $id = $_POST['id'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $fname = $_POST['fullname'];
    $usernameChecker->equalTo('username', $user);
    $userResult = $usernameChecker->find();
    $query->equalTo('objectId', $id);
    $result = $query->find();
    if ($userResult == $result || $userResult == null) {
        $nameChecker->equalTo('fullname', $fname);
        $nameResult = $nameChecker->find();
        if ($nameResult == $result || $nameResult == null) {
            $query = new ParseQuery('SystemAdmin');
            $query->equalTo('objectId', $id);
            $result = $query->find();
            for ($i = 0; $i < count($result); $i++) {
                $object = $result[$i];
                try {
                    $object->set('username', $user);
                    $object->set('password', $pass);
                    $object->set('fullname', $fname);
                    $object->save();

                    $_SESSION['message'] = 'User has been updated by the admin';
                    $_SESSION['msg_type'] = 'warning';
                    header('location:settings.php');
                    exit();
                } catch (ParseException $ex) {
                    echo 'Failed to create new object, with error message: ' . $ex->getMessage();
                }
            }
        } else {
            $_SESSION['message'] = 'Fullname already exists';
            $_SESSION['msg_type'] = 'warning';
            header('location:settings.php');
            exit();
        }
    } else {
        $_SESSION['message'] = 'Username has already been used';
        $_SESSION['msg_type'] = 'warning';
        header('location:settings.php');
        exit();
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/settings.css">
</head>

<body>
    <div class="sidenav">
        <div class="admin">
            <i class="material-icons">admin_panel_settings</i>
            <?php
            for ($i = 0; $i < count($results); $i++) :
                $object = $results[$i];
                $iD = $object->getObjectId();
                $username = $object->get('username');

            ?>
                <h3>
                    <?php echo $username; ?>
                </h3>
            <?php endfor; ?>
        </div>
        <i class="material-icons" id="sideclose">close</i>
        <div class="ul">
            <a href="dashboard.php" class="link">
                <i class="material-icons">dashboard</i>
                Dashboard
            </a>
            <a href="inventory.php" class="link">
                <i class="material-icons">inventory_2</i>
                Inventory
            </a>
            <a href="stocks.php" class="link">
                <i class="material-icons">warehouse</i>
                Stocks
            </a>
            <a href="order.php" class="link">
                <i class="material-icons">shopping_cart</i>
                Orders
            </a>
            <a href="sales.php" class="link">
                <i class="material-icons">insert_chart</i>
                Sales
            </a>
            <a href="customer.php" class="link">
                <i class="material-icons">reviews</i>
                Reviews
            </a>
            <a href="refund.php" class="link">
                <i class="material-icons">find_replace</i>
                Refund
            </a>
            <a href="settings.php" class="link active">
                <i class="material-icons">settings</i>
                Settings
            </a>
        </div>
    </div>
    <div class="top">
        <i class="material-icons" id="menu">menu</i>
    </div>
    <?php if (isset($_SESSION['message'])) : ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?>">
            <?php echo $_SESSION['message'];
            unset($_SESSION['message']);
            ?>
            <span>
                <i class="material-icons" id="alert-closer">close</i>
            </span>
        </div>
    <?php endif; ?>
    <div class="content-wrap">
        <form action="settings.php" method="post">
            <h3>System User</h3>
            <input type="hidden" name="id" value="<?= $id ?>">
            <?php
            if ($users == 'admin') :
            ?>
                <input type="text" name="user" placeholder="username/position" value="<?= $users; ?>" required readonly>
            <?php else : ?>
                <input type="text" name="user" placeholder="username/position" value="<?= $users; ?>" required>
            <?php endif ?>
            <input type="text" name="fullname" placeholder="fullname" value="<?= $fullname; ?>" required>
            <input type="password" name="pass" placeholder="password" value="<?= $password; ?>" required>
            <?php
            if ($update === true) :
            ?>
                <button type="submit" name="update" id="update">Update</button>
            <?php else : ?>
                <button type="submit" name="submit" id="submit">Save</button>
            <?php endif; ?>
        </form>

        <?php
        $query = new ParseQuery('SystemAdmin');
        $result = $query->find();
        ?>
        <div class="card">
            <div class="header">
                <h1>Username</h1>
                <h1>Fullname</h1>
                <h1>Action</h1>
            </div>
            <?php
            for ($i = 0; $i < count($result); $i++) :
                $object = $result[$i];
                $id = $object->getObjectId();
                $user = $object->get('username');
                $fullname = $object->get('fullname');
            ?>
                <div class="body">
                    <div class="left">
                        <p id="name"><?php echo $user; ?></p>
                    </div>
                    <div class="middle">
                        <p id="fname"><?php echo $fullname; ?></p>
                    </div>
                    <div class="right">
                        <a href="settings.php?edit=<?php echo $id; ?>" id="edit">Edit</a>
                        <?php
                        if ($user == "admin") :
                        ?>
                            <a id="disabled">Delete</a>
                        <?php else : ?>
                            <a href="settings.php?delete=<?php echo $id; ?>" id="delete">Delete</a>
                        <?php endif ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <script src="../js/sidenav.js"></script>
    <script src="../js/close.js"></script>
</body>

</html>