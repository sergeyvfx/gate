<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Stencil for tab control
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

  if ($_stencil_tabcontrol__Included_ != '#stencil_tabcontrol_Included#') {
    $_stencil_tabcontrol_Included_ = '#stencil_tabcontrol_Included#';

    global $tabctrl_stuff_included;

    $tabctrl_stuff_included = false;

    function tabctrl_include_stuff () {
      global $tabctrl_stuff_included, $CORE;

      if ($tabctrl_stuff_included) {
        return;
      }

      $CORE->AddStyle ('tabctrl');
      $tabctrl_stuff_included = true;
    }

    function stencil_tabo ($settings) {
      tabctrl_include_stuff ();
      $s = unserialize_params ($settings);
      return tpl ('back/stencil/tabo', $s);
    }

    function stencil_tabc ($settings = '') {
      $s = unserialize_params ($settings);
      return tpl ('back/stencil/tabc', $s);
    }

    function tabo ($s)      { println (stencil_tabo ($s)); }
    function tabc ($s = '') { println (stencil_tabc ($s)); }  
  }
?>
