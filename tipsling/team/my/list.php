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
    $pageSrc .= '<tr class="h"><th width="5%" align="center">Номер команды</th>
        <th width="10%">Учитель</th>
        <th width="10%">Ученик 1</th>
        <th width="10%">Ученик 2</th>
        <th width="15%">Ученик 3</th>
        <th>Статус платежа</th><th width="48" class="last">&nbsp;</th></tr>' . "\n";

    while ($c < $perPage && $i < $n) {
      $it = $list[$i];
      //TODO Check is contest running or archive
      $d = 1;
      $pageSrc .= '<tr' . (($i == $n - 1 || $c == $perPage - 1) ? (' class="last"') : ('')) . '>' .
      '<td class="n"><a href=".?action=edit&id=' . $it['id'] . '&' . $pageid . '">' . $it['number'] . '</a></td>' .
      '<td>' . $it['teacher_full_name'] . '</td><td>' . $it['pupil1_full_name'] . '</td>' .
      '<td>' . $it['pupil2_full_name'] . '<td>' . $it['pupil3_full_name'] . '</td><td>' . $it['is_payment'] . '</td>' .
      '<td align="right">' .
        stencil_ibtnav('edit.gif', '?action=edit&id=' . $it['id'] . '&' . $pageid, 'Изменить информацию о команде') .
        stencil_ibtnav(($d) ? ('cross.gif') : ('cross_d.gif'), ($d) ? ('?action=delete&id=' . $it['id'] . '&' . $pageid) : (''), 'Удалить команду', 'Удалить эту команду?') .
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
