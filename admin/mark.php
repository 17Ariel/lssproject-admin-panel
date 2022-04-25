

<?php
include('../config.php');

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
    $query = new ParseQuery('Order');
    $query->equalTo('orderNumber', $send);
    $result = $query->find();
    for ($i = 0; $i < count($result); $i++) {
        $obj = $result[$i];
        try {
            $obj->set('status', 'To review');
            $obj->save();
        } catch (ParseException $ex) {
            echo 'Failed to create new object, with error message: ' . $ex->getMessage();
        }
        $_SESSION['message'] = 'Transaction has been done';
        $_SESSION['msg_type'] = 'success';
        header('location:order.php');
    }
}
?>