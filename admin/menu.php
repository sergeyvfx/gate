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
  $datacontrol_menu = new CVCMenu ();
  $datacontrol_menu->Init ('dataManagmentMenu', 'type=hor;colorized=true;sublevel=1;border=thin;');
  $datacontrol_menu->AppendItem ('Разделы', config_get ('document-root').'/admin/content/', 'content');
  $datacontrol_menu->AppendItem ('Сервисы', config_get ('document-root').'/admin/service/', 'service');
?>
