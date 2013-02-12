<?php
global $go, $types, $voits;

$user_votes = arr_from_query('SELECT vote.id uservote_id, vote.user_id, voit.id voit_id, voit.text, voit.idvoit FROM `user_vote` vote, `voit` where vote.voit_id = voit.id AND voit.`idvoit`='.$id.' AND  vote.`user_id`='.user_id()); 
if (!$go) {
    $poll = poll_get_by_id($id);
    $type = $poll['type'];
    $name = $poll['vopros'];
    if ($type=="1") { $input="radio"; }
    elseif ($type=="2") { $input="checkbox"; }

    echo 
    "<form action='.?action=vote&go=send&types=$type&id=$id' method='post'>
        <table class ='clear' width='100%'>
            <tr>
                <td valign=top>
                    <p align=center>
                        <span> $name </span>
                    </p>
                        <table>";

    $result=arr_from_query("SELECT * FROM voit WHERE idvoit='$id'");
    $i=0;
    while ($i<count($result))
    {
        $row = $result[$i];
        echo "<tr>
                <td>
                    <p><span>
                            <input type=$input"; 
        if ($type=="2")  
            echo " name=voits[$i]"; 
        else 
            echo " name=voits"; 
        
        foreach ($user_votes as $vote) {
            if ($vote['voit_id'] == $row['id'])
                echo ' checked="checked"';
        }
        
        echo " value=".$row['id']."><span>&nbsp;".$row['text']."</span></p>
                </td> </tr>";        
        $i++;
     }

     echo
        "<input type=hidden name=numsotv value=$numsotv>
            <tr>  
                <td>
                    <p align=center><input type=submit name=www value=Голосовать></p>
            </tr>
        </table>";
}   
if ($go=="send") {
    $result=mysql_query("SELECT * FROM voit WHERE id='$voits'");
    while ($row = mysql_fetch_array($result, MYSQL_NUM))
    {
        $ids=$row[3];
    }
    
    if ($types=="1") {
        $result=mysql_query("SELECT * FROM voit WHERE id=$voits");
        while ($row = mysql_fetch_array($result, MYSQL_NUM))
        {
            $nowgol=$row[2];
            $newgol=$nowgol+1;
            $ids=$row[3];
        }
        $id=$voits;
        
        if (count($user_votes)==0)
        {
            mysql_query("update voit set golos='$newgol' WHERE id='$id'") or die("Ошибка при изменении");
        }
        else
        {
            foreach ($user_votes as $vote) {
                mysql_query("DELETE FROM user_vote WHERE id = ".$vote['uservote_id']);
            }
        }
        mysql_query("INSERT INTO user_vote VALUES ('', ".user_id().", $voits)") or die("Ошибка при изменении");
        print 'Ваш голос учтен. Спасибо!<br/>';
        print '<a href="'.config_get ('document-root').'/">На главную</a>';
    }
    else {
        while ($numsotv!="-1") {
            if ($voits[$numsotv]) {
                $result=mysql_query("SELECT * FROM voit WHERE id='$voits[$numsotv]'");
                while ($row = mysql_fetch_array($result, MYSQL_NUM))
                {
                    $nowgol=$row[2];
                    $newgol=$nowgol+1;
                    $ids=$row[3];
                }
                $id=$voits[$numsotv];
                mysql_query("update voit set golos='$newgol' WHERE id='$id'") or die("Ошибка при изменении");

            }
            $numsotv--;
        }
        $ip=$REMOTE_ADDR;

        mysql_query("INSERT INTO ip VALUES ('', '$ip', '$ids')") or die("Ошибка при изменении");
    }
    $go=results;
}
    /*if ($go=="results") {
        echo "Результаты опроса ";
        $result=mysql_query("SELECT * FROM allvoits WHERE id='$id'");
        while ($row = mysql_fetch_array($result, MYSQL_NUM))
        {
            $name=$row[1];
        }
        $result=mysql_query("SELECT * FROM voit WHERE idvoit='$id' order by id desc");
        $count=0;
        echo "\"$name\"";
        while ($row = mysql_fetch_array($result, MYSQL_NUM))
        {
            $count+=$row[2];
        }
        $colvo=mysql_num_rows($result);

        $results=mysql_query("SELECT * FROM voit WHERE idvoit='$id' order by golos desc");
        $collor="668745";
        while ($row = mysql_fetch_array($results, MYSQL_NUM))
        {
            $col1=$row[2];
            $proc=(100/$count)*$col1;
            $proc2=100-$proc;
            echo "<table cellpadding=0 cellspacing=0 border=0 width=100%>
                      <tr>
                           <td height=7 bgcolor=#$collor width=$proc%></td>
                           <span style=font-size:9pt;>$row[1] </span>
                           <td height=7 width=$proc2%><span style=font-size:9pt;>&nbsp;$row[2] </span>
                      </tr>
                  </table>";
            $collor+=2549;
        }
        echo "<p align=\"center\"><span style=\"font-size:10pt;\"><font face=\"Verdana\"><br><A HREF=javascript:history.back(-1)>Вернуться</A>";
    } */   
?>
