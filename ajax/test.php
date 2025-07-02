<?php
require '../vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml('<h1>Hello, World!</h1>');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

file_put_contents('../uploads/certificates/test.pdf', $dompdf->output());

echo 'PDF generated!';
?>
