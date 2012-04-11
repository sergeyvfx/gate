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
    $pageSrc .= '<tr class="h"><th width="15%" style="text-align: center;">Дата платежа</th>
        <th width="15%" style="text-align: left;">Номер чек-ордера</th>
        <th width="15%" style="text-align: center;">Плательщик</th>
        <th width="10%" style="text-align: center;">Сумма</th>
        <th width="15%" style="text-align: center;">Конкурс</th>
        <th width="15%" style="text-align: center;" align="right">Дата поступления</th>
        <th width="48" class="last">&nbsp;</th></tr>' . "\n";

    while ($c < $perPage && $i < $n) {
      $it = $list[$i];
      $d = $it['date_arrival'] == null ? (1) : (0);
      $amount = $it['amount'];
      if (!preg_match('/\./', $amount)) {
        $amount = $amount . '.00';
      }
      $contest_not_running = get_contest_status($it['contest_id'])<3;
      $amount = $amount . ' руб.';
      $contest_name = contest_get_by_id($it['contest_id']);
      $pageSrc .= '<tr' . (($i == $n - 1 || $c == $perPage - 1) ? (' class="last"') : ('')) . '>' .
      '<td class="n">' . (($d && $contest_not_running) ? ('<a href=".?action=edit&id=' . $it['id'] . '&' . $pageid . '">') : ('')) . date_format(date_create($it['date']), 'd.m.Y') . (($d && $contest_not_running) ? ('</a>') : ('')) . '</td>' .
      '<td>' . $it['cheque_number'] . '</td>' .
      '<td>' . $it['payer_full_name'] . '<td align="right">' . $amount . '</td>' .
      '<td>' . $contest_name['name'] . '</td>' .
      '<td style="text-align: center;">' . (($d) ? ('<span style="color: red">Не поступил</span>') : ('<span style="color: green">' . date_format(date_create($it['date_arrival']), 'd.m.Y') . '</span>')) . '</td>' .
      '<td align="right">' .
        stencil_ibtnav((($d && $contest_not_running) ? 'edit.gif' : 'edit_d.gif'), (($d && $contest_not_running) ? '?action=edit&id=' . $it['id'] . '&' . $pageid : ''), 'Изменить информацию о платеже') .
        stencil_ibtnav((($d && $contest_not_running) ? 'cross.gif' : 'cross_d.gif'), (($d && $contest_not_running) ? '?action=delete&id=' . $it['id'] . '&' . $pageid : ''), 'Удалить платеж', 'Удалить этот платеж?') .
      '</td></tr>' . "\n";
      $c++;
      $i++;
    }
    $pageSrc .= '</table>' . "\n";
    $pages->AppendPage($pageSrc);
  }
  $pages->Draw();
} else {
  info('У Вас пока что нет платежей на конкурсе "'.$contest[name].'". Но Вы можете их добавить.');
}

formc ();
?>
