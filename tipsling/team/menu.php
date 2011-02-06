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
$team_menu = new CVCMenu ();
$team_menu->Init('TeamMenu', 'type=hor;colorized=true;sublevel=1;border=thin;');
if (is_responsible(user_id())) {
  $team_menu->AppendItem('Мои команды', config_get('document-root') . '/tipsling/team/my', 'my');
}
$team_menu->AppendItem('Все команды', config_get('document-root') . '/tipsling/team/all', 'all');
?>
