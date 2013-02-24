<?php

/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */
if ($PHP_SELF != '') {
  print ('HACKERS?');
  die;
}

formo('title=Инфорация о командах и их ответственных;');

$query = 'SELECT 
    concat(team.grade,".",team.number) as "Номер команды", 
    timezone.offset as "Часовой пояс",    
    team.smena as "Смена",
    team.teacher_full_name as "ФИО учителя",
    concat(user.surname, " ", user.name, " ", user.patronymic) as "ФИО ответственного",
    user.email as "email ответственного", 
    user.phone as "Телефон ответственного",
    school.name as "Учебное заведение"
FROM user, team, responsible, school, timezone
WHERE team.responsible_id = user.id
AND responsible.user_id = user.id
AND responsible.school_id = school.id
AND school.timezone_id = timezone.id
AND team.contest_id ='.$current_contest.
' ORDER BY team.grade ASC, team.number
LIMIT 0 , 150';

$list = arr_from_query($query);
if (count($list) > 0) {
  global $page;

  $perPage = 150;
  
  $pages = new CVCPagintation ();
  $pages->Init('', 'bottomPages=false;skiponcepage=true;');
  $i = 0;
  $n = count($list);

  if ($page != '') {
    $pageid = '&page=' . $page;
  }

  while ($i < $n) {
    $c = 0;
    //$pageSrc = '<div>'.$query.'</div>';
    $pageSrc = '<table class="list">' . "\n";
    $pageSrc .= '<tr class="h"><th width="100" style="text-align: center;">Номер команды</th>
        <th style="text-align: center;">Часовой пояс</th>
        <th style="text-align: center;">Смена</th>
        <th style="text-align: center;">ФИО учителя</th>
        <th style="text-align: center;">ФИО ответственного</th>
        <th style="text-align: center;">email ответственного</th>
        <th style="text-align: center;">Телефон ответственного</th>
        <th style="text-align: center;">Учебное заведение</th>
        </tr>' . "\n";
    while ($c < $perPage && $i < $n) {
      $it = $list[$i];
      $pageSrc .= 
      '<td class="center">' . $it["Номер команды"] . '</td>' .
      '<td class="center">' . ($it["Часовой пояс"]<0?$it["Часовой пояс"]:'+'. $it["Часовой пояс"]) . '</td>' .
      '<td class="center">' . $it["Смена"] . '</td>' .
      '<td align="center">' . $it["ФИО учителя"] . '</td>' .
      '<td align="center">' . $it["ФИО ответственного"] . '</td>' .
      '<td align="center">' . $it["email ответственного"] . '</td>' .
      '<td align="center">' . $it["Телефон ответственного"] . '</td>' .
      '<td align="center">' . $it["Учебное заведение"] . '</td>' .
      '</tr>' . "\n";
      $c++;
      $i++;
    }
    $pageSrc .= '</table>' . "\n";
    $pages->AppendPage($pageSrc);
  }
  $pages->Draw();
} else {
  info('Нет команд.');
}

formc ();
?>
