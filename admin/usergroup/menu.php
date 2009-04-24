<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Menu definition for user/group administration
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF!='') {
    print ('HACKERS?');
    die;
  }

  // Creating the developers' specified navigate menu
  $usergroup_menu=new CVCMenu ();
  $usergroup_menu->Init ('andevMenu', 'type=hor;colorized=true;sublevel=1;border=thin;');
  $usergroup_menu->AppendItem ('Пользователи', config_get ('document-root').'/admin/usergroup/user/', 'user');
  $usergroup_menu->AppendItem ('Группы', config_get ('document-root').'/admin/usergroup/group/', 'group');
?>