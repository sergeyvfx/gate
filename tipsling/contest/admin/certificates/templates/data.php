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

global $current_contest, $document_root;

if (!user_authorized ()) {
  header('Location: ../../../../../login');
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

<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$it['name']?></a><a href="<?= config_get('document-root') . "/tipsling/contest/admin" ?>">Администрирование</a><a href="<?= config_get('document-root') . "/tipsling/contest/admin/certificates" ?>">Сертификаты</a>Шаблоны</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $id;
    include '../../menu.php';
    include '../menu.php';
    $admin_menu->SetActive('Certificate');
    $certificate_menu->SetActive('Templates');
    
    if ($action == 'create') {
      certificate_create_received();
    }
    
    $admin_menu->Draw();
    $certificate_menu->Draw();
    
    if ($action == 'edit') {
      include 'edit.php';
    } else {
      if ($action == 'save') {
        certificate_update_received($id);
      } else if ($action == 'delete') {
        certificate_delete($id);
      }
      $contest = contest_get_by_id($current_contest);
      $list = certificate_list($contest['family_id']);
      include 'list.php';
      include 'create_form.php';
    }
    
    ?>

  </div>
</div>