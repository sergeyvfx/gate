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

      $tag_id=db_field_value ('tester_tags_dict', 'id',
                              '`tag`="' . addslashes ($tag) . '"');
      if (!isnumber ($tag_id)) {
        db_insert ('tester_tags_dict', array ('tag' => db_string ($tag)));
        $tag_id = db_last_insert ();
      }

      if (db_count ('tester_problem_tags',
                    "`problem_id`=$id AND `tag_id`=$tag_id") == 0) {
        db_insert ('tester_problem_tags', array ('problem_id' => $id,
                                                 'tag_id'     => $tag_id));
      }

      print ('+OK');
    }

    ipc_register_function ('cmd_problem_add_tag', WT_ProblemAddTag);
  }
?>
