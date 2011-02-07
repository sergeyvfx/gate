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

if (!user_authorized ()) {
  header('Location: ../../../login');
}

if (!is_responsible(user_id())) {
  print (content_error_page(403));
  return;
}
?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/" ?>">Тризформашка-2011</a><a href="<?= config_get('document-root') . "/tipsling/team" ?>">Команды</a>Мои команды</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $id;
    include $DOCUMENT_ROOT . '/tipsling/menu.php';
    include '../menu.php';
    $contest_menu->SetActive('team');
    $team_menu->SetActive('my');

    if ($action == 'create') {
      team_create_received();
    }

    $contest_menu->Draw();
    $team_menu->Draw();

    if ($action == 'edit') {
      include 'edit.php';
    } else {
      if ($action == 'save') {
        team_update_received($id);
      } else if ($action == 'delete') {
        team_delete($id);
      }
      //BUG When you get team_list, you should show only teams for current contest
      $r = responsible_get_by_id(user_id());
      $list = team_list($r['id']);
      include 'list.php';

      include 'create_form.php';
    }
    ?>
  </div>
</div>
