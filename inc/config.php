<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Configuration provider
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

  if ($_config_included_ != '#config_Included#') {
    $_builtin_included_ = '#config_Included#';

    function config_set ($key, $val) {
      global $config;
      $config[$key] = $val;
    }

    function config_get ($key) {
      global $config;
      return $config[$key];
    }
}
?>
