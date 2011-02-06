<?php

/**
 * Gate - Wiki engine and web-interface for WebTester Server
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

if (!user_authorized ()) {
  header('Location: ' . config_get('document-root') . '/login/profile');
}

global $DOCUMENT_ROOT;
include $DOCUMENT_ROOT . '/login/profile/inc/menu.php';
include 'menu.php';
$profile_menu->SetActive('info');

/* Printing da page */
print ($profile_menu->InnerHTML()); /* Print the manage menu */
print ($info_menu->InnerHTML());
?>
