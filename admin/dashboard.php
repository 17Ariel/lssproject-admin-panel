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
    $result = $query->find();
}


if (isset($_POST['logout'])) {
    session_destroy();
    header('location:../index.php');
    exit();
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
        header('location:dashboard.php');
        exit();
    } else {
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
    <title>Dashboard</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/dash.css">
</head>

<body>
    <div class="modal">
        <form action="dashboard.php" method="post">
            <div class="header">
                <h2>Are you sure you want to logout?</h2>
            </div>
            <div class="body">
                <button type="submit" name="logout" id="logout">Yes</button>
                <button type="button" name="cancel" id="cancel">Cancel</button>
            </div>
        </form>
    </div>
    <div class="modal-admin">
        <form action="dashboard.php" method="post">
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
            $results = $result;
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
            <a href="dashboard.php" class="link active">
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
            <!-- <a href="#" class="link" id="admin">
                <i class="material-icons">settings</i>
                Settings
            </a> -->
        </div>
    </div>
    <div class="top">
        <i class="material-icons" id="menu">menu</i>
        <a href="#" id="btnout">
            <i class="material-icons">logout</i>
        </a>

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
        <?php
        $totals = new ParseQuery('Sales');
        $result = $totals->aggregate($results = [
            'group' => [
                'objectId' => null,
                'total' => ['$sum' => '$subTotal'],
            ]
        ]);

        $order = new ParseQuery('Order');
        $order->equalTo('status', 'Pending');
        $count = $order->count();
        ?>
        <a class="chart chart-sales">
            <i class="material-icons">insert_chart</i>
            <h3>Total Sales</h3>
            <p>
                <?php
                foreach ($result as $res) {
                    echo ($res['total']);
                }
                ?>
            </p>
        </a>
        <a class="chart chart-orders">
            <i class="material-icons">shopping_cart</i>
            <h3>Pending Orders</h3>
            <p><?php echo $count ?></p>
        </a>
        <a class="chart chart-stock" href="notify.php?notify">
            <i class="material-icons">move_to_inbox</i>
            <h3>Out of stock</h3>
            <?php
            $stock = new ParseQuery('Product');
            $stock->lessThan('productQuantity', 10);
            $stockcount = $stock->count();
            ?>
            <p><?php echo $stockcount ?></p>
        </a>
    </div>
    <script src="../js/sidenav.js"></script>
    <script src="../js/admin.js"></script>
    <script>
        const logoutbtn = document.getElementById('btnout');
        const cancelbtn = document.getElementById('cancel')

        const logoutOpen = () => {
            document.getElementsByClassName('modal')[0].style.top = '0'
        }

        const closed = () => {
            document.getElementsByClassName('modal')[0].style.top = '-2000px'
        }
        logoutbtn.addEventListener('click', logoutOpen);
        cancelbtn.addEventListener('click', closed);
    </script>
    <script src="../js/close.js"></script>
</body>

</html>