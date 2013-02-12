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
  $database_menu->SetActive ('field');
  
  if ($action == 'create')
  {
      if (trim($_POST['table'])=='')
          add_info ('Не выбрана таблица');
      else if (trim($_POST['field'])=='')
          add_info ('Не выбрано поле таблицы');
      else if (trim($_POST['caption'])=='')
          add_info ('Не задан заголовок поля');
      else
      {
          db_insert("visible_field", array('table_id'=>  db_html_string($_POST['table']), 'field'=>db_html_string($_POST['field']), 'caption'=>db_html_string($_POST['caption'])));
      }
  }
  else if ($action == 'delete') 
  {
    db_delete('visible_field', 'id=' . $id);
  }

  // Printing da page
  $manage_menu->Draw ();
  $database_menu->Draw ();
  
   
  $query = "select `visible_field`.`id` as 'id', `visible_table`.`table` as 'table', `visible_field`.`field` as 'field', `visible_field`.`caption` as 'caption' from `visible_table`, `visible_field` where `visible_field`.`table_id` = `visible_table`.`id` AND `visible_table`.`visible`=1";
  $fields = mysql_query($query);
   
  include 'list.php';
  include 'create_form.php';  
?>
