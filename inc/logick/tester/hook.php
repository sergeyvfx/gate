<?php if ($_WT_hook_included_!='##WT_Hook_Included##') {$_WT_hook_included_='##WT_Hook_Included##';

  function WT_on_user_delete ($user_id) {
     WT_delete_solution_from_xpfs ("`user_id`=$user_id");
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