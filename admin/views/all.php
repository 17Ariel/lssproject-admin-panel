<?php
include('../../config.php');

use Parse\ParseObject;
use Parse\ParseQuery;
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


$id == '';
$user = '';
$address = '';
$pname = '';
$price = '';
$qty = '';
$status = '';
$ship = '';
$total = '';
$send = '';
$subTotal = '';
$totalAmount = '';
//Get
if (isset($_GET['show'])) {
    $order = $_GET['show'];
    $query = new ParseQuery('Order');
    $query->equalTo("orderNumber", $order);
    $result = $query->find();
    for ($i = 0; $i < count($result); $i++) {
        $object = $result[$i];
        $user = $object->get('username');
        $address = $object->get('address');
        $ship = $object->get('shippingFee');
        $payment = $object->get('paymentMethod');
        $orderNumber = $object->get('orderNumber');
        $subTotal = $object->get('subTotal');
        $totalAmount = $object->get('totalPayment');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders</title>
    <link rel="shortcut icon" href="../../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../../css/view.css">
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
            <a href="../dashboard.php" class="link">
                <i class="material-icons">dashboard</i>
                Dashboard
            </a>
            <a href="../inventory.php" class="link">
                <i class="material-icons">inventory_2</i>
                Inventory
            </a>
            <a href="../stocks.php" class="link">
                <i class="material-icons">warehouse</i>
                Stocks
            </a>
            <a href="../order.php" class="link active">
                <i class="material-icons">shopping_cart</i>
                Orders
            </a>
            <a href="../sales.php" class="link">
                <i class="material-icons">insert_chart</i>
                Sales
            </a>
            <a href="../customer.php" class="link">
                <i class="material-icons">reviews</i>
                Reviews
            </a>
            <a href="../refund.php" class="link">
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
            <span id="close_alert">X</span>
        </div>
    <?php endif; ?>
    <div class="modal">
        <div class="details">
            <header id="orderDetails">
                <h1>
                    <span id="user"><?php echo $user ?>
                </h1></span>
                </h1>
                <p>
                    Order Number: <span id="orderNum"><?php echo $orderNumber ?></span>
                </p>
                <p>
                    Payment Method:<span id="paymentMethod"><?php echo $payment; ?></span>
                </p>
                <p>
                    Shipping Address: <span id="address"><?php echo $address ?></span>
                </p>
                <p>
                    Shipping Fee: <span id="fee"><?php echo $ship; ?></span>
                </p>
                <p>
                    Subtotal: <span id="subtotal"><?php echo $subTotal; ?></span>
                </p>
                <p>
                    Total Amount: <span id="totalpayment"><?php echo $totalAmount; ?></span>
                </p>
            </header>
            <?php
            $barcode = '';
            for ($i = 0; $i < count($result); $i++) :
                $object = $result[$i];
                $id = $object->getObjectId();
                $barcode = $object->get('barcode');
                $pname = $object->get('productName');
                $price = $object->get('productPrice');
                $qty = $object->get('productQty');
            ?>
                <div class="card">
                    <div class="wrapper">
                        <header>
                            <h1>
                                <span id="barcode"><?php echo $barcode; ?></span>
                            </h1>
                        </header>
                        <div class="body">
                            <div class="tag">
                                <p>
                                    Name:<span id="name"><?php echo $pname; ?></span>
                                </p>
                                <p>
                                    Qty:<span id="qty"><?php echo $qty; ?></span>
                                </p>
                                <p>
                                    Price:<span id="price"><?php echo $price; ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
    <script src="../../js/sidenav.js"></script>
</body>

</html>