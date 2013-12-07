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
?>
<div class="f" style="margin: 6px -6px 6px;">
  <form action="." method="POST" onsubmit="update (); return false;" onkeypress="if (event.keyCode==13) update ();">
    <table width="100%">
      <tr>
        <td>
          <b>Варианты сортировки: &nbsp;</b>
          <select id="sortGroup" onchange="update()">
            <option value="1" <?=($sort == 1) ? ('selected') : ('')?>>По номеру команды</option>
            <option value="2" <?=($sort == 2) ? ('selected') : ('')?>>По региону</option>
            <option value="3" <?=($sort == 3) ? ('selected') : ('')?>>По учебному заведению</option>
          </select>
        </td>
        <td style="text-align: right; padding-right: 5px;">
          <b>
            <?php
              if (count($list) > 0) {
                print "Всего команд: " . count($list);
              }
            ?>
          </b>
        </td>
      </tr>
    </table>
  </form>
</div>
<?php
formo('title=Список зарегистрированных команд;');
?>
<script language="JavaScript" type="text/javascript">
  function update () {
    var sort=getElementById ('sortGroup').value;
    nav ('.?sort='+sort);
  }
</script>
<?php
if (count($list) > 0) {
  global $page, $sort, $contest, $current_contest;

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
        <th width="15%">Учебное заведение</th>
        <th width="13%">Регион</th>
        <th width="10%">Населенный пункт</th>
        <th width="15%">Учитель</th>
        <th width="20%">Участники</th>
        <th width="10%">Статус платежа</th>' .
        (($has_access) ? ('<th width="48" class="last">&nbsp;</th>') : (''))
        . '</tr>' . "\n";

    while ($c < $perPage && $i < $n) {
      $it = $list[$i];
      $d = $is_user_admin;
      $ps = $it['is_payment'];
      $r = responsible_get_by_id($it['responsible_id']);
      $s = school_get_by_id($r['school_id']);
      $number = $it['grade'].'.'.$it['number'];
      $school_name = $s['name'];
      $region = school_get_region_name($r['school_id']);
      $city = school_get_city_name($r['school_id']);
      $teacher = $it['teacher_full_name'];
      $pupils = $it['pupils'];
      $payment = (($ps) ? ('<span style="color: green">Подтвержден</span>') : ('<span style="color: red">Не подтвержден</span>'));
      if ($has_access) {
        $edit_delete =
          '<td align="right">' .
            stencil_ibtnav('edit.gif', '?action=edit&id=' . $it['id'] . '&' . $pageid, 'Изменить информацию о команде') .
            stencil_ibtnav(($d) ? ('cross.gif') : ('cross_d.gif'), ($d) ? ('?action=delete&id=' . $it['id'] . '&' . $pageid) : (''), 'Удалить команду', 'Удалить эту команду?').
          '</td>';
      }

      $pageSrc .= '<tr' . (($i == $n - 1 || $c == $perPage - 1) ? (' class="last"') : ('')) . '>' .
              '<td class="n">' . $number . 
                '<br/><i style="font-weight: normal">(рег. '.$it['grade'].'.'.$it['reg_number'].')<i/>'.
              '</td>' . 
              '<td>' . $school_name . '</td>' .
              '<td>' . $region . '</td>' .
              '<td>' . $city . '</td>' .
              '<td>' . $teacher . '</td>' .
              '<td>' . $pupils . '</td>' .
              '<td>' . $payment . '</td>' .
              $edit_delete .
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
