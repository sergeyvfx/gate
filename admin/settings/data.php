<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main handlers for settings administration
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

  $printSection = true;
  $manage_menu->SetActive ('settings');

  if ($action == 'create') {
    manage_settings_create_received ();
  }

  // Printing da page
  print ($manage_menu->InnerHTML ());

  if ($action == 'edit') {
    $printSection = false;
    $sections = manage_settings_get_sections ();
    if (count ($sections) > 0) {
      include 'sections.php';
    }

    include 'edit.php';
  } else {
    if ($action == 'save_name') {
      manage_settings_update_name ($id);
    } else if ($action == 'save') {
      manage_settings_update_from_post ();
    } else if ($action == 'delete') {
      manage_settings_delete ($id);
    }

    $sections = manage_settings_get_sections ();
    if (count ($sections) > 0) {
      include 'sections.php';
    } else {
      print ('<div><img height="4" src="'.config_get ('document-root').'/pics/clear.gif"></div>');
    }

    // Print the create form
    include 'create_form.php';
  }
?>
