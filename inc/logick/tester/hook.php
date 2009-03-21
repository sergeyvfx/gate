<?php if ($_WT_hook_included_!='##WT_Hook_Included##') {$_WT_hook_included_='##WT_Hook_Included##';

  function WT_on_user_delete ($user_id) {
    /* util.php is not included yset, so we need this stupid code here */
    global $XPFS;
    $q=db_select ('tester_solutions', array ('id'), "`user_id`=$user_id");
    while ($r=db_row ($q)) $XPFS->removeItem ('/tester/testing/'.$r['id']);

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