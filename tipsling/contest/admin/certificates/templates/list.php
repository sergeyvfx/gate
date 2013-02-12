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

formo('title=Список сертификатов');

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
    $pageSrc .= '<tr class="h"><th width="25%" style="text-align: center;">Название</th>
        <th width="30%" style="text-align: center;">Для кого</th>
        <th width="25%" style="text-align: center;">Ограничение</th>
        <th width="100" style="text-align: center;">Шаблон</th>
        <th width="48" class="last">&nbsp;</th></tr>' . "\n";

    
    while ($c < $perPage && $i < $n) {
      $it = $list[$i];
      $name = $it['name'];
      $template = $it['template'];
      $limit = $it['limit_name'];
      $for = $it['for'];
      
      $pageSrc .= '<tr' . (($i == $n - 1 || $c == $perPage - 1) ? (' class="last"') : ('')) . '>' .
      '<td class="n"><a href=".?action=edit&id=' . $it['id'] . '">' . $name . '</td>' .
      '<td align="center">' . $for . '</td>' .
      '<td align="center">' . ($limit==''?'Нет': $limit) . '</td>' .
      '<td align="center">' . ($template==''?'Не задан':'<a href=".?action=view&id=' . $it['id'] . '">' . 'Просмотр') . '</td>' .
      '<td align="right">' .
        stencil_ibtnav('edit.gif', '?action=edit&id=' . $it['id'] . '&' . $pageid, 'Изменить сертификат') .
        stencil_ibtnav('cross.gif', '?action=delete&id=' . $it['id'] . '&' . $pageid, 'Удалить сертификат', 'Удалить этот сертификат?') .
      '</td></tr>' . "\n";
      $c++;
      $i++;
    }
    $pageSrc .= '</table>' . "\n";
    $pages->AppendPage($pageSrc);
  }
  $pages->Draw();
} else {
  info('На данный момент не задано ни одного сертификата');
}

formc ();
?>
