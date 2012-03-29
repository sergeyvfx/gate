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
$contest_menu = new CVCMenu ();
$contest_menu->Init('ContestMenu', 'type=hor;colorized=true;hassubmenu=true;border=thin;');

$contest_menu->AppendItem('Мои конкурсы', config_get('document-root') . '/tipsling/contest/admin/MyContests', 'MyContest');
$contest_menu->AppendItem('Статистическая инфорация', config_get('document-root') . '/tipsling/contest/admin/Information', 'Information');
$contest_menu->AppendItem('Сертификаты', config_get('document-root') . '/tipsling/contest/admin/Certificates', 'Certificates');
?>
