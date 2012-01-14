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
<div id="snavigator"><!--<a href="<?= config_get('document-root') . "/tipsling/" ?>">Тризформашка-2011</a>--><a href="<?= config_get('document-root') . "/tipsling/payment" ?>">Платежи</a>Мои платежи</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $id;
    include '../menu.php';
    $payment_menu->SetActive('my');

    if ($action == 'create') {
      payment_create_received();
    }

    $payment_menu->Draw();

    if ($action == 'edit') {
      include 'edit.php';
    } else {
      if ($action == 'save') {
        payment_update_received($id);
      } else if ($action == 'delete') {
        payment_delete($id);
      }
      //BUG When you get payment_list, you should show only payment for current contest
      $list = payment_list(user_id());
      include 'list.php';
      include 'create_form.php';
    }
    ?>
  </div>
</div>
