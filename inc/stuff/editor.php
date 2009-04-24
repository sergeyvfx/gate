<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Editor's stuff
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  global $IFACE;

  if ($IFACE != "SPAWNING NEW IFACE" || $_GET['IFACE'] != '') {
    print ('HACKERS?');
    die;
  }

  if ($_editor_included_ != '#editor_Included#') {
    $_editor_included_ = '#editor_Included#';

    $editor_menus     = array ();
    $editor_functions = array ();
    $editor_usage     = array ();

    function editor_initialize ($name = 'default') {
      global $editor_menus, $function;

      if (isset ($editor_menus[$name])) {
        return;
      }

      $editor_menus[$name] = new CVCMenu ();
      $editor_menus[$name]->Init ('editor_menu',
                                  'type=hor;colorized=true;sublevel=0;border=thin;');

      //editor_get_valid_function ();
      $editor_menus[$name]->SetActiveByIndex ($function);
    }

    function editor_add_function ($title, $function, $name = 'default', $args = '') {
      global $editor_functions;
      global $editor_menus, $editor_function, $editor_usage, $id;

      if ($editor_usage [$title][$function][$name]) {
        return;
      }

      //if (isset ($editor_function[$name][$function])) return;
      $fid = count ($editor_functions[$name]);
      $editor_menus[$name]->AppendItem ($title, content_url_get_full ().
                                        '&function='.$fid.'&'.$args, $function);
      $editor_functions[$name][] = array ('title' => $title,
                                          'function' => $function);
      $editor_usage [$title][$function][$name] = true;
    }

    function editor_set_function ($function, $name = 'default') {
      global $editor_menus;
      $editor_menus[$name]->SetActive ($function);
    }

    function editor_get_function ($name='default') {
      global $function, $editor_functions, $editor_menus;

      if ($function == '') {
        $function = 0;
      }

      $t = $editor_functions[$name][$function]['function'];

      if ($t != '') {
        return $t;
      }

      return $editor_functions[$name][0]['function'];
    }

    function editor_get_valid_function ($name = 'default') {
      global $function, $editor_functions;

      if (!isset ($editor_functions[$name][$function])) {
        $function = 0;
      }
    }

    function editor_draw_menu ($name = 'default') {
      global $function, $editor_functions, $editor_menus;

      if (!isset ($editor_menus[$name])) {
        return;
      }

      if (!isset ($editor_functions[$name][$function]['function'])) {
        $editor_menus[$name]->SetActiveByIndex (0);
      }

      $editor_menus[$name]->Draw ();
    }
  }
?>
