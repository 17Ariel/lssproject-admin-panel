<?php
include('../config.php');

use Parse\ParseQuery;
use Parse\ParseObject;
use Parse\ParseException;

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

$id = 0;
$transactiondate = '';
$fullname = '';
$contact = '';
$productName = '';
$productPrice = '';
$productQty = '';
$refundReason = '';
$quantity = '';
$updatedQty = '';
$updatedSubtotal = '';
$subTotal = '';
$price = '';




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
if (isset($_POST['save'])) {
    $transactiondate = $_POST['tdate'];
    $fullname = $_POST['name'];
    $contact = $_POST['contact'];
    $productName = $_POST['pname'];
    $productPrice = $_POST['price'];
    $productQty = $_POST['qty'];
    $refundReason = $_POST['reason'];
    $subtotal = intval($productQty) * floatval($productPrice);
    $refundObject = new ParseObject('Refund');
    $refundObject->set('transactionDate', date_create($transactiondate));
    $refundObject->set('fullname', $fullname);
    $refundObject->set('contact', $contact);
    $refundObject->set('productName', $productName);
    $refundObject->set('productPrice', floatval($productPrice));
    $refundObject->set('productQty', intval($productQty));
    $refundObject->set('subTotal', $subtotal);
    $refundObject->set('reason', $refundReason);
    $sale = new ParseQuery('Sales');
    $sale->equalTo('productName', $productName);
    $saleResult = $sale->find();
    for ($i = 0; $i < count($saleResult); $i++) {
        $obj = $saleResult[$i];
        $quantity = $obj->get('productQty');
        $price = $obj->get('productPrice');
        $subTotal = $obj->get('subTotal');
    }
    if ($price != $productPrice) {
        $_SESSION['message'] = 'The product price does not much the price of the product';
        $_SESSION['msg_type'] = 'warning';
        header('location:refund.php');
        exit();
    } else {
        if ($quantity < $productQty) {
            $_SESSION['message'] = 'Cannot be refund, quantity is greater than expected quantity';
            $_SESSION['msg_type'] = 'warning';
            header('location:refund.php');
            exit();
        } elseif ($quantity == $productQty) {
            try {
                $obj->destroy();
                $obj->save();
                $refundObject->save();
                echo 'New object created with objectId: ' . $refundObject->getObjectId();
                $_SESSION['message'] = 'Record has been saved';
                $_SESSION['msg_type'] = 'success';
                header('location:refund.php');
                exit();
            } catch (ParseException $ex) {
                echo 'Err' . $ex->getMessage();
            }
        } elseif ($quantity > $productQty) {
            $updatedQty = intval($quantity) - intval($productQty);
            $updatedSubtotal = floatval($subTotal) - floatval($subtotal);
            try {
                $obj->set('productQty', $updatedQty);
                $obj->set('subTotal', $updatedSubtotal);
                $obj->save();
                $refundObject->save();
                echo 'New object created with objectId: ' . $refundObject->getObjectId();
                $_SESSION['message'] = 'Record has been saved';
                $_SESSION['msg_type'] = 'success';
                header('location:refund.php');
                exit();
            } catch (ParseException $ex) {
                echo 'Err' . $ex->getMessage();
            }
        } else {
            $_SESSION['message'] = 'Transaction does not exist';
            $_SESSION['msg_type'] = 'danger';
            header('location:refund.php');
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
    <title>Refund</title>
    <link rel="shortcut icon" href="../logo/logo_top.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/refund.css">
</head>

<body>
    <div class="modal-admin">
        <form action="refund.php" method="post">
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
    <div class="modal">
        <form action="refund.php" method="post">
            <div class="header">
                <span>
                    <i class="material-icons" id="closeMod">close</i>
                </span>
                <h1>New Refund</h1>
            </div>
            <div class="body">
                <input type="date" placeholder="Transaction Date" name="tdate" id="tdate" required>
                <input type="text" placeholder="Customer Fullname" name="name" id="name" required>
                <input type="tel" placeholder="Contact Number" name="contact" id="contact" required>
                <input type="text" placeholder="Product Name" name="pname" id="pname" required>
                <input type="number" placeholder="Product Price" name="price" id="price" required>
                <input type="number" placeholder="Product Quantity" name="qty" id="qty" required>
                <textarea name="reason" id="reason" cols="30" rows="10" placeholder="Details/Reason"></textarea>
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
            <a href="stocks.php" class="link">
                <i class=" material-icons">warehouse</i>
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
            <a href="refund.php" class="link active">
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
            unset($_SESSION['message']);
            ?>
            <span>
                <i class="material-icons" id="alert-closer">close</i>
            </span>
        </div>
    <?php endif; ?>
    <div class="content-wrap">
        <button type="button" id="btnadd">
            <i class="material-icons">add</i>
            <span>
                New
            </span>
        </button>
    </div>
    <div class="scroll-wrap">
        <table>
            <thead>
                <th>TransactionDate</th>
                <th>CustomerName</th>
                <th>Contact</th>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Reason</th>
            </thead>
            <tbody>
                <?php
                $tdate = '';
                $subtotal = '';
                $refundQuery = new ParseQuery('Refund');
                $refundQuery->descending('createdAt');
                $refundResult = $refundQuery->find();
                for ($i = 0; $i < count($refundResult); $i++) :
                    $object = $refundResult[$i];
                    $id = $object->getObjectId();
                    $transactiondate = $object->get('transactionDate');
                    $tdate = $transactiondate->format('F j,Y');
                    $fullname = $object->get("fullname");
                    $contact = $object->get("contact");
                    $productName = $object->get("productName");
                    $productPrice = $object->get("productPrice");
                    $productQty = $object->get("productQty");
                    $subtotal = $object->get('subTotal');
                    $refundReason = $object->get("reason");
                ?>
                    <tr>
                        <td><?php echo $tdate ?></td>
                        <td><?php echo $fullname ?></td>
                        <td><?php echo $contact ?></td>
                        <td><?php echo $productName ?></td>
                        <td><?php echo $productPrice ?></td>
                        <td><?php echo $productQty ?></td>
                        <td><?php echo $subtotal ?></td>
                        <td><?php echo $refundReason ?></td>
                    </tr>
                <?php endfor ?>
            </tbody>
        </table>
    </div>
    <script src="../js/sidenav.js"></script>
    <script src="../js/refund-modal.js"></script>
    <script src="../js/admin.js"></script>
    <script src="../js/close.js"></script>
    </script>
</body>

</html>