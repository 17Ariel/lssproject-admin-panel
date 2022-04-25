<?php
include('../../config.php');

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

if (isset($_GET['process'])) {
    $setProcess = $_GET['process'];
    $process = new ParseQuery('Order');
    $process->equalTo('orderNumber', $setProcess);
    $processStatus = $process->find();
    for ($i = 0; $i < count($processStatus); $i++) {
        $newProcess = $processStatus[$i];
        try {
            $newProcess->set('status', 'On process');
            $newProcess->save();
        } catch (ParseException $ex) {
            echo 'Failed to create new object, with error message: ' . $ex->getMessage();
        }
        header('location:../order.php');
    }
}
