<?php
  if ($PHP_SELF!='') {print ('HACKERS?'); die;}
  if (!user_authorized () || !user_access_root ()) header ('Location: '.config_get ('document-root').'/admin');
  global $DOCUMENT_ROOT, $action, $id;
  include $DOCUMENT_ROOT.'/admin/inc/menu.php';
  include '../menu.php';
  $manage_menu->SetActive ('to-developer');
  $mandev_menu->SetActive ('datasets');

  if ($action=='create') manage_dataset_received_create ();

  // Printing da page
  print ($manage_menu->InnerHTML ());
  print ($mandev_menu->InnerHTML ());
  print ('${information}');
  
  if ($action=='edit')
    include 'edit.php'; else
    {
      if ($action=='save') manage_dataset_update_received ($id); else
      if ($action=='delete') manage_dataset_delete ($id);
      $list=manage_dataset_get_list ();
      if (count ($list)>0) include 'list.php';
      // Print the create form
      include 'create_form.php';
    }
?>
