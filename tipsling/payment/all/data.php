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
  header('Location: /../../../login');
}

if (!is_bookkeeper(user_id())) {
  print (content_error_page(403));
  return;
}

global $DOCUMENT_ROOT;
include $DOCUMENT_ROOT . '/tipsling/menu.php';
include '../menu.php';
$contest_menu->SetActive('payment');
$payment_menu->SetActive('all');
?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/" ?>">Тризформашка-2011</a><a href="<?= config_get('document-root') . "/tipsling/payment" ?>">Платежи</a>Все платежи</div>
${information}
<div class="form">
  <div class="content">
<?php
$contest_menu->Draw();
$payment_menu->Draw();
//$f->Draw();
?>
  </div>
</div>
