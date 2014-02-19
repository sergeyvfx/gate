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

formo('title=Список всех платежей;');

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
    $pageSrc .= '<tr class="h"><th width="10%" style="text-align: center;">Дата платежа</th>
        <th width="20%" style="text-align: center;">Вариант оплаты</th>
        <th width="10%" style="text-align: center;">Сумма</th>        
        <th width="10%" style="text-align: center;">Номера команд</th>
        <th width="15%" style="text-align: center;">Плательщик</th>
        <th width="15%" style="text-align: center;">Номер чек-ордера</th>
        <th width="15%" style="text-align: center;" align="right">Дата поступления</th>
        <th width="48" class="last">&nbsp;</th></tr>' . "\n";
    while ($c < $perPage && $i < $n) {
      $it = $list[$i];
      $d = $it['date_arrival'] == null ? (1) : (0);
      $amount = $it['amount'];
      if (!preg_match('/\./', $amount)) {
        $amount = $amount . '.00';
      }
      $amount = $amount . ' руб.';
      switch ($it['payment_option']){
          case 1: $payment_option_name = 'Учебный центр "Информатика" (банковский перевод)'; break;
          case 2: $payment_option_name = 'Учебный центр "Информатика" (безналичный расчет)'; break;
          case 3: $payment_option_name = 'Учебный центр "Информатика" (в кассе)'; break;
          case 4: $payment_option_name = 'Яндекс.Деньги'; break;
          case 5: $payment_option_name = 'Перевод на карту Сбербанка (по номеру карты)'; break;
          case 6: $payment_option_name = 'Перевод на карту Сбербанка (по реквизитам)'; break;
          case -1: $payment_option_name = 'Другое (указать в примечании)'; break;
      }
      $pageSrc .= '<tr' . (($i == $n - 1 || $c == $perPage - 1) ? (' class="last"') : ('')) . '>' .
      '<td class="n"><a href=".?action=edit&id=' . $it['id'] . '&' . $pageid . '">' . date_format(date_create($it['date']), 'd.m.Y') . '</a></td>' .
      '<td style="text-align: center;">' . $payment_option_name . '</td>' .
      '<td style="text-align: center;">' . $amount . '</td>' .
      '<td style="text-align: center;">' . $it['team_numbers'] . '</td>' .
      '<td style="text-align: center;">' . $it['payer_full_name'] . '</td>' .
      '<td style="text-align: center;">' . $it['cheque_number'] . '</td>' .
      '<td style="text-align: center;">' . (($d) ? ('<span style="color: red">Не поступил</span>') : ('<span style="color: green">' . date_format(date_create($it['date_arrival']), 'd.m.Y') . '</span>')) . '</td>' .
      '<td align="right">' .
        stencil_ibtnav('edit.gif', '?action=edit&id=' . $it['id'] . '&' . $pageid, 'Изменить информацию о платеже') .
        stencil_ibtnav('cross.gif', '?action=delete&id=' . $it['id'] . '&' . $pageid, 'Удалить платеж', 'Удалить этот платеж?') .
      '</td></tr>' . "\n";
      $c++;
      $i++;
    }
    $pageSrc .= '</table>' . "\n";
    $pages->AppendPage($pageSrc);
  }
  $pages->Draw();
} else {
  info('Список платежей пуст');
}

formc ();
?>
