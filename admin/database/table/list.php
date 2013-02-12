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

  formo ('title=Список таблиц;');
  ?>
    <form action=".?action=save" method="POST">
  <?php
  
  $pages = new CVCPagintation ();
  $pages->Init('', 'bottomPages=false;skiponcepage=true;');
  
  $pageSrc = 
    '<table class="list">
    <tr>
        <th style="text-align: center;">Таблица</th>
        <th style="text-align: center;">Видимость</th>
    </tr>';
  foreach ($tables as $table)
  {
    $it = db_field_value("visible_table", "visible", "`table` = ".db_html_string($table));
    if ($it == '')
    {
        db_insert("visible_table", array("table"=> db_html_string($table)));
    }
    
    $pageSrc .= '<tr style="text-align: center;"><td>'.$table.'</td><td><input type="checkbox" id="'.$table.'" name="'.$table.'" value="1" '.(($it=='1')?'checked':'').'/></td></tr>';
  }
  $pageSrc .= '</table>';
  $pages->AppendPage($pageSrc);
  $pages->Draw();
  ?>
  
    <div class="formPast">
        <button class="submitBtn block" type="submit">Сохранить</button>
    </div>
  </form>

<?php
  formc ();
?>
