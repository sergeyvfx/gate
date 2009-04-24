<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main configuration
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

    config_set ('WT-Problems-Storage',            '/home/webtester/var/storage/problems');
    config_set ('WT-Problems-Storage-mode',       0777);
    config_set ('WT-Problems-Storage-data-mode',  0666);
  }
?>
