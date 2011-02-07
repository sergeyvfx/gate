<?php

/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Includer of security stuff
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

if ($_tipsling_Included_ != '#tipsling_Included#') {
  $_tipsling_Included_ = '#tipsling_Included#';

  include $DOCUMENT_ROOT . '/inc/stuff/tipsling/school.php';
  include $DOCUMENT_ROOT . '/inc/stuff/tipsling/responsible.php';
  include $DOCUMENT_ROOT . '/inc/stuff/tipsling/bookkeeper.php';
  include $DOCUMENT_ROOT . '/inc/stuff/tipsling/main.php';
  include $DOCUMENT_ROOT . '/inc/stuff/tipsling/team.php';
}
?>
