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
        $tbody .=
            '<tr>
            <td>' . $barcode . '</td>
            <td>' . $name . '</td>
            <td>' . $category . '</td>
            <td>' . $quantity . '</td>
            <td>
                <a class="add" href="add.php?add=' . $id . '">
                    <i class="material-icons">add</i>
                </a>
            </td>
        </tr>';
    }
    echo $tbody;
}
