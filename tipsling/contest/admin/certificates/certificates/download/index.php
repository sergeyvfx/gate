<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function GetSql($certificate, $team)
{
    if ($certificate==1)
    {
        $sql = 'SELECT DISTINCT
                    `team`.`pupil1_full_name` as pupil1,
                    `team`.`pupil2_full_name` as pupil2,
                    `team`.`pupil3_full_name` as pupil3,
                    `school`.`name` as school,
                    `city`.`name` as city,
                    `city_status`.`short_name` as city_status,
                    `region`.`name` as region,
                    `contest`.`name` as contest
                FROM
                    `team`,
                    `school`,
                    `region`,
                    `city`,
                    `city_status`,
                    `contest`,
                    `user`,
                    `responsible`
                WHERE
                    `team`.`responsible_id`=`user`.`id` AND
                    `responsible`.`user_id`=`user`.`id` AND
                    `school`.`id`=`responsible`.`school_id` AND
                    `city`.`region_id`=`region`.`id` AND
                    `city`.`status_id`=`city_status`.`id` AND
                    `school`.`city_id`=`city`.`id` AND
                    `team`.`contest_id`=`contest`.`id` AND
                    `team`.`id`='.$team;
    }   
    else if ($certificate==2)
    {
        $sql = 'SELECT DISTINCT
                    if (`team`.`grade`<12, concat(`team`.`grade`," класса "), "") as grade,
                    `team`.`number` as number,
                    `team`.`pupil1_full_name` as pupil1,
                    if (`team`.`pupil2_full_name`="","",concat(",\n",`team`.`pupil2_full_name`)) as pupil2,
                    if (`team`.`pupil3_full_name`="","",concat(",\n",`team`.`pupil3_full_name`)) as pupil3,
                    if (`team`.`grade`<12, concat("Руководитель команды:\n", `team`.`teacher_full_name`),"") as teacher,
                    `team`.`mark` as mark,
                    `team`.`common_place` as place,
                    `school`.`name` as school,
                    `city`.`name` as city,
                    `city_status`.`short_name` as city_status,
                    `region`.`name` as region,
                    `contest`.`name` as contest
                FROM
                    `team`,
                    `contest`,
                    `user`,
                    `responsible`,
                    `school`,
                    `city`,
                    `city_status`,
                    `region`
                WHERE
                    `team`.`responsible_id`=`user`.`id` AND
                    `responsible`.`user_id`=`user`.`id` AND
                    `team`.`contest_id`=`contest`.`id` AND
                    `responsible`.`school_id`=`school`.`id` AND
                    `school`.`city_id`=`city`.`id` AND
                    `city`.`status_id`=`city_status`.`id` AND
                    `city`.`region_id`=`region`.`id` AND
                    `team`.`id`='.$team;
    }   
    else if ($certificate==3)
    {   
        $sql = 'SELECT DISTINCT
                    `team`.`pupil1_full_name` as pupil1,
                    `team`.`pupil2_full_name` as pupil2,
                    `team`.`pupil3_full_name` as pupil3,
                    `school`.`name` as school,
                    `city`.`name` as city,
                    `city_status`.`short_name` as city_status,
                    `region`.`name` as region,
                    `team`.`place` as place,
                    `team`.`grade` as grade,
                    `team`.`mark` as mark,
                    `contest`.`name` as contest
                FROM    
                    `team`,
                    `contest`,
                    `user`,
                    `responsible`,
                    `school`,
                    `city`,
                    `city_status`,
                    `region`
                WHERE
                    `team`.`responsible_id`=`user`.`id` AND
                    `responsible`.`user_id`=`user`.`id` AND
                    `team`.`contest_id`=`contest`.`id` AND
                    `team`.`place`>0 AND `team`.`place`<4 AND
                    `responsible`.`school_id`=`school`.`id` AND
                    `school`.`city_id`=`city`.`id` AND
                    `city`.`status_id`=`city_status`.`id` AND
                    `city`.`region_id`=`region`.`id` AND
                    `team`.`id`='.$team;
    }   
    else if ($certificate==4)
    {
        $sql = 'SELECT DISTINCT
                    `team`.`grade` as grade,
                    `team`.`number` as number,
                    `team`.`pupil1_full_name` as pupil1,
                    if (`team`.`pupil2_full_name`="","",concat(",\n",`team`.`pupil2_full_name`)) as pupil2,
                    if (`team`.`pupil3_full_name`="","",concat(",\n",`team`.`pupil3_full_name`)) as pupil3,
                    `team`.`teacher_full_name` as teacher,
                    `team`.`place` as place,
                    `team`.`mark` as mark,
                    `school`.`name` as school,
                    `city`.`name` as city,
                    `city_status`.`short_name` as city_status,
                    `region`.`name` as region,
                    `contest`.`name` as contest
                FROM
                    `team`,
                    `contest`,
                    `user`,
                    `responsible`,
                    `school`,
                    `city`,
                    `city_status`,
                    `region`
                WHERE
                    `team`.`responsible_id`=`user`.`id` AND
                    `responsible`.`user_id`=`user`.`id` AND
                    `team`.`contest_id`=`contest`.`id` AND
                    `team`.`place`>0 AND `team`.`place`<4 AND
                    `responsible`.`school_id`=`school`.`id` AND
                    `school`.`city_id`=`city`.`id` AND
                    `city`.`status_id`=`city_status`.`id` AND
                    `city`.`region_id`=`region`.`id` AND
                    `team`.`id`='.$team;
    }   
    else if ($certificate==5)
    {
        $sql = 'SELECT DISTINCT
                    `team`.`teacher_full_name` as teacher,
                    `team`.`grade`,
                    `contest`.`name` as contest,
                    `school`.`name` as school,
                    `city`.`name` as city,
                    `city_status`.`short_name` as city_status,
                    `region`.`name` as region
                FROM
                    `team`,
                    `contest`,
                    `user`,
                    `responsible`,
                    `school`,
                    `city`,
                    `city_status`,
                    `region`
                WHERE
                    `team`.`responsible_id`=`user`.`id` AND
                    `responsible`.`user_id`=`user`.`id` AND
                    `team`.`contest_id`=`contest`.`id` AND
                    `responsible`.`school_id`=`school`.`id` AND
                    `school`.`city_id`=`city`.`id` AND
                    `city`.`status_id`=`city_status`.`id` AND
                    `city`.`region_id`=`region`.`id` AND
                    `team`.`id`='.$team;
    }
    else if ($certificate==6)
    {
        $sql = 'SELECT DISTINCT
                    `team`.`teacher_full_name` as teacher,
                    `team`.`grade`,
                    `contest`.`name` as contest,
                    `school`.`name` as school,
                    `city`.`name` as city,
                    `city_status`.`short_name` as city_status,
                    `region`.`name` as region
                FROM
                    `team`,
                    `contest`,
                    `user`,
                    `responsible`,
                    `school`,
                    `city`,
                    `city_status`,
                    `region`
                WHERE
                    `team`.`responsible_id`=`user`.`id` AND
                    `responsible`.`user_id`=`user`.`id` AND
                    `team`.`contest_id`=`contest`.`id` AND
                    `responsible`.`school_id`=`school`.`id` AND
                    `school`.`city_id`=`city`.`id` AND
                    `city`.`status_id`=`city_status`.`id` AND
                    `city`.`region_id`=`region`.`id` AND
                    `team`.`place`>0 AND `team`.`place`<4 AND
                    `team`.`id`='.$team;
    }    
    return $sql;
}

