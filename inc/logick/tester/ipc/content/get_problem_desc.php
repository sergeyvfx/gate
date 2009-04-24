<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Get problem's description
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

  if ($_WT_ipc_get_get_problem_desc_included_ !=
      '###WT_IPC_GetProblemDesc_Inclided###') {

    $_WT_ipc_get_problem_desc_included_ = '###WT_IPC_GetProblemDesc_Inclided###';

    function WT_GetProblemDesc () {
      global $id, $cid, $backlink;

      if ($id == '' || $cid == '') {
        return;
      }

      $gw = WT_spawn_new_gateway ();
      $r = $gw->current_lib->IPC_Problem_DescriptionForm ($id, $cid, $backlink);

      if ($r != '') {
        print (setvars ($r));
      } else {
        print ('&nbsp;');
      }
    }

    ipc_register_function ('get_problem_desc', WT_GetProblemDesc);
  }
?>
