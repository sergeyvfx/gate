<?php if ($_stuff_mail_included_!='##Mail_Included##') { $_stuff_mail_included_='##Mail_Included##';
  function sendmail ($addr, $subject, $body) {
    global $DOCUMENT_ROOT;
    $css=get_file ($DOCUMENT_ROOT.'/styles/mail.css');
    if ($css!='')
      $css='<style type="text/css">'.$css.'</style>';
    $src='<html><head>'.$css.'</head><body>'.$body.'</body></html>';
    mail ($addr, $subject, $src, 'From: '.config_get ('bot-email')."\n".'Content-Type: text/html; charset="UTF-8" ');
  }
  function sendmail_tpl ($addr, $subject, $tpl, $params=array ()) { $src=tpl ('back/mail/'.$tpl, $params); sendmail ($addr, $subject, $src); }
}
?>
