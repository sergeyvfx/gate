<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Script for sections list generation
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

  formo ('title=Список доступных полей таблиц;');
 
  $pages = new CVCPagintation ();
  $pages->Init('', 'bottomPages=false;skiponcepage=true;');
  
  $pageSrc = 
    '<table class="list">
    <tr>
        <th style="text-align: center;">Таблица</th>
        <th style="text-align: center;">Поле</th>
        <th style="text-align: center;">Заголовок</th>
        <th width="48" class="last">&nbsp;</th>
    </tr>';
  while ($field = mysql_fetch_array($fields))
  {
    $pageSrc .= '<tr style="text-align: center;">
        <td>'.$field["table"].'</td>
        <td>'.$field["field"].'</td>
        <td>'.$field["caption"].'</td>
        <td align="right">' . stencil_ibtnav('cross.gif', '?action=delete&id=' . $field['id'], 'Удалить поле', 'Удалить это поле из списка?') .
       '</td></tr>';
  }
  $pageSrc .= '</table>';
  $pages->AppendPage($pageSrc);
  $pages->Draw();

  formc ();
?>
