<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Administration menu structure definition file
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF != '') {
    print 'HACKERS?';
    die;
  }

  /* Creating the developers' specified navigate menu */
  $database_menu = new CVCMenu ();
  $database_menu->Init ('databaseMenu', 'type=hor;colorized=true;sublevel=1;border=thin;');
  $database_menu->AppendItem ('Таблицы', config_get ('document-root').'/admin/database/table', 'table');
  $database_menu->AppendItem ('Поля', config_get ('document-root').'/admin/database/field', 'field');
  $database_menu->AppendItem ('Связи', config_get ('document-root').'/admin/database/connection', 'connection');
?>
