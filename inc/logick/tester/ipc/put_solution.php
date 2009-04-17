<?php if ($_WT_ipc_put_solution_included_!='###WT_IPC_PutSolution_Inclided###') { $_WT_ipc_put_solution_included_='###WT_IPC_PutSolution_Inclided###';

  function WT_PutSolution () {
    global $id, $lid, $ERRORS, $POINTS, $XPFS;

    $optional_params=array ('REPORT');

    $update_params=array ('COMPILER_MESSAGES', 'TESTS');

    if (!WT_IPC_CheckLogin ()) return;

    if (!isset ($id) || !isset ($lid)) { print ('Void filename for WT_PutSOlution'); return; }

    $r=db_row_value ('tester_solutions', "`id`=$id AND `lid`=$lid");
    $p=unserialize ($r['parameters']);

    for ($i=0; $i<count ($update_params); $i++)
      if (isset ($_GET[$update_params[$i]]))
        $p[$update_params[$i]]=stripslashes ($_GET[$update_params[$i]]);

    if ($POINTS=='') $POINTS=0;

    $n=count ($optional_params);
    for ($i=0; $i<$n; $i++)
      $p[$optional_params[$i]]=stripslashes ($GLOBALS[$optional_params[$i]]);

    unset ($p['force_status']);

    $data=array ();

    if (isset ($_GET['SOLUTION_OUTPUT'])) {
      $data['outputs']=stripslashes ($_GET['SOLUTION_OUTPUT']);
    }

    if (isset ($_GET['CHECKER_OUTPUT'])) {
      $data['checker_outputs']=stripslashes ($_GET['CHECKER_OUTPUT']);
    }

    if (count ($data) > 0) {
      $path='/tester/testing/';
      $XPFS->CreateDirWithParents ($path);
      $XPFS->removeItem ($path.'/'.$id);
      $XPFS->createFile ($path, $id, 0, db_pack ($data));
    }

    db_update ('tester_solutions', array ('status'=>2, 'points'=>$POINTS, 'errors'=>db_string ($ERRORS),
      'parameters'=>db_string (serialize ($p))),"`id`=$id AND `lid`=$lid");
  }

  ipc_register_function ('put_solution', WT_PutSolution);
}
?>
