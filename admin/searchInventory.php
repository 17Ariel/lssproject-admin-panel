<?php
include('../config.php');

use Parse\ParseQuery;

$tbody = '';
$names = new ParseQuery('Product');
$names->startsWith('productName', $_POST['search']);

$barcodes = new ParseQuery('Product');
$barcodes->startsWith('Barcode', $_POST['search']);

$ctg = new ParseQuery('Product');
$ctg->startsWith('productCategory', $_POST['search']);

$query = ParseQuery::orQueries([$names, $barcodes, $ctg]);
$query->descending("createdAt");

$result = $query->find();
if ($result == null) {
    echo 'No result found';
} else {
    for ($i = 0; $i < count($result); $i++) {
        $object = $result[$i];
        $id = $object->getObjectId();
        $barcode = $object->get('Barcode');
        $name = $object->get("productName");
        $category = $object->get("productCategory");
        $quantity = $object->get("productQuantity");
        $unit = $object->get("productUnit");
        $price = $object->get("productPrice");
        $expiry = $object->get("expiryDate");
        $tbody .=
            '<tr>
            <td>' . $barcode . '</td>
            <td>' . $name . '</td>
            <td>' . $category . '</td>
            <td>' . $quantity . '</td>
            <td>' . $unit . '</td>
            <td>' . $price . '</td>
            <td>' . $expiry . '</td>
            <td>
                <a id="edit" href="update.php?edit=' . $id . '">Edit</a>
            </td>
            <td>
                <a id="delete" href="inventory.php?delete=' . $id . '">Delete</a>
            </td>
        </tr>';
    }
    echo $tbody;
}
