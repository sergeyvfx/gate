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
  header('Location: ../../../../login');
}

if (!is_responsible(user_id())) {
  print (content_error_page(403));
  return;
}

$it = contest_get_by_id($current_contest);
$query = arr_from_query("select * from Admin_FamilyContest ".
                   "where family_contest_id=".$it['family_id']." and ".
                   "user_id=".user_id());
if (count ($query) <= 0)
{
  print (content_error_page(403));
  return;
}

?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest" ?>"><?=$it['name']?></a>Администрирование</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $id;
    include '../menu.php';
    $contest_menu->SetActive('MyContest');
    
    if ($action == 'create') {
      contest_create_received();
    }
    $contest_menu->Draw();

    if ($action == 'edit') {
      include 'edit.php';
    } else {
      if ($action == 'save') {
        contest_update_received($id);
        //payment_update_received($id);
      } else if ($action == 'delete') {
        contest_delete($id);
        //payment_delete($id);
      }
      $list = contest_list($it['family_id']);
      include 'list.php';
      include 'create_form.php';
    }
    ?>
  </div>
</div>
