<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Hooks implementations
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

  if ($_WT_hook_included_ != '##WT_Hook_Included##') {
    $_WT_hook_included_ = '##WT_Hook_Included##';

    function WT_on_user_delete ($user_id) {
      /* util.php is not included set, so we need this stupid code here */
      global $XPFS;

      $q = db_select ('tester_solutions', array ('id'), "`user_id`=$user_id");
      while ($r = db_row ($q)) {
        $XPFS->removeItem ('/tester/testing/'.$r['id']);
      }

      db_delete ('tester_solutions', "`user_id`=$user_id");
    }

    function WT_on_group_delete ($group_id) {
      db_delete ('tester_contestgroup', "`group_id`=$group_id");
      db_delete ('tester_contestjudge', "`group_id`=$group_id");
    }

    hook_register ('CORE.Security.OnUserDelete',  WT_on_user_delete);
    hook_register ('CORE.Security.OnGroupDelete', WT_on_group_delete);
  }
?>
