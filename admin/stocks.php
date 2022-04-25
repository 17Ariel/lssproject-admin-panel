<?php
include('../config.php');

use Parse\ParseQuery;


session_start();
$id = 0;
$barcode = '';
$name = '';
$ctg = '';
$unit = '';
$qty = '';
$price = '';
$srp = '';
$expiry = '';
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
        header('location:stocks.php');
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
    <title>Stocks</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/stock.css">
</head>

<body>
    <div class="modal-admin">
        <form action="stocks.php" method="post">
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
            <!-- <a href="#" class="link" id="admin">
                <i class="material-icons">settings</i>
                Settings
            </a> -->
        </div>
    </div>
    <div class="top">
        <i class="material-icons" id="menu">menu</i>
        <?php
        $alerts = new ParseQuery('Product');
        $alerts->lessThan('productQuantity', 10);
        $count = $alerts->count();
        ?>
        <a href="notify.php?notify" id="notify">
            <?php if ($count == 0) : ?>
                <span id="nothing"></span>
            <?php else : ?>
                <span><?php echo $count ?></span>
            <?php endif ?>
            <i class="material-icons" id="notify">notifications_active
            </i>
        </a>
        <form id="searchitem" method="#" action="#">
            <input type="search" name="searchinput" id="search" placeholder="Search here..... " required>
        </form>
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
        <a href="history.php?history" id="history">
            Product Sold
        </a>
        <a href="newstock.php" id="added">
            Product Added
        </a>
    </div>
    <div class="scroll-wrap">
        <table>
            <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th colspan="2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = new ParseQuery('Product');
                $query->greaterThan('productQuantity', 10);
                $query->descending("createdAt");
                $result = $query->find();
                for ($i = 0; $i < count($result); $i++) :
                    $object = $result[$i];
                    $id = $object->getObjectId();
                    $barcode = $object->get('Barcode');
                    $name = $object->get("productName");
                    $category = $object->get("productCategory");
                    $quantity = $object->get("productQuantity");
                ?>
                    <tr id="result">
                        <td id="fixCol"><?php echo $barcode ?></td>
                        <td><?php echo $name ?></td>
                        <td><?php echo $category ?></td>
                        <td><?php echo $quantity ?></td>
                        <td>
                            <a href="add.php?add=<?php echo $id; ?>" class="add">
                                <i class="material-icons">add</i>
                            </a>
                        </td>
                    </tr>
                <?php endfor ?>
            </tbody>
            </tbody>
        </table>
    </div>
    <script src="../js/sidenav.js"></script>
    <script src="../js/admin.js"></script>
    <script src="../js/close.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').keyup(function() {
                var txt = $(this).val();
                $('tbody').html('');
                $.ajax({
                    url: "searchStock.php",
                    method: 'post',
                    data: {
                        search: txt
                    },
                    dataType: "text",
                    success: function(data) {
                        $('tbody').html(data);
                    }
                })
            });
        });
    </script>
</body>

</html>