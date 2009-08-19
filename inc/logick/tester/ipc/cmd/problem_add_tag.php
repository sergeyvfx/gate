<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Add tag to problem
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  global $IFACE;

  if ($IFACE != "SPAWNING NEW IFACE" || $_GET['IFACE'] != '') {
    print ('HACKERS?');
    die;
  }

  if ($_WT_ipc_cmd_problem_add_tag_included_ !=
      '###WT_IPC_ProblemAddTag_Inclided###') {
    $_WT_ipc_cmd_problem_add_tag_included_ =
      '###WT_IPC_ProblemAddTag_Inclided###';

    function WT_ProblemAddTag () {
      $id = $_POST['id'];
      $tag = trim ($_POST['tag']);

      if (!isnumber ($id) || $tag == '') {
        print ('-ERR');
        return;
      }

      $gw = WT_spawn_new_gateway ();
      if ($gw->current_lib->IPC_Problem_AddTag ($id, $tag)) {
        print ('+OK');
      } else {
        print ('-ERR');
      }
    }

    ipc_register_function ('cmd_problem_add_tag', WT_ProblemAddTag);
  }
?>
