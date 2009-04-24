<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main menu description script
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

  global $DOCUMENT_ROOT;
  $manage_menu = new CVCMenu ();
  $manage_menu->Init ('ManageTopMenu', 'type=hor;colorized=true;hassubmenu=true;border=thin;');
  $manage_menu->AppendItem ('Управление данными',    config_get ('document-root').'/admin/content',        'control');
  $manage_menu->AppendItem ('Пользователи и группы', config_get ('document-root').'/admin/usergroup/user', 'usergroup');
  $manage_menu->AppendItem ('Настройки',             config_get ('document-root').'/admin/settings',       'settings');
  $manage_menu->AppendItem ('Разработчику',          config_get ('document-root').'/admin/dev/datatype',   'to-developer');
?>
