<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main handers for storage administration
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF != '') {
    print ('HACKERS?');
    die;
  }

  if (!user_authorized () || !user_access_root ()) {
    header ('Location: '.config_get ('document-root').'/admin');
  }

  global $DOCUMENT_ROOT, $action, $id;
  include $DOCUMENT_ROOT.'/admin/inc/menu.php';
  include '../menu.php';
  $manage_menu->SetActive ('to-developer');
  $mandev_menu->SetActive ('storages');

  if ($action == 'create') {
    manage_storage_create_received ();
  }

  // Printing da page
  print ($manage_menu->InnerHTML ());
  print ($mandev_menu->InnerHTML ());

  print ('${information}');

  if ($action == 'edit') {
    include 'edit.php';
  } else {
    if ($action == 'save') {
      manage_storage_update ($id);
    } else if ($action == 'delete') {
      manage_storage_delete ($id);
    }

    $list = manage_storage_get_list ();
    if (count ($list) > 0) {
      include ('list.php');
    }

    // Print the create form
    include 'create_form.php';
  }
?>
