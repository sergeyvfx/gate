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

formo('title=Указать результаты конкурса;');

if (count($list) > 0) {
  global $page, $contest, $current_contest;

  ?>
  <form action=".?action=save<?= (($page != '') ? ('&page=' . $page) : ('')); ?>" method="POST">

  <?php
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
    $pageSrc = '<table id="teamResultList" class="list">' . "\n";
    $pageSrc .= '<tr class="h">
        <th width="100px" style="text-align:center">Номер команды</th>
        <th style="text-align:center">Количество баллов</th>
        <th style="text-align:center">Место в параллели<!--<br/><a href="./?action=resetPlace"><i>(пересчитать)</i></a>--></th>
        <th style="text-align:center">Место в общекомандном зачете<!--<br/><a href="./?action=resetCommonPlace"><i>(пересчитать)</i></a>--></th>
        </tr>' . "\n";

    while ($c < $perPage && $i < $n) {
      $team = $list[$i];
      $number = $team['grade'].'.'.$team['number'];
      $pageSrc .= '<tr' . (($i == $n - 1 || $c == $perPage - 1) ? (' class="last"') : ('')) . '>' .
              '<td class="n">' . $number . 
                '<br/><i style="font-weight: normal">(рег. '.$team['grade'].'.'.$team['reg_number'].')<i/>'.
              '</td>' . 
              '<td align="center"><input type=text name="mark['.$team['id'].']" value="' . $team['mark'] . '"/></td>' .
              '<td align="center"><input type=text name="place['.$team['id'].']" value="' . $team['place'] . '"/></td>' .
              '<td align="center"><input type=text name="common_place['.$team['id'].']" value="' . $team['common_place'] . '"/></td>' .
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
?>
    <div class="formPast">
      <button class="submitBtn block" type="submit">Сохранить</button>
    </div>
  </form>
<?php
formc ();
?>