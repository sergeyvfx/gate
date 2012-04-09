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

global $DOCUMENT_ROOT;
$payment_menu = new CVCMenu ();
$payment_menu->Init('PaymentMenu', 'type=hor;colorized=true;hassubmenu=true;border=thin;');
$payment_menu->AppendItem('Мои платежи', config_get('document-root') . '/tipsling/payment/my', 'my');
?>
