<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main handlers for datatype administration
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

  global $DOCUMENT_ROOT, $CORE, $action, $id;
  include $DOCUMENT_ROOT.'/admin/inc/menu.php';
  include '../menu.php';

  $CORE->AddScriptFile ('man_dtypes.js');
  $manage_menu->SetActive ('to-developer');
  $mandev_menu->SetActive ('datatype');

  if ($action == 'create') {
    manage_datatype_received_create ();
  }

  // Printing da page
  print ($manage_menu->InnerHTML ());
  print ($mandev_menu->InnerHTML ());

  print ('${information}');

  // Print created datatypes
  if ($action == 'edit') {
    include 'edit.php';
  } else {
    if ($action == 'save') {
      manage_datatype_update_received ($id);
    } else if ($action == 'delete') {
      manage_datatype_delete ($id);
    }

    $q = db_query ('SELECT * FROM `datatypes` ORDER BY `name`');
    if (db_affected () > 0) {
      include 'list.php';
    }

    // Print the create form
    include 'create_form.php';
  }
?>
