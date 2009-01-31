<?php if ($_stuff_Included_!='#stuff_Included#') { $_stuff_Included_='#stuff_Included#';
  include $DOCUMENT_ROOT.'/inc/stuff/dbase.php';
//  if (config_get ('php-version')>=5)
//    include $DOCUMENT_ROOT.'/inc/stuff/browscap.php';
  include $DOCUMENT_ROOT.'/inc/stuff/debug.php';
  include $DOCUMENT_ROOT.'/inc/stuff/hook.php';
  include $DOCUMENT_ROOT.'/inc/stuff/file.php';
  include $DOCUMENT_ROOT.'/inc/stuff/mail.php';
  include $DOCUMENT_ROOT.'/inc/stuff/ipc.php';
  include $DOCUMENT_ROOT.'/inc/stuff/linkage.php';
//  include $DOCUMENT_ROOT.'/inc/stuff/parsers.php';
  include $DOCUMENT_ROOT.'/inc/stuff/redirect.php';
  include $DOCUMENT_ROOT.'/inc/stuff/editor.php';
  include $DOCUMENT_ROOT.'/inc/stuff/log.php';
  include $DOCUMENT_ROOT.'/inc/stuff/iframe/iframe.php';
  include $DOCUMENT_ROOT.'/inc/stuff/calendar.php';
  include $DOCUMENT_ROOT.'/inc/stuff/image/image_validator.php';
  include $DOCUMENT_ROOT.'/inc/stuff/handler.php';
  include $DOCUMENT_ROOT.'/inc/stuff/db_pack.php';
}
?>