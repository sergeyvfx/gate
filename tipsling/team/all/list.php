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

formo('title=Список зарегистрированных команд;');

if (count($list) > 0) {
  global $page;

  $perPage = opt_get('user_count');
  if ($perPage <= 0) {
    $perpage = 10;
  }

  $pages = new CVCPagintation ();
  $pages->Init('', 'bottomPages=false;skiponcepage=true;');
  $i = 0;
  $n = count($list);

  if ($page != '') {
    $pageid = '&page=' . $page;
  }

  while ($i < $n) {
    $c = 0;
    $pageSrc = '<table class="list">' . "\n";
    $pageSrc .= '<tr class="h"><th width="7%" align="center">Номер команды</th>
        <th width="15%">Учебное заведение</th>
        <th width="13%">Регион</th>
        <th width="10%">Населенный пункт</th>
        <th width="15%">Учитель</th>
        <th width="25%">Участники</th>
        <th>Статус платежа</th>';//<th width="48" class="last">&nbsp;</th></tr>' . "\n";

    while ($c < $perPage && $i < $n) {
      $it = $list[$i];
      //TODO Check is contest running or archive
      $d = 1;
      $ps = $it['is_payment'];
      $r = responsible_get_by_id($it['responsible_id']);
      $s = school_get_by_id($r['school_id']);
      $number = $it['number'];
      $school_name = $s['name'];
      $region = school_get_region_name($r['school_id']);
      $city = school_get_city_name($r['school_id']);
      $teacher = $it['teacher_full_name'];
      $pupils = $it['pupil1_full_name'] .
                (($it['pupil2_full_name'] == '') ? ('') : (', ' . $it['pupil2_full_name'])) .
                (($it['pupil3_full_name'] == '') ? ('') : (', ' . $it['pupil3_full_name']));
      $payment = (($ps) ? ('<span style="color: green">Подтвержден</span>') : ('<span style="color: red">Не подтвержден</span>'));

      $pageSrc .= '<tr' . (($i == $n - 1 || $c == $perPage - 1) ? (' class="last"') : ('')) . '>' .
      '<td class="n">' . $number . '</td>' .
      '<td>' . $school_name . '</td>' .
      '<td>' . $region . '</td>' .
      '<td>' . $city . '</td>' .
      '<td>' . $teacher . '</td>' .
      '<td>' . $pupils . '</td>' .
      '<td>' . $payment . '</td>' .
      '</tr>' . "\n";
      $c++;
      $i++;
    }
    $pageSrc .= '</table>' . "\n";
    $pages->AppendPage($pageSrc);
  }
  $pages->Draw();
} else {
  info('Пока что ни одна команда не зарегистрировалась.');
}

formc ();
?>
