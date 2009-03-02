<?php if ($__CGMVirtual_Included__!='##CGMVirtual_Included##') {$__CGMVirtual_Included__='##CGMVirtual_Included##';
  class CGMVirtual extends CVirtual {
    var $module;
    function CGMVirtual ($gw) { $this->SetClassName ('CGMVirtual'); $this->gateway=$gw; }

    function SetModuleName ($v) { $this->module=$v; }
    function GetModuleName ()   { return $this->module; }

    function PerformCreation        ($params) {  }
    function PerformContestDeletion ($id)     {  }
    function PerformContestStateUpdate ($contest, $state) {  }
    function Contest_Manager ($id, $clear=false) {  }
    function Contest_Save    ($id, $clear=false) { return true; }

    function COntest_ClearManagerCaption () { return ''; }

    function CPrint   ($t) { $this->gateway->CPrint ($t); }
    function CPrintLn ($t) { $this->gateway->CPrintLn ($t); }
    function GetContent () { return $this->gateway->GetContent (); }

    function Template ($tpl, $vars=array ()) { return tpl ('front/tester/modules/'.$this->GetModuleName ().'/'.$tpl, $vars); }

    function InsertTemplate ($tpl, $vars=array ()) {
      $this->CPrintLn ($this->Template ($tpl, $vars));
    }

    function InitIface () {  }

    function GetAllowed ($action) { return $this->gateway->GetAllowed ($action); }

    function GetUsersAtContest ($contest_id=-1) {
      global $WT_contest_id;
      if ($contest_id<0) $contest_id=$WT_contest_id;
      return arr_from_query ('SELECT `u`.`id`, `u`.`name` FROM `user` AS `u`, `usergroup` AS `ug`, `tester_contestgroup` AS `tcg` '.
        'WHERE (`tcg`.`contest_id`='.$contest_id.') AND (`ug`.`group_id`=`tcg`.`group_id`) AND (`u`.`id`=`ug`.`user_id`) GROUP BY `u`.`id` ORDER BY `u`.`name`'
      );
    }

    function GetJudgesAtContest ($contest_id=-1) {
      global $WT_contest_id;
      if ($contest_id<0) $contest_id=$WT_contest_id;
      return arr_from_query ('SELECT `u`.`id`, `u`.`name` FROM `user` AS `u`, `usergroup` AS `ug`, `tester_judgegroup` AS `tjg` '.
        'WHERE (`tjg`.`contest_id`='.$contest_id.') AND (`ug`.`group_id`=`tjg`.`group_id`) AND (`u`.`id`=`ug`.`user_id`) GROUP BY `u`.`id` ORDER BY `u`.`name`'
      );
    }

    function GetProblemsAtContest ($contest_id=-1) {
      global $WT_contest_id;
      if ($contest_id<0) $contest_id=$WT_contest_id;
      return arr_from_query ('SELECT * FROM `tester_tasks` WHERE `contest_id`='.$contest_id.' ORDER BY `letter`');
    }

    function UpdateCompilers ($id, $arr) {
      $ccnt=WT_spawn_new_contest_container ();
      // For da correct caching
      $ccnt->UpdateCompilers ($id, $arr);
    }
    
    function AppendQuickLink ($cpt, $url) { $this->gateway->AppendQuickLink ($cpt, $url); }

    function Subnav_Info () { return ''; }
  }
}
?>
