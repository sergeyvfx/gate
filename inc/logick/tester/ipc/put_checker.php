<?php if ($_WT_ipc_put_checker_included_!='###WT_IPC_PutChecker_Inclided###') { $_WT_ipc_put_checker_included_='###WT_IPC_PutChecker_Inclided###';

  function WT_PutChecker () {
    global $id, $err, $desc;

    if (!WT_IPC_CheckLogin ()) return;

    if ($id=='') {
      print ('Void filename for WT_PutChecker()');
      return;
    }

    $data=db_row_value ('tester_checkers', "`id`=$id");
    $s=unserialize ($data['settings']);

    $s['ERR']=$err;
    $s['DESC']=$desc;
    
    db_update ('tester_checkers', array ('uploaded'=>'TRUE', 'settings'=>db_string (serialize ($s))), "`id`=$id");
  }

  ipc_register_function ('put_checker',      WT_PutChecker);
}
?>
