<?php if ($PHP_SELF!='') {print 'HACKERS?'; die;} 
  if (!user_authorized ()) {
    include 'inc/login.php';
    //header ('location: '.config_get ('document-root').'/login?redirect='.urlencode (config_get ('document-root').'/admin'));
  } else
  if (user_access_root ()) {
    header ('Location: content');
  } else
    include 'inc/denied.php';
?>
