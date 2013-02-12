<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main handlers for settings administration
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

  if (!user_authorized () || !user_access_root ()) {
    header ('Location: '.config_get ('document-root').'/admin');
  }

  global $DOCUMENT_ROOT, $action, $id;
  include $DOCUMENT_ROOT.'/admin/inc/menu.php';
  include '../menu.php';
  $manage_menu->SetActive ('database');
  $database_menu->SetActive ('connection');

  if ($action == 'create')
  {
      if (trim($_POST['table1'])=='')
          add_info ('Не выбрана первая таблица');
      else if (trim($_POST['table2'])=='')
          add_info ('Не выбрана вторая таблица');
      else if (trim($_POST['connetion_table'])!='' && trim($_POST['connetion'])!='')
          add_info ('Нельзя указывать и промежуточную таблицу, и связь одновременно');
      else if (trim($_POST['connection_table'])=='' && trim($_POST['connection'])=='')
          add_info ('Не задана ни промежуточная таблица, ни связь');
      else
      {
          db_insert("table_connections", array('table1_id'=>  db_html_string($_POST['table1']), 'table2_id'=>  db_html_string($_POST['table2']), 'connect_table_id'=>  ($_POST['connection_table']?  db_html_string($_POST['connection_table']):'null'), 'connection'=>($_POST['connection']!=""?db_html_string($_POST['connection']):'null')));
      }
  }
  else if ($action == 'delete') 
  {
    db_delete('table_connections', 'id=' . $id);
  }
  
  // Printing da page
  $manage_menu->Draw ();
  $database_menu->Draw ();

  $query = "select `table1`.`table` as table1,
                   `table2`.`table` as table2,
                   `visible_table`.`table` as connection_table,
                   `table_connections`.`connection`,
                   `table_connections`.`id`
            from `visible_table` as table1, 
                 `visible_table` as table2, 
                 `table_connections` 
                    left join `visible_table` on `visible_table`.id = `table_connections`.`connect_table_id` 
            where `table_connections`.`table1_id` = `table1`.`id` AND `table_connections`.`table2_id`=`table2`.`id`";
  $fields = mysql_query($query);
   
  include 'list.php';
  include 'create_form.php';
?>
