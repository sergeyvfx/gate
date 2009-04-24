<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main script for content administration
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

  global $DOCUMENT_ROOT, $action, $id, $pid;
  include $DOCUMENT_ROOT.'/admin/inc/menu.php';
  include '../menu.php';
  $manage_menu->SetActive ('control');
  $datacontrol_menu->SetActive ('content');

  if ($action == 'create') {
    wiki_content_create_received ();
  }

  if ($action == 'setparent') {
    wiki_content_set_parent ($id, $pid);
  }

  // Printing da page
  $manage_menu->Draw ();
  $datacontrol_menu->Draw ();

  print ('${information}');

  if ($action == 'edit') {
    include 'edit.php';
  } else if ($action == 'editor') {
    include 'editor.php';
  } else if ($action == 'editroot') {
    include 'editroot.php';
  } else {
    if ($action == 'save') {
      if ($id <> 1) {
        wiki_content_update_received ($id);
      } else {
        $c = wiki_spawn_content (1);
        $c->security->ReceiveData ();
        $c->Update ();
      }
    } else if ($action == 'delete') {
      wiki_content_delete ($id);
    } else if ($action == 'up') {
      wiki_content_up ($id);
    } else if ($action == 'down') {
      wiki_content_down ($id);
    }

    $list=wiki_content_get_list ();
    include 'list.php';

    /* Print the create form */
    include 'create_form.php';
  }
?>
