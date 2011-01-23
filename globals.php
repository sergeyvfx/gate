<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Some main global definitions
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  global $DOCUMENT_ROOT, $IFACE;

  $IFACE = 'SPAWNING NEW IFACE';

  /* Relative directory name */
  $relative = 'gate';

  $s = $_SERVER['DOCUMENT_ROOT'];

  /* Get full path where scripts aer stored */
  if (substr ($s, strlen ($s)-strlen ($relative)-1, strlen ($relative)) == $relative) {
    $relative = '';
  }

  $DOCUMENT_ROOT = $s.$relative;
  $DOCUMENT_ROOT = preg_replace ('/\/*$/', '', $DOCUMENT_ROOT);
?>
