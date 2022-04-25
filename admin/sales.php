<?php
include('../config.php');

use Parse\ParseQuery;
use Parse\ParseObject;
use Parse\ParseException;

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


$barcode = '';
$name = '';
$price = '';
$qty = '';
$subtotal = '';
$user = '';
$total = 0;
$newQty = 0;
$tax = '';
$total = '';
$Qty = "";

if (isset($_POST['searchbtn'])) {
    $searchinput = $_POST['search'];
    $queries = new ParseQuery('Product');
    $queries->equalTo('Barcode', $searchinput);
    $searches = $queries->find();
    for ($i = 0; $i < count($searches); $i++) {
        $object = $searches[$i];
        $id = $object->getObjectId();
        $barcode = $object->get("Barcode");
        $name = $object->get("productName");
        $price = $object->get("productPrice");
    }
}
if (isset($_POST['btnsend'])) {
    $barcode = $_POST['barcode'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $qty = $_POST['qty'];
    $address = $_POST['address'];

    $productQuery = new ParseQuery('Product');
    $productQuery->equalTo('Barcode', $barcode);
    $productResult = $productQuery->find();
    for ($x = 0; $x < count($productResult); $x++) {
        $productObject = $productResult[$x];
        $Qty = $productObject->get('productQuantity');
    }
    if (($Qty) <= $qty) {
        $_SESSION['message'] = 'Insuficient Stock';
        $_SESSION['msg_type'] = 'danger';
        header('location:sales.php');
        exit();
    } else {
        $subtotal = floatval($price) * intval($qty);
        $sales = new ParseObject('Sales');
        $sales->set('Barcode', $barcode);
        $sales->set('productName', $name);
        $sales->set('productPrice', floatval($price));
        $sales->set('productQty', intval($qty));
        $sales->set('subTotal', $subtotal);
        $sales->set('saleStatus', 'processing');
        $newQty = intval($Qty) - intval($qty);
        $productObject->set('productQuantity', $newQty);
        try {
            $productObject->save();
            $sales->save();
            $_SESSION['address'] = $address;
            header('location:sales.php');
            exit();
        } catch (ParseException $ex) {
            echo 'error:' . $ex->getMessage();
        }
    }
}

if (isset($_POST['compute'])) {
    $customerPayment = $_POST['payment'];
    $totalOrder = $_POST['total'];
    $changes = floatval($customerPayment) - floatval($totalOrder);
    $_SESSION['cash'] = $customerPayment;
    $_SESSION['changes'] = $changes;
    header('location:sales.php');
    exit();
}

if (isset($_POST['reset'])) {
    $changes = 0;
    $product = new ParseQuery('Sales');
    $resultOf = $product->find();
    for ($i = 0; $i < count($resultOf); $i++) {
        $object = $resultOf[$i];
        try {
            $object->set('saleStatus', 'done');
            $object->save();
        } catch (ParseException $ex) {
            echo 'Failed to create new object, with error message: ' . $ex->getMessage();
        }
    }
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
        header('location:sales.php');
        exit();
    } else {
        header('location:settings.php');
        exit();
    }
}
$barcodes = '';
$qtys = '';
$getqty = '';
$setqty = '';
if (isset($_GET['delete'])) {
    $id = $_GET['id'];
    $barcodes = $_GET['barcodes'];
    $qtys = $_GET['qtys'];
    $productAdd = new ParseQuery('Product');
    $productAdd->equalTo('Barcode', $barcodes);
    $addRes = $productAdd->find();
    for ($i = 0; $i < count($addRes); $i++) {
        $objects = $addRes[$i];
        $getqty = $objects->get('productQuantity');
    }
    $setqty = intval($getqty) + intval($qtys);
    $delete = new ParseQuery('Sales');
    $delete->equalTo('objectId', $id);
    $theResult = $delete->find();
    for ($x = 0; $x < count($theResult); $x++) {
        $obj = $theResult[$x];
        $obj->destroy();
        try {
            $objects->set('productQuantity', $setqty);
            $objects->save();
            $obj->save();
            header('location:sales.php');
            exit();
        } catch (ParseException $ex) {
            echo 'Err' . $ex->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/sales.css">
</head>

<body>
    <div class="modal-admin">
        <form action="sales.php" method="post">
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
            <a href="sales.php" class="link active">
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
    <div class="mini-grid">
        <a href="month.php" class="mini mini-1">
            <h3>Monthly Sales</h3>
        </a>
        <a href="year.php" class="mini mini-2">
            <h3>Yearly Sales</h3>
        </a>
        <a href="day.php" class="mini mini-3">
            <h3>Daily Sales</h3>
        </a>
    </div>
    <div class="content-grid">
        <a href="processWalkin.php" class="child child-walkin">
            <i class="material-icons">point_of_sale</i>
            <h2>Walkin</h2>
        </a>
        <a href="processOrder.php" class="child child-online">
            <i class="material-icons">devices</i>
            <h2>Online</h2>
        </a>
    </div>
    <script src="../js/sidenav.js"></script>
    <script src="../js/admin.js"></script>
    <script src="../js/close.js"></script>
</body>

</html>