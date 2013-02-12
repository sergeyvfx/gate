<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

global $id, $DOCUMENT_ROOT;
db_connect (config_get ('check-database'));
$certificate = certificate_get_by_id($id);

include($DOCUMENT_ROOT."/lib/MPDF54/mpdf.php");
$mpdf=new mPDF();
$mpdf->WriteHTML(eval_code('<?php global $DOCUMENT_ROOT; ?>'.$certificate['template']));
$mpdf->Output();
?>