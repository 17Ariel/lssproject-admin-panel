<?php
include('../config.php');

use Parse\ParseQuery;
use Parse\ParseObject;
use Parse\ParseException;

$id = '';
$Qty = '';
$barcode = '';
$pname = '';
$qty = '';
$price = '';
$id = $_POST['id'];
$order = new ParseQuery('Order');
$order->equalTo('objectId', $id);
$results = $order->find();
for ($i = 0; $i < count($results); $i++) {
    $object = $results[$i];
    $id = $object->getObjectId();
    $barcode = $object->get('barcode');
    $pname = $object->get('productName');
    $price = $object->get('productPrice');
    $qty = $object->get('productQty');
}

$product = new ParseObject('Sales');
$product->set('Barcode', $barcode);
$product->set('productName', $pname);
$product->set('productPrice', floatval($price));
$product->set('productQty', intval($qty));
try {
    $product->save();
    echo 'New object created with objectId: ' . $product->getObjectId();
} catch (ParseException $ex) {
    echo 'Failed to create new object, with error message: ' . $ex->getMessage();
}





// if (($Qty) <= $qty) {
//     $response = 'Insufficient stock';
//     echo json_decode(($response));
// } else {
//     $response = 'Success';
//     echo json_decode(($response));
// }
