<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../../../../../globals.php';
include $DOCUMENT_ROOT . '/inc/include.php';

global $params, $current_contest;
db_connect (config_get ('check-database'));
include($DOCUMENT_ROOT."/lib/MPDF54/mpdf.php");

$params_array = split('[;]', $params);
$n = count($params_array);
$i=0;
$html='';
while ($i<$n)
{
    $param = $params_array[$i];
    if ($param=="")
    {
        $i++;
        continue;
    }
    
    $tmp = split('[.]', $param);
    $team = $tmp[0];
    $cert_id = $tmp[1];
    $cert = certificate_get_by_id($cert_id);
    if ($cert==false)
        return;

    $sql = certificate_get_sql($cert_id, $current_contest, '`team`.`id`='.$team, false);
    $result = db_query($sql);
    $for = $cert['for'];
    
    while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    {
        $cert_limit = '';
        preg_match_all("/#[^#]+#/", $for, $matchesarray);
        $array = $matchesarray[0];
        foreach ($array as $value) 
        {
            echo('test4');
            $caption = substr($value, 1, strlen($value)-2);
            $for_copy = str_replace($value, $rows[$caption], $for_copy);
            $visible_field = visible_field_get_by_caption($caption);
            $field = $visible_field['field'];
            $table = db_field_value("visible_table", "table", "`id`=".$visible_field['table_id']);
            $cert_limit .= '`'.$table.'`.`'.$field.'`=\''.$rows[$caption].'\' AND ';
        }
        $cert_limit = substr($cert_limit, 0, strlen($cert_limit) - 5);
        $html .= certificate_get_html($cert_id, $current_contest, $cert_limit);
    }
    $i++;
}

$mpdf=new mPDF();
$mpdf->WriteHTML(eval_code('<?php global $DOCUMENT_ROOT; ?>'.$html));
$mpdf->Output('sertificates.pdf','D');
    
?>