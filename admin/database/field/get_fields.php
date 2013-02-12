<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../../globals.php';
include $DOCUMENT_ROOT . '/inc/include.php';
db_connect (config_get ('check-database'));

global $table;

$table_name = db_field_value("visible_table", "table", "`id`=".$table);
$query = "SHOW COLUMNS from `".$table_name."`";
$result = mysql_query($query);
while($row = mysql_fetch_array($result))
{
    echo ('<option value='.$row['Field'].'>'.$row['Field'].'</option>');
}
?>
