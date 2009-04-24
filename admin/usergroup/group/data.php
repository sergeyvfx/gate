<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Group administration page generator
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

  $manage_menu->SetActive ('usergroup');
  $usergroup_menu->SetActive ('group');

  if ($action == 'create') {
    group_received_create ();
  }

  /* Printing da page */
  print ($manage_menu->InnerHTML ());
  print ($usergroup_menu->InnerHTML ());

  print ('${information}');

  /* Print the create form */

  if ($action == 'edit') {
    include 'edit.php';
  } else {
    if ($action == 'save') {
      group_update ($id);
    } else if ($action == 'delete') {
      group_delete ($id);
    }

    $list = group_list ();
    if (count ($list) > 0) {
      include 'list.php';
    }
    /* Print the create form */
    include 'create_form.php';
  }
?>
