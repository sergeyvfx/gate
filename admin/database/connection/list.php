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

  formo ('title=Список связей между таблицами;');
 
  $pages = new CVCPagintation ();
  $pages->Init('', 'bottomPages=false;skiponcepage=true;');
  
  $pageSrc = 
    '<table class="list">
    <tr>
        <th style="text-align: center;">Таблица 1</th>
        <th style="text-align: center;">Таблица 2</th>
        <th style="text-align: center;">Промежуточная таблица</th>
        <th style="text-align: center;">Связь</th>
        <th width="48" class="last">&nbsp;</th>
    </tr>';
  while ($field = mysql_fetch_array($fields))
  {
    $pageSrc .= '<tr style="text-align: center;">
        <td>'.$field["table1"].'</td>
        <td>'.$field["table2"].'</td>
        <td>'.$field["connection_table"].'</td>
        <td>'.$field["connection"].'</td>
        <td align="right">' . stencil_ibtnav('cross.gif', '?action=delete&id=' . $field['id'], 'Удалить связь', 'Удалить эту связь?') .
       '</td></tr>';
  }
  $pageSrc .= '</table>';
  $pages->AppendPage($pageSrc);
  $pages->Draw();

  formc ();
?>
