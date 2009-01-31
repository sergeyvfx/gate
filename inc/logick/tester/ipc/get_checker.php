<?php if ($_WT_ipc_get_checker_included_!='###WT_IPC_GetChecker_Inclided###') { $_WT_ipc_get_checker_included_='###WT_IPC_GetChecker_Inclided###';

  function WT_GetChecker () {
    if (!WT_IPC_CheckLogin ()) return;

    $r=db_row (db_select ('tester_checkers', array ('*'), '`uploaded`=FALSE', 'LIMIT 1'));
    if ($r) {
      $s=unserialize ($r['settings']);
      $arr=array ('ID'=>$r['id'], 'SRC'=>$s['src'], 'COMPILERID'=>$s['compiler_id']);
      print (db_pack ($arr));
    }
  }

  ipc_register_function ('get_checker',      WT_GetChecker);
}
?>
