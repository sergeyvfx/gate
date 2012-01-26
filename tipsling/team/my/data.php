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
?>
<div id="snavigator">Мои команды</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $id;
    $reg_opened = true;
    if ($id!='' && $id != -1)
    {
        $t = team_get_by_id($id);
        $sql = "SELECT * FROM contest where id=".$t['contest_id']." and ".
               "DATE_FORMAT(registration_start,'%Y-%m-%d')<=DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d') ".
               "and DATE_FORMAT(registration_finish,'%Y-%m-%d')>=DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d')";
        $reg_opened = (count(arr_from_query($sql))>0);
    }
    if ($action == 'create' && $reg_opened) {
      team_create_received();
    }

    if ($action == 'edit' && $reg_opened) {
      include 'edit.php';
    } else {
      if ($reg_opened) {
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
        
        $sql = "SELECT * FROM contest where ".
               "DATE_FORMAT(registration_start,'%Y-%m-%d')<=DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d')".
               "and DATE_FORMAT(registration_finish,'%Y-%m-%d')>=DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d')";
        $tmp = arr_from_query($sql);
        if (count($tmp)) {
          include 'create_form.php';
        }
      } else {
        info('Вы должны сначала заполнить информацию о учебном заведении <a href="' . config_get('document-root') . '/login/profile/info/school/">здесь</a>.');
      }
    }
    ?>
  </div>
</div>
