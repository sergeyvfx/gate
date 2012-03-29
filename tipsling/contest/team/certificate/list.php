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

formo('title=Сертификаты');

if (count($team_list) > 0) {
  global $page;

  $perPage = opt_get('user_count');
  if ($perPage <= 0) {
    $perpage = 10;
  }

  $pages = new CVCPagintation ();
  $pages->Init('', 'bottomPages=false;skiponcepage=true;');
  $i = 0;
  $n = count($team_list);

  if ($page != '') {
    $pageid = '&page=' . $page;
  }

  while ($i < $n) {
    $c = 0;
    $pageSrc = '<table class="list">' . "\n";
    $pageSrc .= '<tr class="h">
        <th width="8%" align="center">Номер команды</th>';
    $certificate_count = count($certificate_list);
    $j=0;
    $column_width = 92/$certificate_count;
    
    while($j<$certificate_count)
    {
        $certificate = $certificate_list[$j];
        $pageSrc .= '<th style="text-align:center;" width="'.$column_width.'%">'.$certificate["name"].'</th>';
        $j++;
    }
    $pageSrc .= '</tr>' . "\n";
    
    while ($c < $perPage && $i < $n) {
      $it = $team_list[$i];

      $pageSrc .= '<tr' . (($i == $n - 1 || $c == $perPage - 1) ? (' class="last"') : ('')) . '>' .  
      '<td class="n" align="center">' . $it['grade'].'.'. $it['number'] . '</td>';
      $j=0;
      while($j<$certificate_count)
      {
        $certificate = $certificate_list[$j];
        $pageSrc .= '<td align="center"><a href="./download/?team='.$it["id"].'&certificate='.$certificate["id"].'">скачать</td>';
        $j++;
      }              
      $pageSrc .= '</tr>' . "\n";
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