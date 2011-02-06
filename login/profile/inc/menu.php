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
  print 'HACKERS?';
  die;
}

global $DOCUMENT_ROOT;
$profile_menu = new CVCMenu ();
$profile_menu->Init('ProfileTopMenu', 'type=hor;colorized=true;hassubmenu=true;border=thin;');
$profile_menu->AppendItem('Мой профиль', config_get('document-root') . '/login/profile/info', 'info');
$profile_menu->AppendItem('Настройки', config_get('document-root') . '/login/profile/settings', 'settings');
?>
