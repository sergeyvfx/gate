<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Entry point to WebTester client inerface
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  global $ipc;

  include '../globals.php';

  /* Some optimization stuff */
  $serv_ipc_procs='#get_task_list#get_task#delete_task#get_checker#get_problem#put_checker#put_problem#put_solution#reset_status#restore_task';
  if ($ipc != '') {
    $n = count ($serv_ipc_procs);
    if (strpos ($serv_ipc_procs, $ipc) > 0) {
      /* Execute IPC command */
      include $DOCUMENT_ROOT.'/inc/stuff/ipc_executor.php';
      include $DOCUMENT_ROOT.'/inc/logick/tester/config/config.php';
      include $DOCUMENT_ROOT.'/inc/logick/tester/config/ipc.php';
      include $DOCUMENT_ROOT.'/inc/logick/tester/ipc.php';
      include $DOCUMENT_ROOT.'/inc/stuff/db_pack.php';
      ipc_exec ($ipc);
      die;
    }
  }

  include $DOCUMENT_ROOT.'/inc/logick/tester/include.php';
  Main (dirname ($PHP_SELF), true);
?>
