
<?php
require_once "../dompdf/autoload.inc.php";
include('../config.php');

use Parse\ParseQuery;
use Dompdf\Dompdf;


$id = 0;
$name = '';
$qty = '';
$date = '';
$users = '';
$dates = '';

$querys = new ParseQuery('Arrived');
$result = $querys->find();
$table .=
    '<table>
    <thead>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Date</th>
            <th>Users</th>
        </tr>
    </thead>
<tbody>';
for ($i = 0; $i < count($result); $i++) :
    $object = $result[$i];
    $id = $object->getObjectId();
    $name = $object->get("productName");
    $quantity = $object->get("productQty");
    $date = $object->getCreatedAt();
    $dates = $date->format('F j,Y');
    $users = $object->get("user");
    $table .=
        '<tr id="result">
    <td>' . $name . '</td>
    <td>' . $quantity . '</td>
    <td>' . $dates . '</td>
    <td>' . $users . '</td>
</tr>';
endfor;
$table .=
    '</tbody>
</table>';



ob_end_clean();
$dompdf = new Dompdf();
$dompdf->load_html($table);
$nameOffile = 'Out_of_stock.pdf';
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();


$dompdf->stream($nameOffile, array("attachment" => false));
?>