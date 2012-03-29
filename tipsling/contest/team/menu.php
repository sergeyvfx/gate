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

global $DOCUMENT_ROOT, $current_contest;
$team_menu = new CVCMenu ();
$team_menu->Init('TeamMenu', 'type=hor;colorized=true;hassubmenu=true;border=thin;');
if (is_responsible(user_id())) {
  $team_menu->AppendItem('Мои команды', config_get('document-root') . '/tipsling/contest/team/my', 'my');
  $it = contest_get_by_id($current_contest);
  $q = arr_from_query ("select * ".
                                       "from Admin_FamilyContest ".
                                       "where family_contest_id=".$it['family_id']." and ".
                                       "user_id=".user_id());
  if (count($q) > 0)
    $team_menu->AppendItem('Сертификаты', config_get('document-root') . '/tipsling/contest/team/certificate', 'certificate');
}
$team_menu->AppendItem('Все команды', config_get('document-root') . '/tipsling/contest/team/all', 'all');
?>
