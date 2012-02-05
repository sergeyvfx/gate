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

if (!is_bookkeeper(user_id())) {
  print (content_error_page(403));
  return;
}

global $current_contest;

if ($current_contest=='' || $current_contest==-1)
    header('Location: ../../choose');

$contest = contest_get_by_id($current_contest);
?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$contest['name']?></a><a href="<?= config_get('document-root') . "/tipsling/contest/team" ?>">Платежи</a>Все платежи</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $id;
    include '../menu.php';
    $payment_menu->SetActive('all');
    $payment_menu->Draw();

    if ($action == 'edit') {
      include 'edit.php';
    } else {
      if ($action == 'save') {
        payment_apply($id);
      } else if ($action == 'delete') {
        payment_delete($id);
      }
      $list = payment_list(-1, $current_contest);
      include 'list.php';
    }
    ?>
  </div>
</div>
