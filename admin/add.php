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
date_default_timezone_set('Asia/Manila');
$id = '';
$qty = '';
$name = '';
$date = date('d-m-y h:i:s');
if (isset($_GET['add'])) {
    $id = $_GET['add'];
    $query = new ParseQuery('Product');
    $query->equalTo('objectId', $id);
    $resulta = $query->find();
    for ($x = 0; $x < count($resulta); $x++) {
        $obj = $resulta[$x];
        $id = $obj->getObjectId();
        $name = $obj->get('productName');
        $qty = $obj->get('productQuantity');
    }
}

if (isset($_POST['confirmadd'])) {
    $id = $_POST['id'];
    $qty = $_POST['qty'];
    $newstock = $_POST['addStock'];
    $name = $_POST['name'];

    $updatedStock = intval($qty) + intval($newstock);

    $add = new ParseObject('Arrived');
    $add->set('user', 'admin');
    $add->set('productName', $name);
    $add->set('productQty', intval($newstock));
    $add->set('custdate', $date);
    $query = new ParseQuery('Product');
    $query->equalTo('objectId', $id);
    $result = $query->find();
    for ($x = 0; $x < count($result); $x++) {
        $obj = $result[$x];
        try {
            $obj->set('productQuantity', $updatedStock);
            $obj->save();
            $add->save();
        } catch (ParseException $ex) {
            echo 'Error' . $ex->getMessage();
        }
        $_SESSION['message'] = 'New Stock has been added';
        $_SESSION['msg_type'] = 'warning';
        header('location:stocks.php');
        exit();
    }
}

if (isset($_GET['cancel'])) {
    header('location:stocks.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stocks/Add Stock</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/update.css">
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
            <a href="stocks.php" class="link active">
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
        </div>
    </div>
    <div class="top">
        <i class="material-icons" id="menu">menu</i>
    </div>
    <?php if (isset($_SESSION['message'])) : ?>
        <div class="alert alert-<?= $_SESSION['msg_type'] ?>">
            <?php echo $_SESSION['message'];
            unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <div class="addmodal">
        <form action="add.php" method="post">
            <header>
                <h1>Add Stock</h1>
            </header>
            <div class="body">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="qty" value="<?= $qty ?>">
                <input type="hidden" name="name" value="<?= $name ?>">
                <input type="number" name="addStock" id="addStock" required>
                <div class="btn-wrap">
                    <button type="submit" name="confirmadd" id="confirmadd">Submit</button>
                    <!-- <button type="button" name="canceladd" id="canceladd">Cancel</button> -->
                    <a href="add.php?cancel" name="canceladd" id="canceladd">Cancel</a>
                </div>
            </div>
        </form>
    </div>
    <script src="../js/sidenav.js"></script>
</body>

</html>