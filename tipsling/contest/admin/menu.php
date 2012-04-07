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
$admin_menu = new CVCMenu ();
$admin_menu->Init('ContestMenu', 'type=hor;colorized=true;hassubmenu=true;border=thin;');

$admin_menu->AppendItem('Мои конкурсы', config_get('document-root') . '/tipsling/contest/admin/MyContests', 'MyContest');
$admin_menu->AppendItem('Статистическая инфорация', config_get('document-root') . '/tipsling/contest/admin/Information', 'Information');
$admin_menu->AppendItem('Сертификаты', config_get('document-root') . '/tipsling/contest/admin/certificates', 'Certificates');
?>
