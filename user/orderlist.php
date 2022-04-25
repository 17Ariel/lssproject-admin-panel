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

$id = 0;
$username = '';
$orderNumber = '';
$totalAmount = '';
$initialTime = '';
$finalTime = '';



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Slip</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/order.css">
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
            <a href="order.php" class="link active">
                <i class="material-icons">shopping_cart</i>
                Orders
            </a>
            <a href="sales.php" class="link">
                <i class="material-icons">insert_chart</i>
                Sales
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
            unset($_SESSION['message']);
            ?>
            <span id="close_alert">X</span>
        </div>
    <?php endif; ?>
    <div class="content-wrap" id="wrap">
        <a href="../pdf/order-pdf.php" id="report">Print</a>
    </div>
    <div class="scroll-wrap" id="ordertb">
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>OrderNumber</th>
                    <th>TotalAmount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $orderQuery = new ParseQuery('Order');
                $orderResult = $orderQuery->find();
                for ($i = 0; $i < count($orderResult); $i++) :
                    $objectOrder = $orderResult[$i];
                    $id = $objectOrder->getObjectId();
                    $username = $objectOrder->get('username');
                    $orderNumber = $objectOrder->get("orderNumber");
                    $totalAmount = $objectOrder->get("totalPayment");
                    $initialTime = $objectOrder->getUpdatedAt();
                    $finalTime = $initialTime->format('F j,Y');
                ?>
                    <tr id="result">
                        <td><?php echo $username ?></td>
                        <td><?php echo $orderNumber ?></td>
                        <td><?php echo $totalAmount ?></td>
                        <td><?php echo $finalTime ?></td>
                    </tr>
                <?php endfor ?>
            </tbody>
        </table>
    </div>
    <script src="../js/sidenav.js"></script>
</body>

</html>