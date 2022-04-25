<?php
include('../config.php');

use Parse\ParseException;
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
$expiryDate = '';
$expiry = '';
//Get
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $query = new ParseQuery('Product');
    $query->equalTo('objectId', $id);
    $result = $query->find();
    for ($i = 0; $i < count($result); $i++) {
        $object = $result[$i];
        $id = $object->getObjectId();
        $barcode = $object->get('Barcode');
        $name = $object->get("productName");
        $ctg = $object->get("productCategory");
        $qty = $object->get("productQuantity");
        $unit = $object->get("productUnit");
        $price = $object->get("productPrice");
        $expiry = $object->get("expiryDate");
        // $expiryDate=$expiry->format('d/m/Y');
    }
}

//Update
$nameChecker = new ParseQuery('Product');
$barcodeChecker = new ParseQuery('Product');
$query = new ParseQuery('Product');
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $barcode = $_POST['barcode'];
    $name = $_POST['name'];
    $ctg = $_POST['ctg'];
    $qty = $_POST['qty'];
    $unit = $_POST['unit'];
    $price = $_POST['price'];
    $expiry = $_POST['expiry'];

    //converting string to float and integer
    // $quantity=intval($qty);
    // $prices=floatval($price);
    //converting string to dates
    // $exD=date_create($expiry);

    $nameChecker->equalTo('productName', $name);
    $nameResult = $nameChecker->find();
    $query->equalTo('objectId', $id);
    $result = $query->find();
    if ($nameResult == $result || $nameResult == null) {
        $barcodeChecker->equalTo('productBarcode', $barcode);
        $barcodeResult = $barcodeChecker->find();
        $query->equalTo('objectId', $id);
        $result = $query->find();
        if ($barcodeResult == $result || $barcodeResult == null) {
            $query->equalTo('objectId', $id);
            $result = $query->find();
            for ($i = 0; $i < count($result); $i++) {
                $object = $result[$i];
                try {
                    $object->set('Barcode', $barcode);
                    $object->set('productName', $name);
                    $object->set('productCategory', $ctg);
                    $object->set('productQuantity', intval($qty));
                    $object->set('productUnit', $unit);
                    $object->set('productPrice', $price);
                    $object->set('expiryDate', $expiry);
                    $object->save();
                } catch (ParseException $ex) {
                    echo 'Failed to create new object, with error message: ' . $ex->getMessage();
                }
            }
            $_SESSION['message'] = 'Record has been updated';
            $_SESSION['msg_type'] = 'warning';
            header('location:inventory.php');
            exit();
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


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory/update</title>
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
    <div class="modal">
        <form action="update.php" method="post">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="text" placeholder="Item Barcode" name="barcode" required id="barcode" value="<?= $barcode ?>">
            <input type="text" placeholder="Item Name" name="name" required id="name" value="<?= $name ?>">
            <select name="ctg" value="<?= $ctg ?>">
                <option value="<?= $ctg ?>"><?= $ctg ?></option>
                <option value="Frozen Foods">Frozen Foods</option>
                <option value="Canned Goods">Canned Goods</option>
                <option value="Bread Bakery">Bread Bakery</option>
                <option value="Personal Care">Personal Care</option>
                <option value="Beer and Wine">Beer and Wine</option>
                <option value="Drink & Beverages">Drink & Beverages</option>
                <option value="Snacks & Candy">Snacks & Candy</option>
                <option value="Condiments & Spices">Condiments & Spices</option>
                <option value="Dairy,Eggs & Cheese">Dairy,Eggs & Cheese</option>
                <option value="Paper Products">Paper Products</option>
                <option value="Cleaning Supplies">Cleaning Supplies</option>
                <option value="Condensed/Dried Milk">Condensed/Dried Milk</option>
                <option value="Pasta and Noodles">Pasta and Noodles</option>
                <option value="Baby Products">Baby Products</option>
                <option value="Coffee & Tea">Coffee & Tea</option>
            </select>
            <select name="unit" value="<?= $unit ?>">
                <option value="<?= $unit ?>"><?= $unit ?></option>
                <option value="GRAMS">GRAMS</option>
                <option value="MILLILITER">MILLILITER</option>
                <option value="LITERS">LITER</option>
                <option value="KILOGRAMS">KILOGRAM</option>
                <option value="MILIGRAMS">MILIGRAM</option>
                <option value="MILIMETER">MILIMETER</option>
                <option value="PIECES">PIECES</option>
                <option value="NOT SPECIFIED">NOT SPECIFIED</option>
            </select>
            <input type="number" placeholder="Item Quantity" name="qty" required id="qty" value="<?= $qty ?>">
            <input type="number" placeholder="Item Price" name="price" step="0.01" required id="price" value="<?= $price ?>">
            <input type="date" name="expiry" required id="exp" value="<?= $expiry ?>">
            <button type="submit" id="update" name="update">Update</button>
        </form>
    </div>
    <script src="../js/sidenav.js"></script>
</body>

</html>