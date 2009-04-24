<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Mail sender
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  global $IFACE;

  if ($IFACE != "SPAWNING NEW IFACE" || $_GET['IFACE'] != '') {
    print ('HACKERS?');
    die;
  }

  if ($_stuff_mail_included_ != '##Mail_Included##') {
    $_stuff_mail_included_ = '##Mail_Included##';

    function sendmail ($addr, $subject, $body) {
      global $DOCUMENT_ROOT;
      $css = get_file ($DOCUMENT_ROOT.'/styles/mail.css');

      if ($css != '') {
        $css = '<style type="text/css">'.$css.'</style>';
      }

      $src = '<html><head>'.$css.'</head><body>'.$body.'</body></html>';
      mail ($addr, $subject, $src, 'From: '.config_get ('bot-email')."\n".
        'Content-Type: text/html; charset="UTF-8" ');
    }

    function sendmail_tpl ($addr, $subject, $tpl, $params = array ()) {
      $src=tpl ('back/mail/'.$tpl, $params);
      sendmail ($addr, $subject, $src);
    }
  }
?>
