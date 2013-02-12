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
$certificate_menu = new CVCMenu ();
$certificate_menu->Init('ContestMenu', 'type=hor;colorized=true;hassubmenu=true;border=thin;');

$certificate_menu->AppendItem('Шаблоны', config_get('document-root') . '/tipsling/contest/admin/certificates/templates', 'Templates');
$certificate_menu->AppendItem('Сертификаты участников', config_get('document-root') . '/tipsling/contest/admin/certificates/certificates', 'Certificates');
$certificate_menu->AppendItem('Файлы', config_get('document-root') . '/tipsling/contest/admin/certificates/files', 'Files');
$certificate_menu->AppendItem('Ограничения', config_get('document-root') . '/tipsling/contest/admin/certificates/limits', 'Limits');
?>
