<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include '../../../../globals.php';
include $DOCUMENT_ROOT . '/inc/include.php';
global $param, $current_contest;

db_connect (config_get ('check-database'));
if ($param=='all')
    $sql="SELECT user.email as mail FROM user WHERE user.id <> 1 ";
else if ($param == 'contest')
    $sql="SELECT user.email as mail FROM responsible, user WHERE user.id<>1 AND user.id = responsible.user_id AND exists (select * from team where team.responsible_id=responsible.user_id and team.contest_id=".$current_contest.")";
$result = mysql_query($sql);
while($row = mysql_fetch_array($result))
{
    echo $row["mail"]."\n";
}

?>
