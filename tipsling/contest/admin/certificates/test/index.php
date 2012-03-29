<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../../../../globals.php';
include $DOCUMENT_ROOT . '/inc/include.php';
global $id;
db_connect (config_get ('check-database'));
$certificate = certificate_get_by_id($id);
include("../../../../../lib/MPDF54/mpdf.php");
$mpdf=new mPDF();
$mpdf->WriteHTML($certificate['template']);
$mpdf->Output($certificate['name'].'.pdf','D');
?>