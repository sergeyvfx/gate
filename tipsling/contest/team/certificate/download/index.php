<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../../../../globals.php';
include $DOCUMENT_ROOT . '/inc/include.php';
include("../../../../../lib/MPDF54/mpdf.php");

global $certificate, $param, $current_contest;
$param = str_replace("&quot;","\"",$param);
db_connect (config_get ('check-database'));
$c = certificate_get_by_id($certificate);
$template = certificate_get_html($certificate, $current_contest, $param);

$mpdf=new mPDF();
$mpdf->WriteHTML(eval_code('<?php global $DOCUMENT_ROOT; ?>'.$template));
$mpdf->Output($c['name'].'.pdf','D');
?>