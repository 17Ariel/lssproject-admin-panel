<?php
require_once "../dompdf/autoload.inc.php";
include('../config.php');

use Parse\ParseQuery;
use Dompdf\Dompdf;


$id = 0;
$name = '';
$qty = '';
$price = '';
$sbtotal = '';
$date = '';
$total = '';

$totals = new ParseQuery('Sales');
$result = $totals->aggregate($results = [
    'group' => [
        'objectId' => [
            'today' => ['$dayOfMonth' => '$createdAt'],
            'month' => ['$month' => '$createdAt'],
            'year' => ['$year' => '$createdAt']
        ],
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
$dates = '';
$today = date('j');
$month = date('F');
$year = date('Y');
$queries = new ParseQuery('Sales');
$queries->equalTo('day', $today);
$queries->equalTo('month', $month);
$queries->equalTo('year', $year);
$result = $queries->find();
$table .=
    '<table>
    <thead>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
            <th>Date</th>
        </tr>
    </thead>
<tbody>';
for ($i = 0; $i < count($result); $i++) :
    $object = $result[$i];
    $id = $object->getObjectId();
    $name = $object->get("productName");
    $quantity = $object->get("productQty");
    $price = $object->get('productPrice');
    $sbtotal = $object->get('subTotal');
    $date = $object->getCreatedAt();
    $dates = $date->format('F j,Y');
    $table .=
        '<tr id="result">
    <td>' . $name . '</td>
    <td>' . $quantity . '</td>
    <td>' . $price . '</td>
    <td>' . $sbtotal . '</td>
    <td>' . $dates . '</td>
</tr>';
endfor;
$table .=
    '</tbody>
</table>
<h3> Total:' . $total . '</h3>

';

ob_end_clean();
$dompdf = new Dompdf();
$dompdf->load_html($table);
$nameOffile = 'Daily Sales.pdf';
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();


$dompdf->stream($nameOffile, array("attachment" => false));
?>
<script>
    let table = document.querySelector("table"),
        sum = 0;
    for (let x = 1; x < table.rows.length; x++) {
        sum = sum + parseFloat(table.rows[x].cells[3].innerHTML);
    }
    document.querySelector("#totals").innerHTML = sum;
</script>