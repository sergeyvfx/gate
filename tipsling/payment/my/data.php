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

$sql = "SELECT * FROM contest where ".
        "DATE_FORMAT(contest_start,'%Y-%m-%d')>=DATE_FORMAT(".db_string(date("Y-m-d")).",'%Y-%m-%d')";
$reg_contests = arr_from_query($sql); 
?>
<<<<<<< HEAD
<div id="snavigator"><!--<a href="<?= config_get('document-root') . "/tipsling/" ?>">Тризформашка-2011</a>--><a href="<?= config_get('document-root') . "/tipsling/payment" ?>">Платежи</a>Мои платежи</div>
=======
<div id="snavigator">Мои платежи</div>
>>>>>>> tipsling
${information}
<div class="form">
  <div class="content">
    <?php
    global $DOCUMENT_ROOT, $action, $id;
    include '../menu.php';
    $payment_menu->SetActive('my');
    
    $allow_editing = false;
    if ($id!='' && $id != -1)
    {
        $p = payment_get_by_id($id);
        $allow_editing = get_contest_status($p['contest_id'])<3 && user_id()==$p['responsible_id'];
    }

    if ($action == 'create') {
      payment_create_received();
    }

    $payment_menu->Draw();

    if ($action == 'edit' &&  $allow_editing) {
      include 'edit.php';
    } else {
        if ($allow_editing) {
            if ($action == 'save') {
                payment_update_received($id);
            } else if ($action == 'delete') {
                payment_delete($id);
            }
        }
      $list = payment_list(user_id());
      include 'list.php';
           
      if (count($reg_contests)>0)
          include 'create_form.php';
    }
    ?>
  </div>
</div>
