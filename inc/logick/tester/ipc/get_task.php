<?php if ($_WT_ipc_get_task_included_!='###WT_IPC_GetTask_Inclided###') { $_WT_ipc_get_task_included_='###WT_IPC_GetTask_Inclided###';

  function WT_GetTask () {
    global $id, $lid;

    if (!WT_IPC_CheckLogin ()) return;

    if (!isset ($id) || !isset ($lid)) { print 'Void filename for WT_GetTask()'; return; }

    $solution=db_row (db_select ('tester_solutions', array ('*'), "`id`=$id"));
    if ($solution['id']=='') return;
    $contest=db_row (db_select ('tester_contests', array ('*'), '`id`='.$solution['contest_id'].' AND `lid`='.$lid));
    $problem=db_row (db_select ('tester_problems', array ('*'), '`id`='.$solution['problem_id'].' AND `lid`='.$lid));

    $contest['settings']=unserialize ($contest['settings']);
    $solution['parameters']=unserialize ($solution['parameters']);

    $arr=array ();

    // Solution's based settings
    $arr['PROBLEMID']   = $solution['problem_id'];

    $arr['COMPILERID']  = $solution['parameters']['compiler_id'];

    $arr['SOURCE']      = $solution['parameters']['src'];

    if ($contest['settings']['rules']==0) $arr['ACM']='TRUE'; else $arr['ACM']='FALSE';

    // Problem's pased settings    
    $prpars=unserialize ($problem['settings']);
    $arr['TIMELIMIT']   = $prpars['timelimit'];
    $arr['MEMORYLIMIT'] = $prpars['memorylimit'];
    $arr['INPUTFILE']   = $prpars['input'];
    $arr['OUTPUTFILE']  = $prpars['output'];
    $arr['TESTS']       = $prpars['tests'];
    $arr['BONUS']       = $prpars['bonus'];

    print db_pack ($arr);
  }

  ipc_register_function ('get_task',      WT_GetTask);
}
?>
