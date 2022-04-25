<?php
include('../config.php');

use Parse\ParseQuery;
use Parse\ParseException;

header('Cache-Control: no cache');
session_cache_limiter('private_no_expire');
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
        header('location:order.php');
        exit();
    } else {
        header('location:settings.php');
        exit();
    }
}
$displayStatus = "default";
$query = new ParseQuery('Order');
if (isset($_POST['filterbtn'])) {
    $choose = $_POST['filterOrder'];
    if ($choose == 'Tocollect') {
        $displayStatus = "Tocollect";
        $query->equalTo('status', 'To collect');
        $result = $query->distinct('orderNumber');
    } else if ($choose == 'Cancel') {
        $displayStatus = "Cancel";
        $query->equalTo('status', 'Cancelled');
        $result = $query->distinct('orderNumber');
    } else if ($choose == 'All') {
        $displayStatus = "All";
        $result = $query->distinct('orderNumber');
    } else if ($choose == 'Pending') {
        $displayStatus = "Pending";
        $query->equalTo('status', 'Pending');
        $result = $query->distinct('orderNumber');
    } else if ($choose == 'Toreview') {
        $displayStatus = "Toreview";
        $query->equalTo('status', 'To review');
        $result = $query->distinct('orderNumber');
    } else if ($choose == 'Topack') {
        $displayStatus = "Topack";
        $query->equalTo('status', 'To pack');
        $result = $query->distinct('orderNumber');
    } else if ($choose == 'Todeliver') {
        $displayStatus = "Todeliver";
        $query->equalTo('status', 'To deliver');
        $result = $query->distinct('orderNumber');
    } else {
        $displayStatus = "default";
        $query->equalTo('status', 'On process');
        $result = $query->distinct('orderNumber');
    }
} else {
    $displayStatus = "default";
    $query->equalTo('status', 'On process');
    $result = $query->distinct('orderNumber');
}

$setProcess = '';
if (isset($_GET['process'])) {
    $setProcess = $_GET['process'];
    $process = new ParseQuery('Order');
    $process->equalTo('orderNumber', $setProcess);
    $processStatus = $process->find();
    for ($i = 0; $i < count($processStatus); $i++) {
        $newProcess = $processStatus[$i];
        try {
            $newProcess->set('status', 'On process');
            $newProcess->save();
            $_SESSION['message'] = 'The order is now On process';
            $_SESSION['msg_type'] = 'success';
            header('location:order.php');
            exit();
        } catch (ParseException $ex) {
            echo 'Failed to create new object, with error message: ' . $ex->getMessage();
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
    <title>Orders</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/order.css">
</head>

<body>
    <div class="modal-admin">
        <form action="order.php" method="post">
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
            <a href="order.php" class="link active">
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
    <div class="content-wrap">
        <form action="order.php" method="post" class="filter-wrap">
            <select name="filterOrder">
                <option value="Onprocess">On Process</option>
                <option value="Pending">Pending</option>
                <option value="Tocollect">To Collect</option>
                <option value="Toreview">To Review</option>
                <option value="Todeliver">To Deliver</option>
                <option value="Topack">To Pack</option>
                <option value="Cancel">Cancel</option>
                <option value="All">All</option>
            </select>
            <button type="submit" name="filterbtn" id="btnsub">Submit</button>
        </form>
        <a href="orderlist.php" id="orderlist">
            <i class="material-icons">info</i>
        </a>
    </div>
    <table id="users">
        <thead>
            <tr>
                <th>Order Number</th>
                <th colspan="2">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($result as $orderNumber) :
            ?>
                <tr id="result">
                    <td><?php echo $orderNumber ?></td>
                    <td>
                        <?php if ($displayStatus == 'Todeliver') : ?>
                            <a href="mark.php?send=<?php echo $orderNumber; ?>" name="toMarked" id="view">
                                <i class="material-icons">done</i>
                            </a>
                        <?php elseif ($displayStatus == 'Cancel') : ?>
                            <a href="./views/cancel.php?show=<?php echo $orderNumber; ?>" name="show" id="view">
                                <i class="material-icons">visibility</i>
                            </a>
                        <?php elseif ($displayStatus == 'Tocollect') : ?>
                            <a href="./views/tocollect.php?show=<?php echo $orderNumber; ?>" name="show" id="view">
                                <i class="material-icons">visibility</i>
                            </a>
                        <?php elseif ($displayStatus == 'Topack') : ?>
                            <a href="./views/topack.php?show=<?php echo $orderNumber; ?>" name="show" id="view">
                                <i class="material-icons">visibility</i>
                            </a>
                        <?php elseif ($displayStatus == 'All') : ?>
                            <a href="./views/all.php?show=<?php echo $orderNumber; ?>" name="show" id="view">
                                <i class="material-icons">visibility</i>
                            </a>
                        <?php elseif ($displayStatus == 'Pending') :
                            $createdIn = '';
                            $dateCreated = new ParseQuery('Order');
                            $dateCreated->equalTo('orderNumber', $orderNumber);
                            $dateResult = $dateCreated->find();
                            for ($y = 0; $y < count($dateResult); $y++) {
                                $obj = $dateResult[$y];
                                $createdIn = $obj->getCreatedAt();
                                $created = $createdIn->format('d-m-Y h:i:s a');
                                $now = date_create(date('d-m-Y h:i:s a'));
                                $diff = date_diff(date_create($created), $now);
                                $value = $diff->format("%a");
                                $setstat = "";
                                if (intval($value) >= 1) {
                                    sleep(2);
                                    $changeStat = new ParseQuery('Order');
                                    $changeStat->equalTo('status', 'Pending');
                                    $changeRes = $changeStat->find();
                                    for ($y = 0; $y < count($changeRes); $y++) {
                                        $changeObj = $changeRes[$y];
                                        $changeObj->set('status', 'On process');
                                        $changeObj->save();
                                        header('location:order.php');
                                        exit();
                                    }
                                }
                            }
                        ?>
                            <a href="./views/setprocess.php?process=<?php echo $orderNumber; ?>" name="show" id="view">
                                <i class="material-icons">inventory</i>
                            </a>
                        <?php elseif ($displayStatus == 'Toreview') : ?>
                            <a href="./views/toreview.php?show=<?php echo $orderNumber; ?>" name="show" id="view">
                                <i class="material-icons">visibility</i>
                            </a>
                        <?php else : ?>
                            <a href="./views/onprocess.php?show=<?php echo $orderNumber; ?>" name="show" id="view">
                                <i class="material-icons">visibility</i>
                            </a>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script src="../js/sidenav.js"></script>
    <script src="../js/close.js"></script>
    <script src="../js/admin.js"></script>
</body>

</html>