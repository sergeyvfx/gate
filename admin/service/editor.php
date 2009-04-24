<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Service edtitor form
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

  global $id;
  $c = manage_spawn_service ($id);
  print '<div id="snavigator"><a href=".">Сервисы</a>'.$c->GetName ().'</div>';
  editor_draw_menu ();
  $c->Editor_ManageEditForm ();
?>
