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
$status = "";
$users = "";
$address = "";
$orderNum = "";
$bcode = '';
$productname = '';
$productprice = '';
$productqty = '';
$sbtotal = '';
$newQty = '';
$shipping = '';


$querys = new ParseQuery('Order');
$querys->equalTo('scann', 'scanning');
$theResult = $querys->find();
for ($i = 0; $i < count($theResult); $i++) {
    $obj = $theResult[$i];
    $users = $obj->get('username');
    $address = $obj->get('address');
    $orderNum = $obj->get('orderNumber');
}


$month = date('F');
$year = date('Y');
$day = date('j');
$date = $month . ' ' . $day . ',' . $year;
if (isset($_GET['save'])) {
    $save = $_GET['save'];
    $query = new ParseQuery('Order');
    $sales = new ParseObject('Sales');
    $query->equalTo('objectId', $save);
    $result = $query->find();
    for ($i = 0; $i < count($result); $i++) {
        $object = $result[$i];
        $bcode = $object->get('barcode');
        $productname = $object->get('productName');
        $productprice = $object->get('productPrice');
        $productqty = $object->get('productQty');
        $shipping = $object->get('shippingFee');
    }
    $product = new ParseQuery('Product');
    $product->equalTo('Barcode', $bcode);
    $productres = $product->find();
    for ($y = 0; $y < count($productres); $y++) {
        $prodObj = $productres[$y];
        $Qty = $prodObj->get('productQuantity');
    }

    if (($Qty) <= $productqty) {
        $_SESSION['message'] = 'Insuficient Stock';
        $_SESSION['msg_type'] = 'danger';
        header('location:sales.php');
        exit();
    } else {
        $sbtotal = (floatval($productprice) * intval($productqty)) + floatval($shipping);
        $sales->set('Barcode', $bcode);
        $sales->set('productName', $productname);
        $sales->set('productPrice', floatval($productprice));
        $sales->set('shippingFee', floatval($shipping));
        $sales->set('productQty', intval($productqty));
        $sales->set('subTotal', $sbtotal);
        $sales->set('saleStatus', 'process');
        $sales->set('custdate', date_create($date));
        $sales->set('day', $day);
        $sales->set('month', $month);
        $sales->set('year', $year);
        $object->set('status', 'To pack');
        $newQty = intval($Qty) - intval($productqty);
        $prodObj->set('productQuantity', $newQty);
        try {
            $sales->save();
            $object->save();
            $prodObj->save();
            header('location:processOrder.php');
            exit();
        } catch (ParseException $ex) {
            echo 'error:' . $ex->getMessage();
        }
    }
}
$scann = '';
$stats = '';
if (isset($_GET['print'])) {
    $print = $_GET['print'];
    $order = new ParseQuery('Order');
    $order->equalTo('orderNumber', $print);
    $orderRes = $order->find();
    for ($x = 0; $x < count($orderRes); $x++) {
        $orderObj = $orderRes[$x];
        $scann = $orderObj->get('scann');
        $stats = $orderObj->get('status');
    }
    $product = new ParseQuery('Sales');
    $resultOf = $product->find();
    for ($i = 0; $i < count($resultOf); $i++) {
        $object = $resultOf[$i];
        try {
            $orderObj->set('status', 'To deliver');
            $orderObj->set('scann', 'scanned');
            $object->set('saleStatus', 'done');
            $object->save();
            $orderObj->save();
        } catch (ParseException $ex) {
            echo 'Failed to create new object, with error message: ' . $ex->getMessage();
        }
        header('location:processOrder.php');
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
            <a href="order.php" class="link">
                <i class="material-icons">shopping_cart</i>
                Orders
            </a>
            <a href="sales.php" class="link active">
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
            unset($_SESSION['message']); ?>
            <span>
                <i class="material-icons" id="alert-closer">close</i>
            </span>
        </div>
    <?php endif; ?>
    <form action="sales.php" method="post" id="resetform">
        <button type="button" id="print">
            Print Receipt
        </button>
        <div class="total-wrapper">
            <?php
            $query = new ParseQuery('Sales');
            $result = 0;
            $result = $query->aggregate($results = [
                'match' => [
                    'saleStatus' => ['$eq' => 'process']
                ],
                'group' => [
                    'objectId' => null,
                    'total' => ['$sum' => '$subTotal'],
                ]
            ]);

            foreach ($result as $res) {
                $res['total'];
                if ($res == 0) {
                    '<span>0.00</span>';
                } else {
                    $total = $res['total'];
                }
            }

            ?>
            <h4>Total Amount:
                <span id="totals">
                    <?php echo $total ?>
                </span>
            </h4>
        </div>
    </form>
    <div class="user-details">
        <h3>Username: <span id="users"><?php echo $users ?></span></h3>
        <p>Address: <span id="address"><?php echo $address ?></span></p>
    </div>
    <div class="scroll-wrap">
        <table>
            <thead>
                <th>Name</th>
                <th>Price</th>
                <th>Qty</th>
                <th>ShippingFee</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php
                $ship = '';
                $queries = new ParseQuery('Order');
                $queries->equalTo('status', 'To collect');
                $resulta = $queries->find();
                for ($i = 0; $i < count($resulta); $i++) :
                    $objects = $resulta[$i];
                    $ids = $objects->getObjectId();
                    $pname = $objects->get('productName');
                    $prices = $objects->get('productPrice');
                    $qtys = $objects->get('productQty');
                    $ship = $objects->get('shippingFee');
                ?>
                    <tr>
                        <td><?php echo $pname ?></td>
                        <td><?php echo $prices ?></td>
                        <td><?php echo $qtys ?></td>
                        <td><?php echo $ship ?></td>
                        <td>
                            <a href="processOrder.php?save=<?php echo $ids ?>" id="verify" name="verify">Verify</a>
                        </td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <div class="scroll-wrap">
        <table>
            <thead>
                <th>Name</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Shipping Fee</th>
                <th>Subtotal</th>
            </thead>
            <tbody id="tbody">
                <?php
                $ships = '';
                $query = new ParseQuery('Sales');
                $query->equalTo('saleStatus', 'process');
                $result = $query->find();
                for ($i = 0; $i < count($result); $i++) :
                    $object = $result[$i];
                    $id = $object->getObjectId();
                    $barcode = $object->get('Barcode');
                    $name = $object->get('productName');
                    $price = $object->get('productPrice');
                    $qty = $object->get('productQty');
                    $ships = $object->get('shippingFee');
                    $subtotal = $object->get('subTotal');
                ?>
                    <tr>
                        <td class="pname"><?php echo $name ?></td>
                        <td class="price"><?php echo $price ?></td>
                        <td class="qty"><?php echo $qty ?></td>
                        <td class="ships"><?php echo $ships ?></td>
                        <td class="sbtotal"><?php echo $subtotal ?></td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>


    <script src="../js/sidenav.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js" integrity="sha256-vIL0pZJsOKSz76KKVCyLxzkOT00vXs+Qz4fYRVMoDhw=" crossorigin="anonymous">
    </script>
    <script>
        const pdf = new jsPDF();
        let receipt = document.querySelector('#print');
        let customerPayment = document.querySelector('#custpayment');
        const username = document.querySelector('#users').innerText;
        const address = document.querySelector('#address').innerText
        const today = new Date();
        const months = today.getMonth() + 1;
        const day = today.getDate();
        const year = today.getFullYear();
        const dateToday = `${months}-${day}-${year}`;
        let total = document.querySelector('#totals').innerText;
        let tables = document.querySelector('#tbody').innerText;
        const customerReceipt = () => {
            pdf.text(10, 10, 'Laoac Super Store');
            pdf.text(10, 20, 'Brgy. Poblacion, Laoac, Pangasinan');

            pdf.text(10, 40, 'Official Receipt');

            pdf.text(10, 50, `Date: ${dateToday}`);
            pdf.text(10, 60, `Username: ${username}`);
            pdf.text(10, 70, `Address: ${address}`);

            pdf.text(10, 90, tables);
            pdf.text(10, 190, `Total Amount: ${total}`);
            pdf.save();
            window.location.replace('processOrder.php?print=<?php echo $orderNum; ?>');

        }
        receipt.addEventListener('click', customerReceipt);
    </script>
    <script src="../js/close.js"></script>
</body>

</html>