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


global $sort, $action, $id, $current_contest;

if ($current_contest=='' || $current_contest==-1)
    header('Location: ../../choose');

$contest = contest_get_by_id($current_contest);
?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$contest['name']?></a><a href="<?= config_get('document-root') . "/tipsling/contest/team" ?>">Команды</a>Все команды</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT;
    if (user_authorized ()) {
      include '../menu.php';
      $team_menu->SetActive('all');
      $team_menu->Draw();
    }
    
    $g = group_get_by_name("Администраторы");
    $is_user_admin = is_user_in_group(user_id(), $g['id']) || user_access_root();
    $has_access = is_user_bookkeeper(user_id(), $current_contest) || $is_user_admin;
    
    if ($action == 'edit') {
      include 'edit.php';
    } else {
      if ($action == 'save') {
        $t = team_get_by_id($id);
        team_update_received($id);
      } else if ($action == 'delete') {
        team_delete($id);
      }
      $list = team_list('', $sort, $current_contest);
      include 'list.php';
    }
    ?>
  </div>
</div>
