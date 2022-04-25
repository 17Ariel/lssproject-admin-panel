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
$custdate = date('F j,Y h:i:s a');
$year = date('Y');
$month = date('F');
$day = date('j');
if (isset($_POST['searchbtn'])) {
    $searchinput = $_POST['search'];
    $queries = new ParseQuery('Product');
    $queries->equalTo('Barcode', $searchinput);
    $searches = $queries->find();
    for ($i = 0; $i < count($searches); $i++) {
        $object = $searches[$i];
        $id = $object->getObjectId();
        $barcode = $object->get("Barcode");
        $name = $object->get("productName");
        $price = $object->get("productPrice");
    }
}
if (isset($_POST['btnsend'])) {
    $barcode = $_POST['barcode'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $qty = $_POST['qty'];

    $productQuery = new ParseQuery('Product');
    $productQuery->equalTo('Barcode', $barcode);
    $productResult = $productQuery->find();
    for ($x = 0; $x < count($productResult); $x++) {
        $productObject = $productResult[$x];
        $Qty = $productObject->get('productQuantity');
    }
    if (($Qty) <= $qty) {
        $_SESSION['message'] = 'Insuficient Stock';
        $_SESSION['msg_type'] = 'danger';
        header('location:processWalkin.php');
        exit();
    } else {
        $subtotal = floatval($price) * intval($qty);
        $sales = new ParseObject('Sales');
        $sales->set('Barcode', $barcode);
        $sales->set('productName', $name);
        $sales->set('productPrice', floatval($price));
        $sales->set('productQty', intval($qty));
        $sales->set('subTotal', $subtotal);
        $sales->set('saleStatus', 'processing');
        $sales->set('custdate', date_create($custdate));
        $sales->set('year', $year);
        $sales->set('month', $month);
        $sales->set('day', $day);
        $newQty = intval($Qty) - intval($qty);
        $productObject->set('productQuantity', $newQty);
        try {
            $productObject->save();
            $sales->save();
            header('location:processWalkin.php');
            exit();
        } catch (ParseException $ex) {
            echo 'error:' . $ex->getMessage();
        }
    }
}

if (isset($_POST['compute'])) {
    $customerPayment = $_POST['payment'];
    $totalOrder = $_POST['total'];
    $changes = floatval($customerPayment) - floatval($totalOrder);
    $_SESSION['cash'] = $customerPayment;
    $_SESSION['changes'] = $changes;
    header('location:processWalkin.php');
    exit();
}

if (isset($_GET['print'])) {
    $changes = 0;
    $product = new ParseQuery('Sales');
    $resultOf = $product->find();
    for ($i = 0; $i < count($resultOf); $i++) {
        $object = $resultOf[$i];
        try {
            $object->set('saleStatus', 'done');
            $object->save();
            // header('location:processWalkin.php');
            // exit();
        } catch (ParseException $ex) {
            echo 'Failed to create new object, with error message: ' . $ex->getMessage();
        }
        header('location:processWalkin.php');
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
        <form action="processWalkin.php" id="formsearch" method="post">
            <input type="search" name="search" id="inputsearch" value="" placeholder="type the barcode here..">
            <button type="submit" name="searchbtn" id="searchbtn">
                <i class="material-icons">qr_code_scanner</i>
            </button>
        </form>
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
    <form id="resetform">
        <button type="button" id="print">
            Print Receipt
        </button>
    </form>
    <div class="content-wrap">
        <form action="processWalkin.php" id="formres" method="post">
            <input type="text" name="barcode" placeholder="Barcode" required value="<?= $barcode ?>" readonly>
            <input type="text" name="name" placeholder="Name" required value="<?= $name ?>" readonly>
            <input type="number" name="price" placeholder="Price" required step="0.01" value="<?= $price ?>" readonly>
            <input type="number" name="qty" placeholder="Quantity" required>
            <button type="submit" name="btnsend">Submit</button>
        </form>
        <form action="processWalkin.php" id="formtransac" method="POST">
            <?php
            $query = new ParseQuery('Sales');
            $result = 0;
            $result = $query->aggregate($results = [
                'match' => [
                    'saleStatus' => ['$eq' => 'processing']
                ],
                'group' => [
                    'objectId' => null,
                    'total' => ['$sum' => '$subTotal'],
                ]
            ]);

            foreach ($result as $res) {
                $res['total'];
                if ($res == 0) {
                    $total = 0;
                } else {
                    $total = $res['total'];
                }
            }

            ?>
            <input type="number" name="total" value="<?= $total ?>" placeholder="Total" id="totals" readonly required>
            <input type="number" name="payment" placeholder="Customer's Payment" id="payments" required>
            <button type="submit" name="compute" id="compute">Submit</button>
            <label for="" id="customerChanges">
                Change:
                <?php
                if (isset($_SESSION['changes'])) :
                    echo $_SESSION['changes'];
                    unset($_SESSION['changes']);
                ?>
                <?php endif ?>
            </label>
        </form>
    </div>
    <table class="tbl">
        <thead>
            <th>Name</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th colspan="2" id="action">Action</th>
        </thead>
        <tbody id="tbody">
            <?php
            $query = new ParseQuery('Sales');
            $query->equalTo('saleStatus', 'processing');
            $result = $query->find();
            for ($i = 0; $i < count($result); $i++) :
                $object = $result[$i];
                $id = $object->getObjectId();
                $barcode = $object->get('Barcode');
                $name = $object->get('productName');
                $price = $object->get('productPrice');
                $qty = $object->get('productQty');
                $subtotal = $object->get('subTotal');
            ?>
                <tr>
                    <td class="pname"><?php echo $name ?></td>
                    <td class="price"><?php echo $price ?></td>
                    <td class="qty"><?php echo $qty ?></td>
                    <td class="sbtotal"><?php echo $subtotal ?></td>
                    <td id="btndel">
                        <form action="" method="get">
                            <input type="hidden" value="<?php echo $id; ?>" name="id">
                            <input type="hidden" value="<?php echo $barcode; ?>" name="barcodes">
                            <input type="hidden" value="<?php echo $qty; ?>" name="qtys">
                            <button type="submit" name="delete" id="delete">
                                <i class="material-icons" id="remove">remove</i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    <input type="hidden" id="custpayment" value="<?php
                                                    if (isset($_SESSION['cash'])) :
                                                        echo $_SESSION['cash'];
                                                        unset($_SESSION['cash']);
                                                    endif;
                                                    ?>">
    <script src="../js/sidenav.js"></script>
    <script src="../js/check.js"></script>
    <script src="../js/close.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js" integrity="sha256-vIL0pZJsOKSz76KKVCyLxzkOT00vXs+Qz4fYRVMoDhw=" crossorigin="anonymous">
    </script>
    <script>
        const pdf = new jsPDF();
        let receipt = document.querySelector('#print');
        let customerPayment = document.querySelector('#custpayment');
        let changes = document.querySelector('#customerChanges').innerText;
        const today = new Date();
        const months = today.getMonth() + 1;
        const day = today.getDate();
        const year = today.getFullYear();
        const dateToday = `${months}-${day}-${year}`;
        let total = document.querySelector('#totals');
        let tables = document.querySelector('#tbody').innerText;
        const customerReceipt = () => {
            pdf.text(10, 10, 'Laoac Super Store');
            pdf.text(10, 20, 'Brgy. Poblacion, Laoac, Pangasinan');

            pdf.text(10, 40, 'Official Receipt');

            pdf.text(10, 50, `Date: ${dateToday}`);

            pdf.text(10, 80, tables);
            pdf.text(10, 180, `Total Amount: ${total.value}`);
            pdf.text(10, 190, `Customer Payment: ${customerPayment.value}`);
            pdf.text(10, 200, `Customer Change: ${changes}`);
            pdf.save();
            window.location.replace('processWalkin.php?print');

        }
        receipt.addEventListener('click', customerReceipt);
    </script>
</body>

</html>