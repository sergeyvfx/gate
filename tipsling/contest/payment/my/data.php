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

if ($current_contest=='' || $current_contest==-1)
    header('Location: ../../choose');

$contest = contest_get_by_id($current_contest);
$contest_status = get_contest_status($current_contest);
$allow_create = ($contest_status & 2) == 0 && ($contest_status & 4) == 0;
?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$contest['name']?></a><a href="<?= config_get('document-root') . "/tipsling/contest/payment" ?>">Платежи</a>Мои платежи</div>    
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $id;
    include '../menu.php';
    
    $allow_editing = false;
    if ($id!='' && $id != -1)
    {
        $p = payment_get_by_id($id);
        $allow_editing = $allow_create && user_id()==$p['responsible_id'];
    }
    
    $payment_menu->SetActive('my');

    if ($action == 'create' && $allow_create) {
      payment_create_received();
    }

    $payment_menu->Draw();

    if ($action == 'edit'  && $allow_editing) {
      include 'edit.php';
    } else {
        if ($allow_editing) {
            if ($action == 'save') {
                payment_update_received($id);
            } else if ($action == 'delete') {
                payment_delete($id);
            }
        }
      $list = payment_list(user_id(), $current_contest);
      include 'list.php';
      
      if ($allow_create){
        include 'create_form.php';
      }
    }
    ?>
  </div>
</div>
