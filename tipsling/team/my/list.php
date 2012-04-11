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

formo('title=Список моих команд;');

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
    $pageSrc .= '<tr class="h">
        <th width="7%" align="center">Номер команды</th>
        <th width="15%">Учитель</th>
        <th width="15%">Участник 1</th>
        <th width="15%">Участник 2</th>
        <th width="15%">Участник 3</th>
        <th width="10%">Статус платежа</th>
        <th width="15%">Конкурс</th>
        <th width="10%">Статус платежа</th>
        <th width="48" class="last">&nbsp;</th></tr>' . "\n";

    while ($c < $perPage && $i < $n) {
      $it = $list[$i];
      $ps = $it['is_payment'];
      $d = !$ps;
      $contest_stat = get_contest_status($it['contest_id']);
      $allow_edit = $contest_stat==1 || $contest_stat==2;
      $contest_name = contest_get_by_id($it['contest_id']);
      $pageSrc .= '<tr' . (($i == $n - 1 || $c == $perPage - 1) ? (' class="last"') : ('')) . '>' .
      '<td class="n">' . (($allow_edit) ? ('<a href=".?action=edit&id=' . $it['id'] . '&' . $pageid . '">') : ('')).$it['grade'].'.'. $it['number'] . (($allow_edit) ? ('</a>') : ('')) . '</td>' .
      '<td>' . $it['teacher_full_name'] . '</td><td>' . $it['pupil1_full_name'] . '</td>' .
      '<td>' . $it['pupil2_full_name'] . '<td>' . $it['pupil3_full_name'] . '</td>' .
      '<td>' . $contest_name['name'] . '</td>'.
      '<td>' . (($ps) ? ('<span style="color: green">Подтвержден</span>') : ('<span style="color: red">Не подтвержден</span>')) . '</td>' .
      '<td align="right">' .
        stencil_ibtnav(($allow_edit) ? 'edit.gif' : 'edit_d.gif', ($allow_edit) ? '?action=edit&id=' . $it['id'] . '&' . $pageid : '', ($allow_edit) ? 'Изменить информацию о команде' : '') .
        stencil_ibtnav(($d && $allow_edit) ? ('cross.gif') : ('cross_d.gif'), ($d && $allow_edit) ? ('?action=delete&id=' . $it['id'] . '&' . $pageid) : (''), ($d && $allow_edit) ? 'Удалить команду' : '', 'Удалить эту команду?') .
      '</td></tr>' . "\n";
      $c++;
      $i++;
    }
    $pageSrc .= '</table>' . "\n";
    $pages->AppendPage($pageSrc);
  }
  $pages->Draw();
} else {
  info('У Вас пока что нет команд. Но Вы можете их добавить.');
}

formc ();
?>
