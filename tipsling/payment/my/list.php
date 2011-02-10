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

formo('title=Список моих платежей;');

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
    $pageSrc .= '<tr class="h"><th class="n first">Дата платежа</th>
        <th width="20%">Номер чек-ордера</th>
        <th width="25%">Плательщик</th>
        <th width="10%">Сумма</th>
        <th width="48" class="last">&nbsp;</th></tr>' . "\n";

    while ($c < $perPage && $i < $n) {
      $it = $list[$i];
      //TODO Check is contest running or archive
      //TODO Check is payment confirm
      $d = 1;
      $amount = $it['amount'];
      if (!preg_match('/\./', $amount)) {
        $amount = $amount . '.00';
      }
      $amount = $amount . ' руб.';
      $pageSrc .= '<tr' . (($i == $n - 1 || $c == $perPage - 1) ? (' class="last"') : ('')) . '>' .
      '<td class="n"><a href=".?action=edit&id=' . $it['id'] . '&' . $pageid . '">' . $it['date'] . '</a></td>' .
      '<td>' . $it['cheque_number'] . '</td>' .
      '<td>' . $it['payer_full_name'] . '<td align="right">' . $amount . '</td>' .
      '<td align="right">' .
        (($d) ? stencil_ibtnav('edit.gif', '?action=edit&id=' . $it['id'] . '&' . $pageid, 'Изменить информацию о платеже') : ('')) .
        (($d) ? stencil_ibtnav('cross.gif', '?action=delete&id=' . $it['id'] . '&' . $pageid, 'Удалить платеж', 'Удалить этот платеж?') : ('')) .
      '</td></tr>' . "\n";
      $c++;
      $i++;
    }
    $pageSrc .= '</table>' . "\n";
    $pages->AppendPage($pageSrc);
  }
  $pages->Draw();
} else {
  info('У Вас пока что нет платежей. Но Вы можете их добавить.');
}

formc ();
?>
