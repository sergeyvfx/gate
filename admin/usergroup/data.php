<?php
  if ($PHP_SELF!='') {print ('HACKERS?'); die;}
  if (!user_authorized () || !user_access_root ()) header ('Location: '.config_get ('document-root').'/admin');

  global $DOCUMENT_ROOT;
  include $DOCUMENT_ROOT.'/admin/inc/menu.php';
  include 'menu.php';
  $manage_menu->SetActive ('usergroup');

  // Printing da page
  print ($manage_menu->InnerHTML ()); // Print the manage menu
  print ($usergroup_menu->InnerHTML ());
?>
