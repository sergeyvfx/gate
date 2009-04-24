<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Debugging function
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

  if ($_debug_included_ != '#debug_Included#') {
    $_debug_included_ = '#debug_Included#'; 

    function debug_watchdog_clear () {
      global $debug_watchdog;
      $debug_watchdog = mtime ();
    }

    function debug_get_watchdog () {
      global $debug_watchdog;
      return mtime () - $debug_watchdog;
    }
  }
?>
