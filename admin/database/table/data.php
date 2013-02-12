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

  global $DOCUMENT_ROOT, $action;
  include $DOCUMENT_ROOT.'/admin/inc/menu.php';
  include '../menu.php';
  $manage_menu->SetActive ('database');
  $database_menu->SetActive ('table');

  // Printing da page
  $manage_menu->Draw ();
  $database_menu->Draw ();
  
  $tables = db_get_tables();
  
  if ($action=='save')
  {
      foreach ($tables as $table)
      {
          db_update("visible_table", array("visible"=>($_POST[$table]=="1"?1:0)), "`table`=". db_html_string($table));
      }
  }
  
  include 'list.php';
?>
