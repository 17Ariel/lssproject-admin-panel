<?php
include('../../config.php');

use Parse\ParseObject;
use Parse\ParseQuery;
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


if (isset($_GET['send'])) {
    $send = $_GET['send'];
    $queries = new ParseQuery('Order');
    $queries->equalTo('orderNumber', $send);
    $result = $queries->find();
    for ($i = 0; $i < count($result); $i++) {
        $object = $result[$i];
        try {
            $object->set('status', 'To collect');
            $object->set('scann', 'scanning');
            $object->save();
        } catch (ParseException $ex) {
            echo 'Failed to create new object, with error message: ' . $ex->getMessage();
        }
        header('location:../processOrder.php');
    }
}
