<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Rejudge problem
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

  if ($_WT_ipc_cmd_problem_rejudge_included_ !=
      '###WT_IPC_ProblemRejudge_Inclided###') {
    $_WT_ipc_cmd_problem_rejudge_included_ = '###WT_IPC_ProblemRejudge_Inclided###';

    function WT_ProblemRejudge () {
      global $id;

      if ($id == '') {
        return;
      }

      $gw = WT_spawn_new_gateway ();
      if ($gw->current_lib->IPC_Problem_Rejudge ($id)) {
        print ('+OK');
      } else {
        print ('-ERR');
      }
    }

    ipc_register_function ('cmd_problem_rejudge', WT_ProblemRejudge);
  }
?>
