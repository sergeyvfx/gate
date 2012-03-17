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

if (!is_responsible_has_school(user_id())) {
  redirect(config_get('document-root') . '/login/profile/info/school/?noschool=1');
}

$sql = "SELECT * FROM contest where ".
        "DATE_FORMAT(registration_start,'%Y-%m-%d')<=DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d')".
        "and DATE_FORMAT(registration_finish,'%Y-%m-%d')>=DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d')";
$reg_opened = arr_from_query($sql);
?>
<div id="snavigator">Мои команды</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $id;
    $allow_editing = false;
    if ($id!='' && $id != -1)
    {
        $t = team_get_by_id($id);
        $allow_editing = get_contest_status($t['contest_id'])<4 && user_id()==$t['responsible_id'];
    }
    if ($action == 'create') {
      team_create_received();
    }

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
      $r = responsible_get_by_id(user_id());
      if ($r['school_id'] > 0 || user_is_system(user_id())) {
        $list = team_list(user_id());
        include 'list.php';
        
        if (count($reg_opened)) {
          include 'create_form.php';
        }
      } else {
        info('Вы должны сначала заполнить информацию о учебном заведении <a href="' . config_get('document-root') . '/login/profile/info/school/">здесь</a>.');
      }
    }
    ?>
  </div>
</div>
