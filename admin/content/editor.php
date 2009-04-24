<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Script for entry editing form generation
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
  $c = wiki_spawn_content ($id);
  print ('<div id="snavigator"><a href=".">Разделы</a>'.
         wiki_content_navigator ($id, 'action=editor')).'</div>';
  editor_draw_menu ();
  $c->Editor_ManageEditForm ();
?>
