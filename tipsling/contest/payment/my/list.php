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
<<<<<<< HEAD:tipsling/payment/all/list.php
?>
<div class="f" style="margin: 6px -6px 6px;">
  <form action="." method="POST" onsubmit="update (); return false;" onkeypress="if (event.keyCode==13) update ();">
    <table width="100%">
      <tr>
        <td>
          <b>Конкурс: &nbsp;</b>
          <select id="ContestGroup" onchange="update()">
            <?php
                echo('<option value="-1" selected>Все конкурсы</option>');
                $sql = "SELECT\n"
                . " * \n"
                . "FROM\n"
                . " contest \n";
                $tmp = arr_from_query($sql);
                
                foreach ($tmp as $k)
                {
                    $selected = ($k['id'] == $contest) ? ('selected') : ('');
                    echo('<option value ="' . $k['id'] . '" '.$selected.' >' . $k['name'] . '</option>');
                }
            ?>
          </select>
        </td>
        <td style="text-align: right; padding-right: 5px;">
          <b>
            <?php
              if (count($list) > 0) {
                print "Всего платежей: " . count($list);
              }
            ?>
          </b>
        </td>
      </tr>
    </table>
  </form>
</div>
<?php
formo('title=Список всех платежей;');
?>
<script language="JavaScript" type="text/javascript">
  function update () {
    //var sort=getElementById ('sortGroup').value;
    var contest=getElementById ('ContestGroup').value;
    nav ('.?contest='+contest);
  }
</script>
<?php
=======

formo('title=Список моих платежей;');

>>>>>>> tipsling:tipsling/contest/payment/my/list.php
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
