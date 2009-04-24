<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Compilers' configurations
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

  if ($_WT_config_compilers_included_ != '###WT_config_compilers_included###') {
    $_WT_config_compilers_included_ != '###WT_config_compilers_included###';

    global $WT_Compilers;

    $WT_Compilers = array (
      array ('title' => 'GNU C Compiler 4.1.2',          'id' => 'GCC'),
      array ('title' => 'GNU C++ Compiler 4.1.2',        'id' => 'G++'),
      array ('title' => 'Free Pascal Compiler 2.2.0',    'id' => 'FPC'),
      array ('title' => 'Borland Delphi for Linux 14.5', 'id' => 'DCC'),
      array ('title' => 'Sun Java',                      'id' => 'Java'),
     );
  }
?>
