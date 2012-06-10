<?php
include '../../../../globals.php';
include $DOCUMENT_ROOT . '/inc/include.php';

db_connect (config_get ('check-database'));
$sql="SELECT user.email as mail FROM responsible, user WHERE user.id = responsible.user_id";
$result = mysql_query($sql);
while($row = mysql_fetch_array($result))
{
    echo $row["mail"]."\n";
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
