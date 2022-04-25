<?php
include('../config.php');

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

if (isset($_POST['confirm'])) {
    $adminPass = $_POST['adminpassword'];
    $Query = new ParseQuery('SystemAdmin');
    $Query->equalTo('username', 'admin');
    $Query->equalTo('password', $adminPass);
    $Result = $Query->find();
    if ($Result == null) {
        $_SESSION['message'] = 'Wrong password';
        $_SESSION['msg_type'] = 'danger';
        header('location:customer.php');
        exit();
    } else {
        header('location:settings.php');
        exit();
    }
}
$username = '';
$product = '';
$comment = '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/customer.css">
</head>

<body>
    <div class="modal-admin">
        <form action="customer.php" method="post">
            <header>
                <h1>Admin Password</h1>
            </header>
            <div class="body">
                <input type="password" name="adminpassword" id="password" required>
                <div class="btn-wrap">
                    <button type="submit" name="confirm" id="confirm">Submit</button>
                    <button type="button" name="cancel" id="cancelIn">cancel</button>
                </div>
            </div>
        </form>
    </div>
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
            <a href="customer.php" class="link active">
                <i class="material-icons">reviews</i>
                Reviews
            </a>
            <a href="refund.php" class="link">
                <i class="material-icons">find_replace</i>
                Refund
            </a>
            <!-- <a href="#" class="link" id="admin">
                <i class="material-icons">settings</i>
                Settings
            </a> -->
        </div>
    </div>
    <div class="top">
        <i class="material-icons" id="menu">menu</i>
    </div>
    <?php if (isset($_SESSION['message'])) : ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?>">
            <?php echo $_SESSION['message'];
            unset($_SESSION['message']); ?>
            <span>
                <i class="material-icons" id="alert-closer">close</i>
            </span>
        </div>
    <?php endif; ?>
    <?php
    $query = new ParseQuery('Order');
    $query->notEqualTo('comment', null);
    $result = $query->find();
    for ($i = 0; $i < count($result); $i++) :
        $object = $result[$i];
        $username = $object->get('username');
        $product = $object->get('productName');
        $comment = $object->get('comment');
    ?>
        <div class="card">
            <div class="wrapper">
                <header>
                    <h1><?php echo $product; ?></h1>
                </header>
                <div class="body">
                    <div class="tag">
                        <h3><?php echo $username; ?></h3>
                        <p><?php echo $comment; ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endfor ?>
    <script src="../js/sidenav.js"></script>
    <script src="../js/admin.js"></script>
    <script src="../js/close.js"></script>
</body>

</html>