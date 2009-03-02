<?php if ($__WT_contest_included__!='##WT_contents_Included##') {$__WT_contest_included__='##WT_contents_Included##';
  global $gateway_content_container;
  $gateway_content_container=nil;
  class CGContestContainer {
    var $data;
    var $cache=array ();
    var $lib_cache;

    function CheckTables () {
      if (!config_get ('check-database')) return;
      db_create_table_safe ('tester_contests', array (
        'id'       => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
        'name'     => 'TEXT',
        'lid'      => 'INT',
        'status'   => 'INT',
        'settings' => 'TEXT NOT NULL DEFAULT ""'
      ));
    }

    function CallContestStateUpdate ($c, $state) {
      if (!isset ($this->lib_cache[$c['lid']]))
        $this->lib_cache[$c['lid']]=WT_spawn_new_library ($c['lid']);
      $lib=$this->lib_cache[$c['lid']];
      $lib->PerformContestStateUpdate ($c, $state);
    }

    function ReNewOneStatus ($c, $ns=0) {
      $s=$c['settings'];

      if ($c['settings']['autostart'] && $c['status']!=2 && $c['status']!=1) {
        $date=$c['settings']['autostart.date'];
        $time=$c['settings']['autostart.time'];

        $y=preg_replace ('/^([0-9]+)\-([0-9]+)\-([0-9]+)$/', '\\1', $date);
        $m=preg_replace ('/^([0-9]+)\-([0-9]+)\-([0-9]+)$/', '\\2', $date);
        $d=preg_replace ('/^([0-9]+)\-([0-9]+)\-([0-9]+)$/', '\\3', $date);

        $h=preg_replace ('/^([0-9]+)\:([0-9]+)$/', '\\1', $time);
        $min=preg_replace ('/^([0-9]+)\:([0-9]+)$/', '\\2', $time);

        $deadline=mktime ($h, $min, 0, $m, $d, $y, 0);

        if ($deadline<=time ()) {
          $s=$c['settings'];
          $s['timestamp']=$deadline;

          if ($s)
            $ns['timestamp']=$deadline;

          db_update ('tester_contests', array ('status'=>1, 'settings'=>'"'.addslashes (serialize ($s)).'"'), '`id`='.$c['id']);
          $this->UpdateStatus ($c['id'], 1);
          $this->CallContestStateUpdate ($c, 1);

          // Update data in cache
          for ($i=0, $n=count ($this->data); $i<$n; $i++) 
            if ($this->data[$i]['id']==$c['id']) {
              $this->data[$i]['settings']['timestamp']=$readline;
              $this->data[$i]['status']=1;
            }

          $this->CallContestStateUpdate ($c, 1);

          return 1; // Running
        }
      }

      if ($c['status']==0) {
        $this->CallContestStateUpdate ($c, 0);
        return 0;
      }

      if ($s['duration']!=0 && $s['timestamp']+$s['duration']*60<=time ()) {
        if ($c['status']!=2) {
          db_update ('tester_contests', array ('status'=>2), '`id`='.$c['id']);
          $this->UpdateStatus ($c['id'], 2);
        }
        $this->CallContestStateUpdate ($c, 2);
        return 2; // Finished
      }

      $this->CallContestStateUpdate ($c, $c['status']);
      return $c['status'];
    }

    function FillData () {
      $this->data=array ();
      $q=db_select ('tester_contests', array ('*'), '', 'ORDER BY `lid`, `name`');
      while ($r=db_row ($q)) {
        $arr=$r;
        $s=$arr['settings']=$s=unserialize ($r['settings']);
        $arr['status']=$this->ReNewOneStatus ($arr, &$s);
        $arr['settings']=$s;
        $this->data[]=$arr;
      }
      $this->cache['ACCLIST']=array ();
    }

    function CGContestContainer () {
      $this->CheckTables ();
      $this->FillData ();
    }

    function Create ($name, $lid) {
      $gw=WT_spawn_new_gateway ();
      if (!$gw->GetAllowed ('CONTEST.CREATE')) return;
      if (trim ($name)=='') { add_info ('Имя создаваемого контеста не может быть пустым'); }
      if (db_count ('tester_contests', '`name`='.db_html_string ($name).' AND `lid`='.$lid)>0) { add_info ('Контест с таким именем уже существует в списке контестов указанной библиотеки.'); return; }
      $_POST=array ();
      $lib=WT_spawn_new_library ($lid);
      $params=array ();
      $lib->PerformCreation (&$params);
      db_insert ('tester_contests', array ('name'=>db_html_string ($name), 'lid'=>$lid, 'status'=>0, 'settings'=>db_string (serialize ($params))));
      $this->FillData ();
    }

    function Delete ($id) {
      $gw=WT_spawn_new_gateway ();
      if (!$gw->GetAllowed ('CONTEST.DELETE')) return;
      $lid=db_field_value ('tester_contests', 'lid', "`id`=$id");
      $c=WT_spawn_new_library ($lid);
      if ($c!=nil) $c->PerformContestDeletion ($id);
      db_delete ('tester_contests', "`id`=$id");
      $this->FillData ();
    }

    function CreateReceived () {
      return $this->Create (stripslashes (FormPOSTValue ('name', 'ContestData')), FormPOSTValue ('module', 'ContestData'));
    }

    function GetList () { return $this->data; }

    function ContestByField ($f, $v) {
      $data=$this->data;
      $n=count ($data);
      for ($i=0; $i<$n; $i++)
        if ($data[$i][$f]==$v) return $data[$i];
      return array ();
    }
    function ContestById ($v=-1) { global $WT_contest_id; if ($v<0) $v=$WT_contest_id; return $this->ContestByField ('id', $v); }

    function Save ($id) {
      $d=$this->ContestById ($id);
      $lib=WT_spawn_new_library ($d['lid']);
      if ($lib==nil) return true;
      $res=$lib->Contest_Save ($id);
      $this->FillData ();
      return $res;
    }

    function Stop    ($contest_id) { db_update ('tester_contests', array ('status'=>0), "`id`=$contest_id"); $this->FillData (); }
    function Start   ($contest_id) { db_update ('tester_contests', array ('status'=>1), "`id`=$contest_id"); $this->FillData (); }
    function ReStart ($contest_id) {
      $c=$this->ContestById ($contest_id);
      $s=$c['settings'];
      $s['timestamp']=time ();
      db_update ('tester_contests', array ('status'=>1, 'settings'=>db_string (serialize ($s))), "`id`=$contest_id");
      $this->FillData ();
    }

    function UpdateStatus ($id, $status) {
      $n=count ($this->data);
      for ($i=0; $i<$n; $i++)
        if ($this->data[$i]['id']==$id)
          $this->data[$i]['status']=$status;
    }

    function GetAccessibleList ($user_id) {
      $gw=WT_spawn_new_gateway ();
      if (isset ($this->cache['ACCLIST'][$user_id])) return $this->cache['ACCLIST'][$user_id];
      if ($gw->GetAllowed ('CONTEST.MANAGE')) $arr=$this->data; else {
        $q=db_query ('SELECT `tcg`.contest_id FROM `tester_contestgroup` AS `tcg`, `usergroup` AS `ug`, `tester_contests` AS `tc` '.
                  "WHERE (`ug`.`user_id`=$user_id) AND (`tcg`.`group_id`=`ug`.`group_id`) AND (`tc`.`id`=`tcg`.`contest_id`) ".
                  'GROUP BY `tcg`.`contest_id` '.
                  'ORDER BY `tc`.`lid`, `tc`.`name`');
        print db_error ();
        $arr=array ();
        while ($r=db_row ($q)) $arr[]=$this->ContestByID ($r['contest_id']);
      }
      $this->cache['ACCLIST'][$user_id]=$arr;
      return $arr;
    }

    function UpdateCompilers ($id, $arr) {
      $n=count ($this->data);
      for ($i=0; $i<$n; $i++)
        if ($this->data[$i]['id']==$id) {
          $this->data[$i]['settings']['compilers']=$arr;
          db_update ('tester_contests', array ('settings'=>db_string (serialize ($this->data[$i]['settings']))), "`id`=$id");
          break;
        }
    }
  }

  function WT_spawn_new_contest_container () {
    global $gateway_content_container;
    if ($gateway_content_container!=nil) return $gateway_content_container;
    $gateway_content_container=new CGContestContainer ();
    return $gateway_content_container;
  }

  function WT_draw_contest_manage_form ($id, $clear=false) {
    $contest=WT_contest_by_id ($id);
    $lib=WT_spawn_new_library ($contest['lid']);
    if ($lib==nil) return;
    $lib->Contest_Manager ($id, $clear);
  }

  function WT_contest_clear_manage_caption ($id) {
    $contest=WT_contest_by_id ($id);
    $lib=WT_spawn_new_library ($contest['lid']);
    if ($lib==nil) return '';
    return $lib->Contest_ClearManagerCaption ($id);
  }

  function WT_contest_by_id ($id=-1) { $cnt=WT_spawn_new_contest_container (); return $cnt->ContestById ($id); }

  function WT_contest_running ($id=-1) {
    $cnt=WT_spawn_new_contest_container ();
    $c=WT_contest_by_id ($id);
    return $cnt->ReNewOneStatus ($c)==1;
  }

  function WT_contest_finished ($id=-1) {
    $cnt=WT_spawn_new_contest_container ();
    $c=WT_contest_by_id ($id);
    return $cnt->ReNewOneStatus ($c)==2;
  }

  function WT_contest_status_string ($c) {
    $cnt=WT_spawn_new_contest_container ();
    $cnt->ReNewOneStatus ($c);
    $c=$cnt->ContestById ($c['id']);

    if ($c['status']==0) {
      if ($c['settings']['autostart']) {
        $date=$c['settings']['autostart.date'];
        $time=$c['settings']['autostart.time'];

        $y=preg_replace ('/^([0-9]+)\-([0-9]+)\-([0-9]+)$/', '\\1', $date);
        $m=preg_replace ('/^([0-9]+)\-([0-9]+)\-([0-9]+)$/', '\\2', $date);
        $d=preg_replace ('/^([0-9]+)\-([0-9]+)\-([0-9]+)$/', '\\3', $date);

        $h=preg_replace ('/^([0-9]+)\:([0-9]+)$/', '\\1', $time);
        $min=preg_replace ('/^([0-9]+)\:([0-9]+)$/', '\\2', $time);

        $deadline=mktime ($h, $min, 0, $m, $d, $y, 0);

        return 'Ожидание '.Timer ($deadline-time ());
      } else
        return 'Остановлен';
    }
    if ($c['status']==2) return 'Завершен';
    if ($c['settings']['duration']) {
      // TODO: Add time formation here
      return 'Осталось '.Timer($c['settings']['timestamp']+$c['settings']['duration']*60-time ());
    }
    return '&nbsp;';
  }
}
?>
