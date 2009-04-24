<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * WT console configuration
   *
   * Rename this file to wt.php
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

  if ($_WT_config_included_ != '###WT_config_included###') {
    $_WT_config_included_ != '###WT_config_included###';

    config_set ('WT-Server', '127.0.0.1');
    config_set ('WT-Port',   '13666');
    config_set ('WT-login',  'root');
    config_set ('WT-pass',   'assword');

    config_set ('WT-Cache-Lifetime', 60 * 60 * 24 * 3);
  }
?>
