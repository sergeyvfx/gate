<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $id;
db_connect (config_get ('check-database'));
$certificate = certificate_get_by_id($id);

include("../../../../../lib/MPDF54/mpdf.php");
$mpdf=new mPDF();
$mpdf->WriteHTML($certificate['template']);
$mpdf->Output();
?>