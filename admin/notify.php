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
$barcode = '';
$name = '';
$ctg = '';
$qty = '';

if (isset($_GET['notify'])) {
    $queries = new ParseQuery('Product');
    $queries->lessThan('productQuantity', 10);

    $querys = new ParseQuery('Product');
    $querys->equalTo('productQuantity', 0);

    $finalquery = ParseQuery::orQueries([$queries, $querys]);
    $result = $finalquery->find();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stocks/Notify</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/stock.css">
</head>

<body>
    <div class="modal">
        <form action="stocks.php" method="post">
            <div class="header">
                <span>
                    <i class="material-icons" id="closeMod">close</i>
                </span>
                <h1>Add Items</h1>
            </div>
            <div class="body">
                <input type="hidden" name="id">
                <input type="text" placeholder="Item Barcode" name="barcode" required value="<?= $barcode; ?>">
                <input type="text" placeholder="Item Name" name="name" required value="<?= $name; ?>">
                <select name="ctg">
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
                <input type="number" placeholder="Item Quantity" name="qty" required value="<?= $qty; ?>">
                <input type="text" placeholder="Item Unit" name="unit" required value="<?= $unit; ?>">
                <input type="number" placeholder="Item Price" name="price" step="0.01" required value="<?= $price; ?>">
                <input type="date" name="expiry" required value="<?= $expiry; ?>">
                <button type="submit" name="save">Save</button>
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
    <div class="content-wrap">
        <a href="../pdf/out_stock.php" id="outOfstock">Print</a>
    </div>
    <div class="scroll-wrap" id="notifytb">
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
                for ($i = 0; $i < count($result); $i++) :
                    $object = $result[$i];
                    $id = $object->getObjectId();
                    $barcode = $object->get('Barcode');
                    $name = $object->get("productName");
                    $category = $object->get("productCategory");
                    $quantity = $object->get("productQuantity");
                ?>
                    <tr id="result">
                        <td><?php echo $barcode ?></td>
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
        </table>
    </div>
    <script src="../js/sidenav.js"></script>
</body>

</html>