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

formo('title=Список моих конкурсов;');

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
    $pageSrc .= '<tr class="h"><th width="150" style="text-align: center;">Название</th>
        <th style="text-align: center;">Начало регистрации</th>
        <th style="text-align: center;">Конец регистрации</th>
        <th style="text-align: center;">Начало конкурса</th>
        <th style="text-align: center;">Конец конкурса</th>
        <th style="text-align: center;">Дата добавления в архив</th>
        <th style="text-align: center;">Статус</th>
        <th width="48" class="last">&nbsp;</th></tr>' . "\n";

    
    while ($c < $perPage && $i < $n) {
      $it = $list[$i];
      //TODO Check is contest running or archive
      $name = $it['name'];
      $r_s =  $it['registration_start'];
      $r_f =  $it['registration_finish'];
      $c_s =  $it['contest_start'];
      $c_f =  $it['contest_finish'];
      $s_to_a =  $it['send_to_archive'];
      
      $d = $it['date_arrival'] == null ? (1) : (0);
      $amount = $it['amount'];
      if (!preg_match('/\./', $amount)) {
        $amount = $amount . '.00';
      }
      $amount = $amount . ' руб.';
      $pageSrc .= '<tr' . (($i == $n - 1 || $c == $perPage - 1) ? (' class="last"') : ('')) . '>' .
      '<td class="n"><a href=".?action=edit&id=' . $it['id'] . '">' . $name . '</td>' .
      '<td align="center">' . $r_s . '</td>' .
      '<td align="center">' . $r_f . '</td>' .
      '<td align="center">' . $c_s . '</td>' .
      '<td align="center">' . $c_f . '</td>' .
      '<td align="center">' . $s_to_a . '</td>' .
      '<td align="center">' . get_contest_status($it['id']) . '</td>' .
      '<td align="right">' .
        stencil_ibtnav((($d) ? 'edit.gif' : 'edit_d.gif'), (($d) ? '?action=edit&id=' . $it['id'] . '&' . $pageid : ''), 'Изменить информацию о конкурсе') .
        stencil_ibtnav((($d) ? 'cross.gif' : 'cross_d.gif'), (($d) ? '?action=delete&id=' . $it['id'] . '&' . $pageid : ''), 'Удалить конкурс', 'Удалить этот конкурс?') .
      '</td></tr>' . "\n";
      $c++;
      $i++;
    }
    $pageSrc .= '</table>' . "\n";
    $pages->AppendPage($pageSrc);
  }
  $pages->Draw();
} else {
  info('Ошибка! У вас нет конкурсов');
}

formc ();
?>
