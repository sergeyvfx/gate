<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Remove tag from problem
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

  if ($_WT_ipc_cmd_problem_rm_tag_included_ !=
      '###WT_IPC_ProblemRmTag_Inclided###') {
    $_WT_ipc_cmd_problem_rm_tag_included_ =
      '###WT_IPC_ProblemRmTag_Inclided###';

    function WT_ProblemRemoveTag () {
      $id = $_POST['id'];
      $tag = trim ($_POST['tag']);

      if (!isnumber ($id) || $tag == '') {
        print ('-ERR');
        return;
      }

      $tag_id = db_field_value ('tester_tags_dict', 'id',
                                '`tag`="' . addslashes ($tag) . '"');
      if (isnumber ($tag_id)) {
        db_delete ('tester_problem_tags', "`problem_id`=$id AND `tag_id`=$tag_id");
      }

      print ('+OK');
    }

    ipc_register_function ('cmd_problem_rm_tag', WT_ProblemRemoveTag);
  }
?>
