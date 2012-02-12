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

global $current_contest;

if (!user_authorized ()) {
  header('Location: ../../../login');
}

if (!is_responsible(user_id())) {
  print (content_error_page(403));
  return;
}

if (!is_responsible_has_school(user_id())) {
  redirect(config_get('document-root') . '/login/profile/info/school/?noschool=1');
}

if ($current_contest=='' || $current_contest==-1)
    header('Location: ../../choose');


$contest = contest_get_by_id($current_contest);
$allow_registration = get_contest_status($current_contest)==1;
?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$contest['name']?></a><a href="<?= config_get('document-root') . "/tipsling/contest/team" ?>">Команды</a>Мои команды</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $id;
    include '../menu.php';
    
    $allow_editing = false;
    if ($id!='' && $id != -1)
    {
        $t = team_get_by_id($id);
        $allow_editing = get_contest_status($current_contest)<3 && user_id()==$t['responsible_id'];
    }
    
    $team_menu->SetActive('my');

    if ($action == 'create' && $allow_registration) {
      team_create_received();
    }

    $team_menu->Draw();

    if ($action == 'edit' && $allow_editing) {
      include 'edit.php';
    } else {
      if ($allow_editing) {
        if ($action == 'save') {
          team_update_received($id);
        } else if ($action == 'delete') {
          team_delete($id);
        }
      }
      if ($action=='register_again'){
          team_register_again_received();
      } 
        
      $r = responsible_get_by_id(user_id());
      if ($r['school_id'] > 0 || user_is_system(user_id())) {
        $list = team_list(user_id(), '', $current_contest);
        include 'list.php';

        if ($allow_registration) {
          include 'create_form.php';
        }
      } else {
        info('Вы должны сначала заполнить информацию о учебном заведении <a href="' . config_get('document-root') . '/login/profile/info/school/">здесь</a>.');
      }
    }
    ?>
  </div>
</div>
