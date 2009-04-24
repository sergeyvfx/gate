<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Tests contest class
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

  if ($__CGMTesting_Included__ != '##CGMTesting_Included##') {
    $__CGMTesting_Included__ = '##CGMTesting_Included##';

    $WT_TESTING_Cache = array ();

    class CGMTestingATVirtual extends CVirtual {
      function CGMTestingATVirtual () {
        $this->SetClassName ('CGMTestingATVirtual');
      }

      function Draw ($s, $assoc_data, $task_num, $form_prefix = '') { }
      function Collect ($d, $assoc_data, $task_num, $form_prefix = '') {  }
      function Check ($data, $user_data) {  }
      function GetUserAnswerText    ($pr, $ud) { return ''; }
      function GetCorrectAnswerText ($pr) { return ''; }
    }

    class CGMTestingATCheckbox extends CGMTestingATVirtual {
      function CGMTestingATCheckbox () {
        $this->SetClassName ('CGMTestingATCheckBox');
      }

      function Draw ($s, $assoc_data, $task_num, $form_prefix = '') {
        for ($i = 0; $i < $s['anscount']; $i++) {
          $j = $assoc_data['ans'][$i];
          println ('<div class="ans"><input type="checkbox" class="cb" name="'.
                 $form_prefix.'_task_'.$task_num.'_ans_'.$i.'" value="1">'.
                 '<span class="text">'.$s['ans'][$j]['text'].'</span></div>');
        }
      }

      function Collect ($d, $assoc_data, $task_num, $form_prefix = '') {
        $user_ans = array ();
        $s = $d['settings'];

        for ($i = 0; $i < $s['anscount']; $i++) {
          $j = $assoc_data['ans'][$i];
          $name = $form_prefix.'_task_'.$task_num.'_ans_'.$i;
          if ($_POST[$name]) {
            $user_ans[$j] = 1;
          } else {
            $user_ans[$j] = 0;
          }
        }

        return $user_ans;
      }

      function Check ($data, $user_data) {
        for ($i = 0; $i < $data['anscount']; $i++) {
          $c = $data['ans'][$i]['correct'];
          if ($c && !$user_data[$i] || !$c && $user_data[$i]) {
            return 0;
          }
        }

        return 1;
      }

      function GetUserAnswerText ($pr, $ud) {
        $text = '';
        $anss = $pr['settings']['ans'];

        for ($i = 0, $n = count ($ud); $i < $n; $i++) {
          $text .= (($text!='')?(', '):('')).$anss[$ud[$i]]['text'];
        }

        return $text;
      }

      function GetCorrectAnswerText ($pr) {
        $text = '';
        $s = $pr['settings'];

        for ($i = 0; $i < $s['anscount']; $i++) {
          $a = $s['ans'][$i];
          if ($a['correct']) {
            $text .= (($text != '') ? (', ') : ('')).$a['text'];
          }
        }

        return $text;
      }
    }

    class CGMTestingATRadiobutton extends CGMTestingATVirtual {
      function CGMTestingATRadiobutton () {
        $this->SetClassName ('CGMTestingATRadiobutton');
      }

      function Draw ($s, $assoc_data, $task_num, $form_prefix = '') {
        for ($i = 0; $i < $s['anscount']; $i++) {
          $j = $assoc_data['ans'][$i];
          println ('<div class="ans"><input type="radio" class="radio" name="'.
                 $form_prefix.'_task_'.$task_num.'_ans" value="'.$i.'" group="1">'.
                 '<span class="text">'.$s['ans'][$j]['text'].'</span></div>');
        }
      }

      function Collect ($d, $assoc_data, $task_num, $form_prefix = '') {
        $user_ans = array ();
        $s = $d['settings'];
      
        $name = $form_prefix.'_task_'.$task_num.'_ans';
        $t = $_POST[$name];

        return $assoc_data['ans'][$t];
      }

      function Check ($data, $user_data) {
        if ($data['ans'][$user_data]['correct']) {
          return 1;
        }

        return 0;
      }

      function GetUserAnswerText ($pr, $ud) {
        return $pr['settings']['ans'][$ud]['text'];
      }

      function GetCorrectAnswerText ($pr) {
        $text = '';
        $s = $pr['settings'];

        for ($i = 0; $i < $s['anscount']; $i++) {
          $a = $s['ans'][$i];

          if ($a['correct']) {
            $text = (($text != '') ? (', ') : ('')).$a['text'];
          }
        }

        return $text;
      }
    }

    class CGMTestingATString extends CGMTestingATVirtual {
      function CGMTestingATString () {
        $this->SetClassName ('CGMTestingATCheckBox');
      }

      function Draw ($s, $assoc_data, $task_num, $form_prefix = '') {
        print ('<div class="ans"><input type="text" class="block txt" name="'.
               $form_prefix.'_task_'.$task_num.'" value=""></div>');
      }

      function Collect ($d, $assoc_data, $task_num, $form_prefix = '') {
        $name = $form_prefix.'_task_'.$task_num;

        return stripslashes ($_POST[$name]);
      }

      function Check ($data, $user_data) {
        for ($i = 0; $i < $data['anscount']; $i++) {
          $c = $data['ans'][$i]['correct'];

          if ($c && $user_data == $data['ans'][$i]['text']) {
            return 1;
          }
        }

        return 0;
      }

      function GetUserAnswerText ($pr, $ud) {
        return $ud;
      }

      function GetCorrectAnswerText ($pr) {
        $text = '';
        $s = $pr['settings'];

        for ($i = 0; $i < $s['anscount']; $i++) {
          $a = $s['ans'][$i];
          if ($a['correct']) {
            $text .= (($text != '') ? (' | ') : ('')).$a['text'];
          }
        }

        return $text;
      }
    }

    //////
    //

    class CGMTesting extends CGMVirtual {
      var $anstypes;
  
      function CreateTables () {
        if (config_get ('check-database')) {
          if (!db_table_exists ('tester_categories')) {
            db_create_table_safe ('tester_categories', array (
                                    'id'               => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                    'order'            => 'INT',
                                    'contest_id'       => 'INT',
                                    'name'             => 'TEXT'
                                  ));
          }
        }
      }

      function CGMTesting ($gw) {
        CGMVirtual::CGMVirtual ($gw);

        $this->anstypes=array ();
        $this->AnsType_Register ('CGMTestingATRadiobutton', 'Один из многих');
        $this->AnsType_Register ('CGMTestingATCheckbox',    'Многие из многих');
        $this->AnsType_Register ('CGMTestingATString',      'Однострочный');

        $this->CreateTables ();
        $this->SetClassName ('CGMTesting');
        $this->SetModuleName ('Testing');

        $this->gateway->AppendLIBHandler (1, 'testing',   'PAGE_Testing');
        $this->gateway->AppendLIBHandler (1, 'solutions', 'PAGE_Solutions');
        $this->gateway->AppendLIBHandler (1, 'status',    'PAGE_Status');
      }

      function AnsType_Register ($className, $title) {
        $this->anstypes[] = array ('ClassName' => $className, 'title' => $title);
      }

      function AnsType_GetRegistered () {
        return $this->anstypes;
      }

      function InitIface () {
        global $WT_contest_id;
        $judge = $this->IsContestJudge ();
        $running = WT_contest_running ();
        $manage = $this->GetAllowed ('CONTEST.MANAGE');
        $started = WT_contest_running ($WT_contest_id);
        $finished = WT_contest_finished ($WT_contest_id);

        if (($judge || $running || $manage) && ($this->Test_Obtained () ||
                                                $this->Test_CanObtain ())) {
          $this->gateway->AppendMainMenuItem ('Тестирование',
                                              '.?page=testing', 'testing');
        }

        if (($started || $finished || $manage) &&
            $this->GetUserSolutionsCount ($WT_contest_id, user_id ())) {
          $this->gateway->AppendMainMenuItem ('Статус', '.?page=status', 'status');
        }

        if ($this->GetAllowed ('SOLUTIONS.MANAGE') &&
            $this->GetAllSolutionsCount ($WT_contest_id))
          $this->gateway->AppendMainMenuItem ('Решения участников',
                                              '.?page=solutions',
                                              'solutions');
      }

      function DefaultContestSettings () {
        return array ('timestamp' => time (), 'duration' => 0, 'trycount' => 0,
                      'groups' => array (), 'judges' => array (),
                      'autostart' => false, 'timelimit' => 0);
      }

      function PerformCreation ($params) {
        $params = $this->DefaultContestSettings ();
      }

      function Contest_GetUserGroup_Iterator ($id, $table) {
        if ($id < 0) {
          $id = $WT_contest_id;
        }

        $q = db_select ($table, array ('group_id'), "`contest_id`=$id");
        $arr = array ();

        while ($r = db_row ($q)) {
          $arr[] = $r['group_id'];
        }

        return $arr;
      }
 
      function Contest_GetUserGroup  ($id = -1) {
        return $this->Contest_GetUserGroup_Iterator ($id, 'tester_contestgroup');
      }

      function Contest_GetJudgeGroup ($id = -1) {
        return $this->Contest_GetUserGroup_Iterator ($id, 'tester_judgegroup');
      }

      function Contest_UpdateRecievedGroupUsed_Iterator ($id, $name, $table) {
        global $WT_contest_id;

        if ($id < 0) {
          $id = $WT_contest_id;
        }

        $list = new CVCAppendingList ();
        $list->Init ($name);
        $list->SetItems ($groups);
        $list->ReceiveItemsUsed ();

        $glist = $list->GetItemsUsed ();
        $n = count ($glist);

        db_delete ($table, "`contest_id`=$id");

        for ($i = 0; $i < $n; $i++) {
          $gid = $glist[$i];
          if (db_count ($table, "`contest_id`=$id AND `group_id`=$gid") > 0) {
            continue;
          }

          db_insert ($table, array ('contest_id' => $id, 'group_id' => $gid));
        }
      }

      function Contest_UpdateRecievedGroupUsed ($id = -1) {
        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $this->Contest_UpdateRecievedGroupUsed_Iterator ($id, 'usergroup',
                                                         'tester_contestgroup');
        $this->Contest_UpdateRecievedGroupUsed_Iterator ($id, 'judgegroup',
                                                         'tester_judgegroup');
      }

      function Contest_ClearManagerCaption () {
        global $act;

        if ($act == 'showaddproblem') {
          return 'Добавление задания в категорию';
        }
      }

      function Contest_Manager ($id, $clear=false) {
        global $CORE;

        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $CORE->AddStyle ('testing');
        content_url_var_push_global ('action');
        content_url_var_push ('id', $id);
        content_url_var_push_global ('cman');

        $this->Contest_ActionHandler ();
        $contest = WT_contest_by_id ($id);

        if ($clear) {
          global $act;
          if ($act == 'showaddproblem') {
            $this->InsertTemplate ('problem.form',
                                   array ('data' => array (),
                                          'lib' => $this,
                                          'act' => 'createproblem'));
          } else if ($act == 'editproblem') {
            global $uid;
            $r = db_row_value ('tester_problems', '`id`='.$uid);
            $r['settings'] = unserialize ($r['settings']);
            $this->InsertTemplate ('problem.form',
                                   array ('data' => $r, 'lib' => $this,
                                          'act' => 'saveproblem'));
          }
        } else {
          $this->InsertTemplate ('contest.edit', array ('data' => $contest,
                                                        'lib'=>$this));
        }
      }

      function Contest_Save ($id, $clear = false) {
        global $noarchive;

        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $contest = WT_contest_by_id ($id);
        $name = FormPOSTValue ('name', 'ContestSettings');

        if (trim ($name)=='') {
          add_info ('Название контеста не может быть пустым.');
          return false;
        }

        $settings=$contest['settings'];
        $settings['duration']   = atoi (FormPOSTValue ('duration',   'ContestSettings'));
        $settings['timelimit']  = atoi (FormPOSTValue ('timelimit',  'ContestSettings'));
        $settings['trycount']   = atoi (FormPOSTValue ('trycount',   'ContestSettings'));
        $settings['autostart']  = $_POST['contest_autostart'];

        if ($settings['autostart']) {
          $date=new CDCDate ();
          $date->Init ();
          $date->ReceiveValue ('contest_autostart_date');
          $settings['autostart.date'] = $date->GetValue ();

          $time=new CDCDate ();
          $time->Init ();
          $time->ReceiveValue ('contest_autostart_time');
          $settings['autostart.time'] = $time->GetValue ();
        }

        $update = array ('name' => db_html_string ($name),
                         'settings' => db_string (serialize ($settings)));

        db_update ('tester_contests', $update, "`id`=$id");

        return true;
      }
    
      function Contest_ActionHandler () {
        global $act, $uid, $id, $pid, $catid;

        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $full = content_url_get_full ();

        if ($act == 'savegroups') {
          $this->Contest_UpdateRecievedGroupUsed ($id);
        } else if ($act == 'savecompilers') {
          $this->Contest_UpdateRecievedCompilers ($id);
        } else if ($act == 'addcat') {
          $this->Categories_Add ($id);
        } else if ($act == 'delcat') {
          $this->Categories_Del ($uid);
        } else if ($act == 'upcat') {
          $this->Categories_Up ($uid);
        } else if ($act == 'downcat') {
          $this->Categories_Down ($uid);
        } else if ($act == 'createproblem') {
          $this->Problems_Create ($id, $catid);
        } else if ($act == 'delproblem') {
          $this->Problems_Delete ($uid);
        } else if ($act == 'saveproblem') {
          $this->Problems_Save ($uid);
        }
      } 

      function Contest_UpdateRecievedCompilers ($id = -1) {
        global $WT_contest_id;

        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        if ($id < 0) {
          $id = $WT_contest_id;
        }

        $list = WT_compiler_list ();
        $n = count ($list);
        $arr = array ();

        for ($i = 0; $i < $n; $i++) {
          if ($_POST[$list[$i]['id']]) {
            $arr[$list[$i]['id']] = 1;
          }
        }
      
        $this->UpdateCompilers ($id, $arr);
      }

      function Categories_Get ($contest_id = -1) {
        global $WT_contest_id;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        return arr_from_query ('SELECT * FROM `tester_categories` '.
                               'WHERE `contest_id`='.$contest_id.
                               ' ORDER BY `order`');
      }

      function Categories_Add ($contest_id = -1) {
        global $name, $WT_contest_id;

        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        $name = stripslashes ($name);

        if (trim ($name) == '') {
          add_info ('Имя категории не может быть пустым.');
          return false;
        }

        if (db_count ('tester_categories', '`name`='.db_string ($name))) {
          add_info ('Категория с таким именем уже существует.');
          return false;
        }

        $order = db_max ('tester_categories', 'order',
                         '`contest_id`='.$WT_contest_id) + 1;

        db_insert ('tester_categories', array ('name' => db_string (htmlspecialchars ($name)),
                                               'contest_id' => $contest_id,
                                               'order' => $order));

        return true;
      }

      function Categories_Del ($id) {
        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $r = db_row_value ('tester_categories', '`id`='.$id);

        if ($r['id'] == '') {
          return;
        }

        db_delete ('tester_categories', '`id`='.$id);
        db_update ('tester_categories', array ('order' => '`order`-1'),
                   '(`contest_id`='.$r['contest_id'].') '.
                   'AND (`order`>'.$r['order'].')');

        // TODO:
        // Add deleting of tasks here
      }

      function Categories_Up ($id) {
        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $r = db_row_value ('tester_categories', '`id`='.$id);
        db_move_up ('tester_categories', $id, '`contest_id`='.$r['contest_id']);
      }

      function Categories_Down ($id) {
        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $r = db_row_value ('tester_categories', '`id`='.$id);
        db_move_down   ('tester_categories', $id, '`contest_id`='.$r['contest_id']);
      }

      ////
      //

      function Problems_Create ($contest_id, $catid) {
        global $WT_contest_id;
        global $anstype, $anscount;

        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        $name = stripslashes (FormPOSTValue ('name', 'ProblemSettings'));
        $desc = iframe_accept_content ('desc', '');
        $s = array ();

        $s['anstype'] = $anstype;
        $s['anscount'] = $anscount;
        $ans = array ();

        for ($i = 0; $i < $anscount; $i++) {
          $ans[$i] = array ('text' => htmlspecialchars (stripslashes ($_POST['ans_text_'.$i])),
                            'correct' => (($_POST['ans_correct_'.$i]) ? (1) : (0)),
                            'static' => (($_POST['ans_static_'.$i])?(1):(0)));
        }

        $s['ans'] = $ans;
        db_insert ('tester_problems', array ('lid' => 1, 'name' => db_string ($name),
                                             'description' => db_string ($desc),
                                             'settings' => db_string (serialize ($s)),
                                             'uploaded' => true));
        $pid = db_last_insert ();
        db_insert ('tester_tasks', array ('contest_id' => $contest_id,
                                          'problem_id' => $pid,
                                          'catid' => $catid));
      }

      function Problems_Save ($id) {
        global $anstype, $anscount;

        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $name = stripslashes (FormPOSTValue ('name', 'ProblemSettings'));

        $r = db_row_value ('tester_problems', '`id`='.$id);

        $desc = iframe_accept_content ('desc', $r['description']);
        $s = array ();

        $s['anstype'] = $anstype;
        $s['anscount'] = $anscount;
        $ans = array ();

        for ($i = 0; $i < $anscount; $i++) {
          $ans[$i] = array ('text' => htmlspecialchars (stripslashes ($_POST['ans_text_'.$i])),
                            'correct' => (($_POST['ans_correct_'.$i])?(1):(0)),
                            'static' => (($_POST['ans_static_'.$i])?(1):(0)));
        }
        $s['ans'] = $ans;

        db_update ('tester_problems', array ('name' => db_string ($name),
                                             'description' => db_string ($desc),
                                             'settings' => db_string (serialize ($s))),
                   '`id`='.$id);
      }

      function Problems_GetAtCat ($catid) {
        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $q = db_query ('SELECT `tp`.* FROM `tester_problems` AS `tp`, '.
                       '`tester_tasks` AS `tt` '.
                       'WHERE (`tp`.`id`=`tt`.`problem_id`) '.
                       'AND (`tt`.`catid`='.$catid.') ORDER BY `tp`.`name`');
        $arr = array ();

        while ($r = db_row ($q)) {
          $t = $r;
          $t['settings'] = unserialize ($t['settings']);
          $arr[] = $t;
        }

        return $arr;
      }

      function Problems_Delete ($id) {
        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $r = db_row_value ('tester_problems', '`id`='.$id);
        iframe_destroy_content ($r['description']);
        db_delete ('tester_problems', '`id`='.$id);
        db_delete ('tester_tasks', '`problem_id`='.$id);
      }

      function Problems_GetAll ($contest_id = -1) {
        global $WT_contest_id, $WT_TESTING_Cache;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }
      
        if (isset ($WT_TESTING_Cache['AllProblems'][$contest_id])) {
          return $WT_TESTING_Cache['AllProblems'][$contest_id];
        }
      
        $arr = array ();
        $q = db_query ('SELECT `tp`.*, `tt`.`catid` '.
                       'FROM `tester_problems` AS `tp`, `tester_tasks` AS `tt` '.
                       'WHERE '.
                       '(`tp`.`id`=`tt`.`problem_id`) '.
                       'AND (`tt`.`contest_id`='.$contest_id.') '.
                       'ORDER BY `tt`.`catid`, `tp`.name');

        while ($r = db_row ($q)) {
          $r['settings'] = unserialize ($r['settings']);
          $arr[] = $r;
        }

        $WT_TESTING_Cache['AllProblems'][$contest_id] = $arr;

        return $arr;
      }
    
      function Problems_AllById ($contest_id = -1) {
        global $WT_TESTING_Cache;

        if (isset ($WT_TESTING_Cache['AllProblemsById'])) {
          $arr = $WT_TESTING_Cache['AllProblemsById'];
        } else {
          $arr = array ();
          $all = $this->Problems_GetAll ($contest_id);

          for ($i = 0, $n = count ($all); $i < $n; $i++) {
            $arr[$all[$i]['id']] = $all[$i];
          }

          $WT_TESTING_Cache['AllProblemsById']=$arr;
        }

        return $arr;
      }

      function Problem_Description ($num, $assoc_data, $form_prefix = '') {
        return $this->Template ('problem.desc', array ('num' => $num,
                                                       'assoc_data' => $assoc_data,
                                                       'lib' => $this,
                                                       'form_prefix' => $form_prefix));
      }

      ////
      //

      function PAGE_Testing () {
        global $CORE;
        $CORE->AddStyle ('testing');
        $this->gateway->AppendNavigation ('Тестиорование', '.?page=testing');

        $contest = WT_contest_by_id ();
        $judge = $this->IsContestJudge ();
        $running = WT_contest_running ();
        $manage = $this->GetAllowed ('CONTEST.MANAGE');

        if (!$judge && !$manage && !$running) {
          content_unavaliable ();
          return;
        }

        $this->InsertTemplate ('testing',
                               array ('data' => $contest, 'lib' => $this));
      }

      function PAGE_Solutions () {
        global $CORE;
        global $pageid, $WT_contest_id;

        if (!$this->GetAllowed ('SOLUTIONS.MANAGE')) return;

        $CORE->AddStyle ('testing');

        redirector_add_skipvar ('action');
        redirector_add_skipvar ('id');
        redirector_add_skipvar ('detail');

        $this->Solutions_ActionHandler ();
        $this->gateway->AppendNavigation ('Список решений',
                                          '?page=solutions'.
                                          (($pageid!='')?('&pageid='.$pageid):('')));

        $this->CPrintLn (stencil_formo ());
        $this->InsertTemplate ('solutions', array ('lib' => $this));
        $this->CPrintLn (stencil_formc ());
      }
    
      function PAGE_Status () {
        global $WT_contest_id, $action, $id;
        $manage=$this->IsContestJudge ();

        if (!WT_contest_running ($WT_contest_id) &&
            !WT_contest_finished ($WT_contest_id) && !$manage) {
          content_unavaliable ();
          return;
        }

        $this->gateway->AppendNavigation ('Статус по контесту', '.?page=submit');

        $this->CPrintLn (stencil_formo (''));
        $this->InsertTemplate ('status', array ('lib' => $this));
        $this->CPrintLn (stencil_formc ());
      }

      ////
      //

      function Solutions_ActionHandler () {
        global $action, $id;
        if ($action == 'delete') {
          $this->Solutions_Delete ($id);
        }
      }

      function Solutions_Delete ($id) {
        if (!$this->GetAllowed ('SOLUTIONS.DELETE')) {
          return;
        }

        db_delete ('tester_solutions', '`id`='.$id);
      }

      function GetSolutionsEntry ($contest_id, $clause = '') {
        $arr = array ();
        $q = db_select ('tester_solutions', array ('*'),
                        "`contest_id`=$contest_id".
                        (($clause!='')?(' AND '.$clause):(''))."",
                        'ORDER BY `timestamp` DESC');

        while ($r = db_row ($q)) {
          $t = $r;
          $t['parameters'] = unserialize ($r['parameters']);
          $arr[] = $t;
        }

        $c = WT_contest_by_id ($contest_id);

        // Filling da `try` field
        $n = count ($arr);
        $tries = array ();
        for ($i = $n - 1 ;$i >= 0; $i--) {
          if ($arr[$i]['ignored']) {
            $arr[$i]['try'] = $tries[$arr[$i]['user_id']][$arr[$i]['problem_id']];

            if (!isset ($arr[$i]['try'])) {
              $arr[$i]['try'] = 0;
            }

            continue;
          }

          $arr[$i]['try']=++$tries[$arr[$i]['user_id']][$arr[$i]['problem_id']];
        }

        return $arr;
      }

      function GetSolutionsCountEntry ($contest_id = -1, $clause = '') {
        global $WT_contest_id;

        if ($contest_id = -1) {
          $contest_id = $WT_contest_id;
        }

        return db_count ('tester_solutions', "`contest_id`=$contest_id".(($clause!='')?(' AND '.$clause):(''))."");
      }

      function GetUserSolutions ($contest_id, $user_id) {
        return $this->GetSolutionsEntry ($contest_id, "`user_id`=$user_id");
      }

      function GetAllSolutions ($contest_id = -1) {
        global $WT_contest_id;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        return $this->GetSolutionsEntry ($contest_id);
      }

      function GetUserSolutionsCount ($contest_id, $user_id) {
        return $this->GetSolutionsCountEntry ($contest_id, "`user_id`=$user_id");
      }

      function GetAllSolutionsCount  ($contest_id = -1) {
        return $this->GetSolutionsCountEntry ($contest_id);
      }

      ////
      //

      function IsContestJudge ($id = -1) {
        global $WT_contest_id;

        if ($id < 0) {
          $id = $WT_contest_id;
        }

        if (isset ($this->cache[$id]['IsContestJudge'])) {
          return $this->cache[$id]['IsContestJudge'];
        }

        $this->cache[$id]['IsContestJudge']=$this->GetAllowed ('CONTEST.MANAGE');

        if ($this->cache[$id]['IsContestJudge']) {
          return true;
        }

        if ($id == '') {
          return;
        }

        $q = db_query ('SELECT COUNT(*) AS `c` FROM `usergroup` AS `ug`, '.
                     '`tester_judgegroup` AS `tjg` '.
                     ' WHERE (`ug`.`user_id`='.user_id ().') '.
                     'AND (`tjg`.`group_id`=`ug`.`group_id`) '.
                     'AND (`tjg`.`contest_id`='.$id.')');
        $r = db_row ($q);
        $res = $r['c']>0;
        $this->cache[$id]['IsContestJudge'] = $res;

        return $res;
      }
    
      ////
      //

      function Test_Obtained ($contest_id = -1, $user_id = -1) {
        global $WT_contest_id, $WT_TESTING_Cache;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        if ($user_id < 0) {
          $user_id = user_id ();
        }

        if (isset ($WT_TESTING_Cache['Test.Obtained'][$contest_id][$user_id])) {
          return $WT_TESTING_Cache['Test.Obtained'][$contest_id][$user_id];
        }

        $res = false;
        if (!isset ($WT_TESTING_Cache['Test.Obtained.Data'][$contest_id][$user_id])) {
          $r = db_row_value ('tester_solutions',
                             "(`contest_id`=$contest_id) AND ".
                             "(`user_id`=$user_id)",
                             'ORDER BY `id` DESC LIMIT 1');
          $r['parameters'] = unserialize ($r['parameters']);
        } else {
          $r = $WT_TESTING_Cache['Test.Obtained.Data'][$contest_id][$user_id];
        }

        if (!$this->Test_CheckTimers ($r)) {
          $WT_TESTING_Cache['Test.Obtained.Data'][$contest_id][$user_id]['parameters']['interrupted'] = 1;
          return false;
        }

        $s = $r['parameters'];
        if (db_affected () > 0 && !$s['finished'] && !$s['interrupted']) {
          $res = true;
        }

        $WT_TESTING_Cache['Test.Obtained'][$contest_id][$user_id] = $res;
        $WT_TESTING_Cache['Test.Obtained.Data'][$contest_id][$user_id] = $r;

        return $res;
      }
    
      function Test_CanObtain ($contest_id = -1, $user_id = -1) {
        global $WT_contest_id;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        if ($user_id < 0) {
          $user_id = user_id ();
        }

        if ($this->GetAllowed ('CONTEST.MANAGE')) {
          return true;
        }

        if ($this->IsContestJudge ($user_id)) {
          return true;
        }
      
        if (!WT_contest_running ($contest_id)) {
          return false;
        }

        return true;
      }

      function Test_CheckTimers ($r, $contest_id = -1) {
        global $WT_contest_id;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        if ($r['parameters']['started']) {
          $c = WT_contest_by_id ();

          if ($c['settings']['timelimit']) {
            $t = $r['timestamp'];

            if (!$t) {
              $t = time ();
            }

            $p = $r['parameters'];

            if ($t + $c['settings']['timelimit'] * 60 < time ()) {
              if (!$p['interrupted']) {
                $p['interrupted'] = 1;
                db_update ('tester_solutions',
                           array ('parameters' => db_string (serialize ($p)),
                                  'points' => 0),
                           '`id`='.$r['id']);
              }

              return false;
            }
          }
        }

        return true;
      }

      function Test_GetCurrent ($contest_id = -1, $user_id = -1) {
        global $WT_contest_id, $WT_TESTING_Cache;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        if ($user_id < 0) {
          $user_id = user_id ();
        }

        if (isset ($WT_TESTING_Cache['Test.Obtained.Data'][$contest_id][$user_id])) {
          $t = $WT_TESTING_Cache['Test.Obtained.Data'][$contest_id][$user_id];
          if (!$this->Test_CheckTimers ($t)) {
            $WT_TESTING_Cache['Test.Obtained.Data'][$contest_id][$user_id]['parameters']['interrupted']=1;
            return array ();
          }
          return $t;
        }

        $r = db_row_value ('tester_solutions',
                           "(`contest_id`=$contest_id) AND ".
                           "(`user_id`=$user_id)",
                           'ORDER BY `id` DESC LIMIT 1');
        $r['parameters'] = unserialize ($r['parameters']);

        $WT_TESTING_Cache['Test.Obtained.Data'][$contest_id][$user_id] = $r;

        return $r;
      }

      ////
      //
      function Subnav_Info () {
        global $WT_contest_id;

        if (!isset ($WT_contest_id) || $WT_contest_id == '') {
          return;
        }

        $data = WT_contest_by_id ($WT_contest_id);
        return $this->Template ('subnav_info',
                                array ('data' => $data, 'lib' => $this));
      }
    
      ////
      //
    
      function Check ($c, $p) {
        $all = $this->Problems_AllById ($c['id']);
      
        $points = 0;
        $res = array ();

        for ($i = 0, $n = count ($p['tasks']); $i < $n; ++$i) {
          $pid = $p['tasks'][$i]['problem_id'];
          $ts = $all[$pid]['settings'];

          $lib = new $ts['anstype'] ();
          $res[$i] = $lib->Check ($ts, $p['user_answers'][$i]);

          if ($res[$i]) {
            $points++;
          }
        }

        return array ('res' => $res, 'points' => $points);
      }

      function Answers_Info ($user_data) {
        $arr = $user_data['parameters']['res'];
        $res = '<table class="data">'."\n";
        $p = $this->Problems_AllById ($user_data['contest_id']);
        $tasks = $user_data['parameters']['tasks'];

        $opened = false;
        $n = count ($arr);
        $i = 0;

        while ($i < $n) {
          $m = min (10, $n-$i);
          $res .= '  <tr>'."\n    ";

          for ($j = 0; $j < $m; $j++) {
            $pr = $p[$tasks[$i]['problem_id']];
            $res .= '<th><span title="'.
              htmlspecialchars (html2txt ($pr['description'])).'">'.($i +1 ).
              '</span></th>';
            $i++;
          }

          for ($j = $m; $j < 10; $j++) {
            $res.='<td class="void"></td>';
          }

          $res .= '  </tr>'."\n";
          $i -= $m;
          $res .= '  <tr>'."\n    ";

          for ($j = 0; $j < $m; $j++) {
            $title = '';
            $pr = $p[$tasks[$i]['problem_id']];

            if (!$arr[$i]) {
              $lib = new $pr['settings']['anstype'] ();
              $title = htmlspecialchars ('Ответ участника: '.
                                       $lib->GetUserAnswerText ($pr, $user_data['parameters']['user_answers'][$i], $i));
              $title .= htmlspecialchars (' | Верный ответ: '.$lib->GetCorrectAnswerText ($pr));
            }

            $res .= '<td'.(($arr[$i])?(' style="background: #cfc"'):('')).'>'.
              (($arr[$i])?('+'):('<span title="'.$title.'">-</span>')).'</td>';
            $i++;
          }

          for ($j = $m; $j < 10; $j++) {
            $res .= '<td class="void"></td>';
          }

          $res .= '  </tr>'."\n";
        }
        $res .= '</table>'."\n";

        return $res;
      }
    }

    WT_Library_Register ('CGMTesting', 'Testing', 1);
  }
?>
