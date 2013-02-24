<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if ($PHP_SELF != '') {
  print ('HACKERS?');
  die;
}

global $id, $current_contest;
formo('title=Просмотр результатов опроса;');
$poll = poll_get_by_id($id);

$name=$poll['vopros'];
$type=$poll['type'];

$sql = "SELECT  voit.text AS answer, 
               (SELECT count(*)
                FROM team
                WHERE team.contest_id = $current_contest
                      AND exists (SELECT * 
                                  FROM user_vote 
                                  WHERE user_vote.voit_id = voit.id 
                                    and user_vote.user_id = team.responsible_id)) AS 'count'
        FROM voit
            JOIN allvoits ON allvoits.id = voit.idvoit
        WHERE allvoits.id =$id
        GROUP BY voit.text
        ORDER BY voit.id";

$answers = mysql_query($sql);
$count=0;
while ($row = mysql_fetch_array($answers, MYSQL_ASSOC)) {
    $count+=$row['count'];
}

$answers = mysql_query($sql);
$collor="668745";
echo "Опрос: $name";
echo "<HR>";
while ($row = mysql_fetch_array($answers, MYSQL_ASSOC)) {
    $col1=$row['count'];
    $proc=(100/$count)*$col1;
    $proc2=100-$proc;
    echo "<table cellpadding=0 cellspacing=0 border=0 width=100%>
            <tr>
                <td height=7 bgcolor=#$collor width=$proc%></td>
                <span style=font-size:9pt;>".$row['answer']."</span>
                <td height=7 width=$proc2%><span style=font-size:9pt;>&nbsp;$col1 </span>
            </tr>
        </table> ";
    $collor+=2549;
}

?>
