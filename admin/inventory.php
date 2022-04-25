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

$id = 0;
$barcode = '';
$name = '';
$ctg = '';
$unit = '';
$qty = '';
$price = '';
$srp = '';
$expiry = '';
$update = false;
$likes = '';

//create
$product = new ParseObject('Product');
$nameChecker = new ParseQuery('Product');
$barcodeChecker = new ParseQuery('Product');
if (isset($_POST['save'])) {

    $barcode = $_POST['barcode'];
    $name = $_POST['name'];
    $ctg = $_POST['ctg'];
    $qty = $_POST['qty'];
    $unit = $_POST['unit'];
    $price = $_POST['price'];
    $expiry = $_POST['expiry'];

    $nameChecker->equalTo('productName', $name);
    $nameResult = $nameChecker->find();
    if ($nameResult == null) {
        $barcodeChecker->equalTo('Barcode', $barcode);
        $barcodeResult = $barcodeChecker->find();
        if ($barcodeResult == null) {
            $product->set('Barcode', $barcode);
            $product->set('productName', $name);
            $product->set('productCategory', $ctg);
            $product->set('productQuantity', intval($qty));
            $product->set('productUnit', $unit);
            $product->set('productPrice', $price);
            $product->set('expiryDate', $expiry);

            try {
                $product->save();
                echo 'New object created with objectId: ' . $product->getObjectId();
                $_SESSION['message'] = 'Record has been saved';
                $_SESSION['msg_type'] = 'success';
                header('location:inventory.php');
                exit();
            } catch (ParseException $ex) {
                echo 'Failed to create new object, with error message: ' . $ex->getMessage();
            }
        } else {
            $_SESSION['message'] = 'Barcode already exist';
            $_SESSION['msg_type'] = 'warning';
            header('location:inventory.php');
            exit();
        }
    } else {
        $_SESSION['message'] = 'Product already exist';
        $_SESSION['msg_type'] = 'warning';
        header('location:inventory.php');
        exit();
    }
}
//Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = new ParseQuery('Product');
    $query->equalTo('objectId', $id);
    $result = $query->find();
    for ($i = 0; $i < count($result); $i++) {
        $object = $result[$i];
        $object->destroy();
        $object->save();
        $_SESSION['message'] = 'Record has been deleted';
        $_SESSION['msg_type'] = 'danger';
        header('location:inventory.php');
        exit();
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
        header('location:inventory.php');
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
    <title>Inventory</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/stock.css">
</head>

<body>
    <div class="modal">
        <form action="inventory.php" method="post">
            <div class="header">
                <span>
                    <i class="material-icons" id="closeMod">close</i>
                </span>
                <h1>Add Items</h1>
            </div>
            <div class="body">
                <input type="hidden" name="id">
                <input type="text" placeholder="Item Barcode" name="barcode" id="bcode" required>
                <input type="text" placeholder="Item Name" name="name" id="pname" required>
                <select name="ctg">
                    <option value="FROZEN FOODS">Frozen Foods</option>
                    <option value="CANNED GOODS">Canned Goods</option>
                    <option value="BREAD AND BAKERY">Bread Bakery</option>
                    <option value="PERSONAL CARE">Personal Care</option>
                    <option value="BEER AND WINE">Beer and Wine</option>
                    <option value="DRINK AND BEVERAGES">Drink & Beverages</option>
                    <option value="COOKIES, SNACKS & CANDY">Cookies, Snacks & Candy</option>
                    <option value="CONDIMENTS AND SPICES">Condiments & Spices</option>
                    <option value="DAIRY, EGGS, AND CHEESE">Dairy,Eggs & Cheese</option>
                    <option value="PAPER PRODUCTS">Paper Products</option>
                    <option value="CLEANING SUPPLIES">Cleaning Supplies</option>
                    <option value="CONDENSED/DRIED MILK">Condensed/Dried Milk</option>
                    <option value="PASTA AND NOODLES">Pasta and Noodles</option>
                    <option value="BABY PRODUCTS">Baby Products</option>
                    <option value="COFFEE & TEA">Coffee & Tea</option>
                </select>
                <select name="unit">
                    <option value="GRAMS">GRAMS</option>
                    <option value="MILLILITER">MILLILITER</option>
                    <option value="LITERS">LITER</option>
                    <option value="KILOGRAMS">KILOGRAM</option>
                    <option value="MILIGRAMS">MILIGRAM</option>
                    <option value="MILIMETER">MILIMETER</option>
                    <option value="PIECES">PIECES</option>
                    <option value="NOT SPECIFIED">NOT SPECIFIED</option>
                </select>
                <input type="number" placeholder="Item Quantity" id="qtys" name="qty" required>
                <input type="number" placeholder="Item Price" id="prices" name="price" step="0.01" required>
                <input type="date" name="expiry" id="expdate" required>
                <button type="submit" name="save">Save</button>
            </div>
        </form>
    </div>
    <div class="modal-admin">
        <form action="inventory.php" method="post">
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
            <a href="inventory.php" class="link active">
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
        <form id="searches" method="post" action="#">
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
        <button type="button" id="btnadd">Add Item</button>
    </div>
    <div class="scroll-wrap">
        <table>
            <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Expiry Date</th>
                    <th colspan="2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $query = new ParseQuery('Product');
                $query->descending("createdAt");
                $result = $query->find();

                for ($i = 0; $i < count($result); $i++) :
                    $object = $result[$i];
                    $id = $object->getObjectId();
                    $barcode = $object->get('Barcode');
                    $name = $object->get("productName");
                    $category = $object->get("productCategory");
                    $quantity = $object->get("productQuantity");
                    $unit = $object->get("productUnit");
                    $price = $object->get("productPrice");
                    $expiry = $object->get("expiryDate");


                ?>
                    <tr id="result">
                        <td id="fixCol"><?php echo $barcode ?></td>
                        <td><?php echo $name ?></td>
                        <td><?php echo $category ?></td>
                        <td><?php echo $quantity ?></td>
                        <td><?php echo $unit ?></td>
                        <td><?php echo $price ?></td>
                        <td><?php echo $expiry ?></td>
                        <td>
                            <a href="update.php?edit=<?php echo $id; ?>" id="edit">Edit</a>
                        </td>
                        <td>
                            <a href="inventory.php?delete=<?php echo $id; ?>" id="delete">Delete</a>
                        </td>
                    </tr>
                <?php endfor ?>
            </tbody>
        </table>
    </div>
    <script src="../js/sidenav.js"></script>
    <script src="../js/modal.js"></script>
    <script src="../js/admin.js"></script>
    <script src="../js/close.js"></script>
    <script src="../js/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#search').keyup(function() {
                var txt = $(this).val();
                $('tbody').html('');
                $.ajax({
                    url: "searchInventory.php",
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