
<?php
require_once "../dompdf/autoload.inc.php";
include('../config.php');

use Parse\ParseQuery;
use Dompdf\Dompdf;


$id = 0;
$username = '';
$orderNumber = '';
$totalAmount = '';
$initialTime = '';
$finalTime = '';

$order = new ParseQuery('Order');
$count = $order->count();

$orderQuery = new ParseQuery('Order');
$orderResult = $orderQuery->find();
$table .=
    '<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>OrderNumber</th>
            <th>TotalAmount</th>
            <th>Date</th>
        </tr>
    </thead>
<tbody>';
for ($i = 0; $i < count($orderResult); $i++) :
    $objectOrder = $orderResult[$i];
    $id = $objectOrder->getObjectId();
    $username = $objectOrder->get('username');
    $orderNumber = $objectOrder->get("orderNumber");
    $totalAmount = $objectOrder->get("totalPayment");
    $initialTime = $objectOrder->getUpdatedAt();
    $finalTime = $initialTime->format('F j,Y');
    $table .=
        '<tr id="result">
    <td>' . $username . '</td>
    <td>' . $orderNumber . '</td>
    <td>' . $totalAmount . '</td>
    <td>' . $finalTime . '</td>
</tr>';
endfor;
$table .=
    '</tbody>
</table>
<h3> Total Orders:' . $count . '</h3>';

ob_end_clean();
$dompdf = new Dompdf();
$dompdf->load_html($table);
$nameOffile = 'OrderList.pdf';
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();


$dompdf->stream($nameOffile, array("attachment" => false));
?>