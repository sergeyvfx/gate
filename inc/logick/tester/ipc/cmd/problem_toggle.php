<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Toggle problem's usage in contest
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

  if ($_WT_ipc_cmd_toggle_prusage_included_ !=
      '###WT_IPC_ToggleProblemUsage_Inclided###') {
    $_WT_ipc_cmd_toggle_prusage_included_ =
      '###WT_IPC_ToggleProblemUsage_Inclided###';

    function WT_ToggleProblemUsage () {
      global $cid, $id;

      if ($id == '' || $cid == '') {
        return;
      }

      $gw = WT_spawn_new_gateway ();
      if ($gw->current_lib->IPC_Contest_ToggleDisableProblem ($cid, $id)) {
        print ('+OK');
      } else {
        print ('-ERR');
      }
    }

    ipc_register_function ('cmd_problem_toggle_usage', WT_ToggleProblemUsage);
  }
?>