function GetHTML($c, $t)
{
    $template = $c['template'];
    $matchearray = array();
    preg_match_all("/#\w+#/", $template, $matchearray);
    $count = count($matchearray[0]);
    $k=0;
    while ($k<$count)
    {
        $match = substr($matchearray[0][$k], 1, count($matchearray[0][$k])-2);
        $template = str_replace($matchearray[0][$k], $t[$match], $template);
        $k++;
    }  
    return $template;
}

include '../../../../../../globals.php';
include $DOCUMENT_ROOT . '/inc/include.php';

global $params;
db_connect (config_get ('check-database'));
include($DOCUMENT_ROOT."/lib/MPDF54/mpdf.php");
$mpdf=new mPDF();


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
    $certificate = $tmp[1];
    
    $c = certificate_get_by_id($certificate);
    if ($c==false)
        return;

    $sql=GetSql($certificate, $team);
    $t = db_row(db_query($sql));

    if ($t==false)
        return;
    
    if ($certificate==3||$certificate==4)
    {
        if ($t['place']==1)
            $t['place'] = I;
        else if ($t['place']==2)
            $t['place'] = II;
        else if ($t['place']==3)
            $t['place'] = III;
    }
    
    if ($certificate==5||$certificate==6)
    {   
        $teachers = split('[,]', $t['teacher']);
        $teachers_count = count($teachers);
        $j = 0;
        while ($j<$teachers_count)
        {
            $t['teacher'] = ($teachers[$j]);
            $html .= GetHTML($c, $t);
            $j++;
        }
    }
    else if ($certificate==1||$certificate==3)
    {
        $t['pupil'] = $t['pupil1'];
        $html .= GetHTML($c, $t);
        if ($t['pupil2']!="")
        {
            $t['pupil'] = $t['pupil2'];
            $html .= GetHTML($c, $t);
        }
        if ($t['pupil3']!="")
        {
            $t['pupil'] = $t['pupil3'];
            $html .= GetHTML($c, $t);
        }
    }
    else
    {
        $html .= GetHTML($c, $t);    
    }
    $i++;
}

$mpdf->WriteHTML($html);
$mpdf->Output('sertificates.pdf','D');
    
?>