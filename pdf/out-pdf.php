
<?php
require_once "../dompdf/autoload.inc.php";
include('../config.php');

use Parse\ParseQuery;
use Dompdf\Dompdf;


$id = 0;
$name = '';
$qty = '';
$date = '';
$total = '';

$dates = '';
$year = date('Y');
$queries = new ParseQuery('Sales');
$queries->descending('createdAt');
$results = $queries->find();
$table .=
    '<table>
    <thead>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Date</th>
        </tr>
    </thead>
<tbody>';
for ($i = 0; $i < count($results); $i++) :
    $object = $results[$i];
    $id = $object->getObjectId();
    $name = $object->get("productName");
    $quantity = $object->get("productQty");
    $date = $object->getCreatedAt();
    $dates = $date->format('F j,Y');
    $table .=
        '<tr id="result">
    <td>' . $name . '</td>
    <td>' . $quantity . '</td>
    <td>' . $dates . '</td>
</tr>';
endfor;
$table .=
    '</tbody>
</table>';



ob_end_clean();
$dompdf = new Dompdf();
$dompdf->load_html($table);
$nameOffile = 'Product_out.pdf';
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();


$dompdf->stream($nameOffile, array("attachment" => false));
?>