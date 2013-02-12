<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../../../../globals.php';
include $DOCUMENT_ROOT . '/inc/include.php';
db_connect (config_get ('check-database'));

global $field;
$array = preg_split("/\./", $field);
$table = $array[0];
$field = $array[1];

echo $table;
echo $field;

$sql="SELECT DISTINCT ".$field." FROM ".$table." ORDER BY ".$field;
$result = mysql_query($sql);
while($row = mysql_fetch_array($result))
{
    echo '<option value="'.$row[$field].'">'.$row[$field].'</option>\n';
}

?>
