<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Informatics contest class
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

  if ($__CGMInformatics_Included__ != '##CGMInformatics_Included##') {
    $__CGMInformatics_Included__ = '##CGMInformatics_Included##';

    global $INFORMATICS_problemsContainer;
    global $INFORMATICS_ProblemSettingsFields;
    $INFORMATICS_problemsContainer = nil;

    $INFORMATICS_ProblemSettingsFields = array (
      array ('name' => 'name', 'type' => 'TEXT', 'postname' => 'name',
             'title' => 'Название задачи', 'important' => true),

      array ('name' => 'timelimit', 'type' => 'TEXT', 'postname' => 'timelimit',
             'title' => 'Ограничение по времени (сек)', 'important' => true),

      array ('name' => 'memorylimit', 'type' => 'TEXT',
             'postname' => 'memorylimit', 'title'=>'Ограничение по памяти (Мб)',
             'important' => true),

      array ('name' => 'input','type' => 'TEXT', 'postname' => 'input',
             'title' => 'Входной файл', 'important' => true),

      array ('name' => 'output', 'type' => 'TEXT', 'postname'=>'output',
             'title' => 'Выходной файл', 'important' => true),

      array ('name' => 'tests', 'type' => 'CUSTOM', 'postname' => 'tests',
             'title' => 'Разбалловка тестов',
             'src' => tpl_unparsed ('front/tester/modules/Informatics'.
                                    '/problems.tests_automate'),
             'important' => true, 'check_value' => 'tests'),

      array ('name' => 'bonus', 'type' => 'TEXT', 'postname' => 'bonus',
             'title' => 'Бонус', 'important' => true),

      array ('name' => 'archove', 'type' => 'CUSTOM', 'postname' => 'archive',
             'title' => 'Архив с тестами',
             'src' => tpl_unparsed ('front/tester/modules/Informatics'.
                                    '/problems.archive'),
             'action' => 'save'),

      array ('name' => 'archove', 'type' => 'CUSTOM', 'postname' => 'archive',
             'title' => 'Архив с тестами',
             'src' => tpl_unparsed ('front/tester/modules/Informatics'.
                                    '/problems.archive'),
             'action' => 'create', 'important' => true,
             'check_value' => 'archive'),

      array ('name' => 'checker', 'type' => 'CUSTOM', 'postname' => 'checker',
             'title' => 'Готовый чекер',
             'src' => tpl_unparsed ('front/tester/modules/Informatics'.
                                    '/problems.checker')),

      array ('name' => 'comment', 'type' => 'TEXT', 'postname' => 'comment',
             'title' => 'Комментарий', 'important' => false),
    );

    ////////
    // Different Olympiad rules

    class CGMRulesVirtual extends CVirtual {
      var $problem_container;

      function CGMRulesVirtual ($lib, $problem_container) {
        $this->lib = $lib; 
        $this->problem_container=$problem_container;
      }

      function GetStatusRow ($r, $last = false) { return ''; }
      function GetStatusHeader () { return ''; }

      function TaskStatus ($r) {
        if ($r['status'] != 2) {
          return 'Тестируется...';
        }

        return WT_errors_string ($r['errors'],
                                 $r['parameters']['force_status']);
      }

      function TaskPoints ($r) {
        if ($r['status'] != 2) {
          return '&nbsp;';
        }

        return $r['points'];
      }

      function SolutionsHeader () {
        return '<tr class="h"><th  class="first">Время</th>'.
          '<th width="25%">Участник</th><th width="25%">Задача</th>'.
          '<th width="60">Попытка</th><th width="25%">Результат</th>'.
          ((!preg_match ('/ACM/', $this->GetClassName ()))?
             ('<th width="60">Балл</th>'):('')).
          '<th class="last"><div style="background: transparent; '.
          'width: 96px;">&nbsp;</div></th></tr>';
      }

      function SolutionsRow ($contest_id, $row_data, $n, $c,
                             $perPage, $full, $acl) {
        global $id, $WT_contest_id;

        $class = '';

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        $contest = WT_contest_by_id ($contest_id);

        if ($c == $perPage - 1 || $c == $n - 1) {
          $class = 'last';
        }

        if ($id == $row_data['id']) {
          $class .= (($class != '') ? (' ') : ('')).'active';
        }

        if ($row_data['status'] == 2 && $row_data['errors'] == 'OK') {
          $class .= (($class != '')?(' '):('')).'subactive';
        }

        $redirect = urlencode ($full);

        $user = user_generate_short_info_string ($row_data['user_id'], true);
        $pr = $this->problem_container->GetById ($row_data['problem_id']);
        $problem =
          $this->problem_container->GetProblemLetter (-1,
                                                      $row_data['problem_id']).
            '. <a href="?page=problems&act=view&id='.
          $row_data['problem_id'].'&redirect='.$redirect.'">'.
          htmlspecialchars ($pr['name']).'</a>';

        $status = $this->TaskStatus ($row_data);
        $points = $this->TaskPoints ($row_data);
        $try = $row_data['try'];

        $actions = '';
        $actions .= stencil_ibtnav ('detail.gif' , 
                                    $full.'&action=view&id='.$row_data['id'].
                                    '&redirect='.$redirect,
                                    'Просмотреть решение');

        $actions .= stencil_ibtnav ('painter.gif', $full.'&action=rejudge&id='.
                                    $row_data['id'],
                                    'Перетестировать решение');

        $actions .= stencil_ibtnav ('mem.gif', $full.'&action=setml&id='.
                                    $row_data['id'],
                                    'Установить &laquo;Превышение памяти&raquo;');

        if ($acl['delete']) {
          $actions .= stencil_ibtnav ('cross.gif', $full.'&action=delete&id='.
                                      $row_data['id'], 'Удалить',
                                      'Удалить это решение?');
        }

        $time=$this->lib->GetSolutionTime ($row_data);

        return '<tr'.(($class != '') ? (' class="'.$class.'"') : ('')).
          '><td>'.$time.'</td><td>'.$user.'</td><td>'.$problem.'</td>'.
          '<td align="center">'.$try.'</td><td>'.$status.'</td>'.
          ((!preg_match ('/ACM/', $this->GetClassName ()))?
               ('<td align="center">'.$points.'</td>') : ('')).
          '<td align="right">'.$actions.'</td></tr>';
      }

      function Monitor () {  }
    }

    class CGMRulesACM extends CGMRulesVirtual {

      function CGMRulesACM ($lib, $problem_container) {
        CGMRulesVirtual::CGMRulesVirtual ($lib, $problem_container);
        $this->SetClassName ('CGMRulesACM');
      }

      function TaskStatus ($r) {
        if ($r['status'] != 2) {
          return 'Тестируется...';

        }
        return WT_errors_string ($r['parameters']['TESTS'],
                                 $r['parameters']['force_status'],
                                 true, $r['errors']);
      }

      function GetStatusHeader () {
        return '<tr class="h"><th width="40%" class="first">Задача</th>'.
          '<th width="60">Попытка</th><th>Результат</th>'.
          '<th class="last">&nbsp;</th></tr>';
      }

      function GetStatusRow ($contest_id, $r, $n, $c, $perPage,
                             $full = '', $acl = array ()) {
        $pr = $this->problem_container->GetById ($r['problem_id']);
        $redirect = urlencode ($full);
        $letter = $this->problem_container->GetProblemLetter ($contest_id,
                                                              $r['problem_id']);
        $title = $letter.'. <a href="?page=problems&act=view&id='.$pr['id'].
          '&redirect='.$redirect.'">'.htmlspecialchars ($pr['name']).'</a>';
        $status = $this->TaskStatus ($r);
        $try = $r['try'];
        $class = '';

        if ($c == $perPage - 1 || $c == $n - 1) {
          $class = 'last';
        }

        if ($r['status'] == 2 && $r['errors'] == 'OK') {
          $class .= (($class != '') ? (' ') : ('')).'subactive';
        }

        $actions = stencil_ibtnav ('detail.gif' , $full.'&action=view&id='.
                                   $r['id'].'&redirect='.$redirect,
                                   'Просмотреть решение');

        return '<tr'.(($class != '') ? (' class="'.$class.'"') : ('')).'>'.
          '<td>'.$title.'</td><td align="center">'.$try.'</td>'.
          '<td>'.$status.'</td><td align="right" width="48">'.$actions.'</td>'.
          '</tr>';
      }

      function Monitor ($contest_id = -1) {
        global $WT_contest_id, $CORE;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        $this->lib->InsertTemplate ('monitor.acm',
                                    array ('lib' => $this->lib,
                                           'contest_id' => $contest_id,
                                           'user_id' => user_id ()));
      }
    }

    class CGMRulesKirov extends CGMRulesVirtual {
      function CGMRulesKirov ($lib, $problem_container) {
        CGMRulesVirtual::CGMRulesVirtual ($lib, $problem_container);
        $this->SetClassName ('CGMRulesKirov');
      }

      function GetStatusHeader () {
        return '<tr class="h"><th width="40%" class="first">Задача</th>'.
          '<th width="60">Попытка</th><th width="40%">Результат</th>'.
          '<th>Балл</th><th class="last">&nbsp;</th></tr>';
      }

      function GetStatusRow ($contest_id, $r, $n, $c, $perPage,
                             $full = '', $acl = array ()) {
        $pr = $this->problem_container->GetById ($r['problem_id']);
        $letter = $this->problem_container->GetProblemLetter ($contest_id,
                                                              $r['problem_id']);
        $redirect = urlencode ($full);
        $title = $letter.'. <a href="?page=problems&act=view&id='.$pr['id'].
          '&redirect='.$redirect.'">'.htmlspecialchars ($pr['name']).'</a>';
        $status = $this->TaskStatus ($r);
        $points = $this->TaskPoints ($r);
        $try = $r['try'];
        $class = '';
        $actions = stencil_ibtnav ('detail.gif' , $full.'&action=view&id='.
                                   $r['id'].'&redirect='.$redirect,
                                   'Просмотреть решение');

        if ($c == $perPage - 1 || $c == $n - 1) {
          $class = 'last';
        }

        if ($r['status'] == 2 && $r['errors'] == 'OK') {
          $class .= (($class != '') ? (' ') : ('')).'subactive';
        }

        return '<tr'.(($class!='')?(' class="'.$class.'"'):('')).'><td>'.
          $title.'</td><td align="center">'.$try.'</td>'.
          '<td>'.$status.'</td><td align="center">'.$points.'</td>'.
          '<td align="right" width="48">'.$actions.'</td></tr>';
      }

      function Monitor ($contest_id = -1) {
        global $WT_contest_id, $CORE;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        $this->lib->InsertTemplate ('monitor.kirov',
                                    array ('lib' => $this->lib,
                                           'contest_id' => $contest_id,
                                           'user_id' => user_id ()));
      }
    }

    ////////
    // Problems' container

    class CGMInformaticsProblemsContainer {
      var $data = array ();
      var $problem_setts = array ('timelimit', 'memorylimit', 'input',
                                  'output', 'tests', 'bonus', 'comment',
                                  'checker');
      var $cache = array ();

      function CGMInformaticsProblemsContainer ($lib) { $this->lib = $lib; }

      function Init () { $this->FillData (); }

      function FillData () {
        $t = db_query ('SELECT `tags`.`problem_id`, `dict`.`tag` '.
          'FROM `tester_problem_tags` AS `tags`, `tester_tags_dict` AS `dict` '.
          'WHERE `tags`.`tag_id`=`dict`.`id` ORDER BY `dict`.`tag`');
        $raw_tags = arr_from_ret_query ($t);
        $tags = array ();

        for ($i = 0, $n = count ($raw_tags); $i < $n; ++$i) {
          $tags[$raw_tags[$i]['problem_id']][] = $raw_tags[$i]['tag'];
        }

        $raw_tags = array ();
        $this->data = array ();

        $q = db_select ('tester_problems', array ('id', 'lid', 'settings',
                                                  'name', 'uploaded'),
                        '`lid`=0', 'ORDER BY `lid`, `name`');

        while ($r = db_row ($q)) {
          $arr = $r;
          $arr['settings'] = unserialize ($r['settings']);
          $arr['tags'] = $tags[$r['id']];
          $this->data[] = $arr;
        }
      }

      function ProblemDescription ($id) {
        if (isset ($this->cache['Problem.Description'][$id])) {
          return $this->cache['Problem.Description'][$id];
        }

        $r = db_field_value ('tester_problems', 'description', "`id`=$id");
        $this->cache['Problem.Description'][$id] = $r;
        return $r;
      }

      function GetByField ($f, $v) {
        $n = count ($this->data);

        for ($i=0; $i<$n; $i++) {
          if ($this->data[$i][$f] == $v) {
            return $this->data[$i];
          }
        }

        return $arr;
      }

      function GetByName ($name, $skipid = -1) {
        $tmp = $this->GetByField ('name', $name);

        if ($tmp['id'] == $skipid) {
          return array ();
        }

        return $tmp;
      }

      function GetById ($id)   { return $this->GetByField ('id', $id); }

      function StoreArchive ($id, $file) {
        $dir = config_get ('WT-Problems-Storage');
        @mkdir ($dir, config_get ('WT-Problems-Storage-mode'));
        @chmod ($dir, config_get ('WT-Problems-Storage-mode'));
        $name = preg_replace ('/^[^\.]+/i', $id.'@0', $file['name']);
        $fn = $dir.'/'.$name;
        move_uploaded_file ($file['tmp_name'], $fn);
        @chmod ($fn, config_get ('WT-Problems-Storage-data-mode'));
        return $name;
      }

      function Create ($name, $settings) {
        if (trim ($name) == '') {
          add_info ('Название задачи не может быть пустым.');
          return false;
        }

        if ($this->GetByName (htmlspecialchars ($name))) {
          add_info ('Задача с таким именем уже существует.');
          return false;
        }

        if ($_FILES['TestsArchive']['name'] == '') {
          add_info ('Не указан архив с тестами.');
          return false;
        }

        $arr = array ();
        $n = count ($this->problem_setts);

        for ($i = 0; $i < $n; $i++) {
          $arr[$this->problem_setts[$i]] = $settings[$this->problem_setts[$i]];
        }

        $s = serialize ($arr);

        db_insert ('tester_problems',
                   array ('name' => db_html_string ($name),
                          'description' => db_string ($settings['desc']),
                          'settings' => db_string ($s),
                          'lid' => 0));

        $id = db_last_insert ();
        $arr['filename'] = $this->StoreArchive ($id, $_FILES['TestsArchive']);

        db_update ('tester_problems',
                   array ('settings'=>db_string (serialize ($arr))),
                   "`id`=$id");

        $this->FillData ();
        return true;
      }

      function Delete ($id) {
        if ($id == '') {
          return false;
        }

        $desc = $this->ProblemDescription ($id);
        iframe_destroy_content ($desc);

        db_delete ('tester_problems', "`id`=$id");
        $q = db_select ('tester_tasks', array ('contest_id'),
                        "`problem_id`=$id");

        while ($r = db_row ($q)) {
          $this->lib->Problem_Drop ($r['contest_id'], $id);
        }

        WT_delete_solution_from_xpfs ("`problem_id`=$id");
        db_delete ('tester_solutions', "`problem_id`=$id");
        $this->FillData ();
        return true;
      }

      function CreateReceived () {
        if ($this->Create (FormPOSTValue ('name', 'ProblemSettings'),
                           INFORMATICS_GenerateProblemEditorForm ())) {
          if (FormPOSTValue ('addToContest', 'ProblemSettings')) {
            global $promtadd;
            $this->lib->Contenst_AddProblemToContest ($promtadd,
                                                      db_last_insert ());
          }
          $_POST=array ();
        }
      }

      function Update ($id, $settings) {
        global $id;

        $name = $settings['name'];
        $desc = $settings['desc'];

        if (trim ($name) == '') {
          add_info ('Название задачи не может быть пустым.');
          return false;
        }

        if ($this->GetByName (htmlspecialchars ($name), $id)) {
          add_info ('Задача с таким именем уже существует.');
          return false;
        }

        $a = $this->GetById ($id);

        $arr = $a['settings'];
        $n = count ($this->problem_setts);
        for ($i = 0; $i < $n; $i++) {
          $arr[$this->problem_setts[$i]] = $settings[$this->problem_setts[$i]];
        }

        $r = db_row_value ('tester_problems', "`id`=$id");
        $set = unserialize ($r['settings']);
        $uploaded = $r['uploaded'];

        if ($_FILES['TestsArchive']['name'] != '' ||
            $arr['checker'] != $set['checker']) {

          if ($_FILES['TestsArchive']['name'] != '') {
            global $XPFS;
            $arr['filename'] =
              $this->StoreArchive ($id, $_FILES['TestsArchive']);
            $XPFS->removeItem ('/tester/tests/'.$id.'0');
          }

          $uploaded = 0;
          unset ($arr['ERR']);
          unset ($arr['DESC']);
        }

        $s = serialize ($arr);
        db_update ('tester_problems', array ('name' => db_html_string ($name),
                                             'description' => db_string ($desc),
                                             'settings' => db_string ($s),
                                             'uploaded' => $uploaded),
                   "`id`=$id");

        return true;
      }

      function UpdateReceived () {
        global $id;
        $old_desc = $this->ProblemDescription ($id);
        $s = INFORMATICS_GenerateProblemEditorForm ();

        $s['desc'] =' ';
        if ($this->Update ($id, $s)) {
          $s['desc'] = iframe_accept_content ('desc', $old_desc);
          $this->Update ($id, $s);
          $_POST=array ();
          return true;
        }

        return false;
      }

      function GetList  ($filter = '')   {
        if ($filter != '') {
          return $this->Filter ($filter);
        }
        return $this->data;
      }

      function GetCount ()   { return count ($this->data); }
      function GetItem  ($i) { return $this->data[$i]; }

      function Filter ($filter) {
        $res = array ();

        for ($i = 0, $n = count ($this->data); $i < $n; ++$i) {
          if (@preg_match ("/$filter/i", $this->data[$i]['name']) ||
              @preg_match ("/$filter/i", $this->data[$i]['settings']['comment'])) {
            $res[] = $this->data[$i];
          }
        }

        return $res;
      }

      function UpdateTasksCache ($contest_id, $problem_id) {
        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        if (isset ($this->cache['tasks'][$contest_id])) {
          return;
        }

        $t = arr_from_query ("SELECT * FROM `tester_tasks` ".
                             "WHERE `contest_id`=$contest_id");

        $this->cache['tasks'][$contest_id] = $t;
      }

      function GetProblemLetter ($contest_id, $problem_id) {
        global $WT_contest_id;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        if (isset ($this->cache['letters'][$contest_id][$problem_id])) {
          return $this->cache['letters'][$contest_id][$problem_id];
        }

        $this->UpdateTasksCache ($contest_id, $problem_id);
        $arr = $this->cache['tasks'][$contest_id];
        $n = count ($arr);

        for ($i = 0; $i < $n; $i++) {
          if ($arr[$i]['problem_id'] == $problem_id) {
            $res = core_alpha ($arr[$i]['letter']);
          }
        }

        $this->cache['letters'][$contest_id][$problem_id] = $res;
        return $res;
      }

      function Rejudge ($id) {
        if ($this->lib->GetAllowed ('PROBLEMS.REJUDGE')) {
          db_update ('tester_solutions', array ('errors' => '""',
                                                'points' => '0',
                                                'status' => 0),
                     "`problem_id`=$id");
        }
      }

      function GetTests ($id) {
        $data = $this->GetByID ($id);

        if ($data['id'] != $id) {
          return null;
        }

        $tests_count = count (explode (' ', $data['settings']['tests']));
        $data = db_unpack (WT_ReceiveIPCData ('/tester/tests', $id.'@0',
                                              'informatics',
                                              array ('get_tests', $id,
                                                     '1-'.$tests_count)));

        return array ('tests' => db_unpack ($data['tst']),
                      'answers' => db_unpack ($data['ans']));
      }

      function AddTagToProblem ($problem_id, $tag) {
        $tag_id=db_field_value ('tester_tags_dict', 'id',
                                '`tag`="' . addslashes ($tag) . '"');
        if (!isnumber ($tag_id)) {
          db_insert ('tester_tags_dict', array ('tag' => db_string ($tag)));
          $tag_id = db_last_insert ();
        }

        if (db_count ('tester_problem_tags',
                      "`problem_id`=$problem_id AND `tag_id`=$tag_id") == 0) {
          db_insert ('tester_problem_tags', array ('problem_id' => $problem_id,
                                                   'tag_id'     => $tag_id));
          return true;
        } else {
          return false;
        }
      }

      function RemoveTagFromProblem ($problem_id, $tag) {
        $tag_id = db_field_value ('tester_tags_dict', 'id',
                                  '`tag`="' . addslashes ($tag) . '"');
        if (isnumber ($tag_id)) {
          db_delete ('tester_problem_tags', "`problem_id`=$problem_id AND `tag_id`=$tag_id");
          return true;
        } else {
          return false;
        }
      }

      function GetAllTags () {
        $t = db_query ('SELECT `tag` FROM `tester_tags_dict` ORDER BY `tag`');
        return arr_from_ret_query ($t, 'tag');
      }
    }

    ////////
    // 
    class CGMInformatics extends CGMVirtual {
      var $rules = array ();
      var $problemsContainer;
      var $cache;

      function Init () {
        $this->RegisterRules ('CGMRulesACM',   'ACM',       0);
        $this->RegisterRules ('CGMRulesKirov', 'Кировские', 1);
        if ($this->GetAllowed ('PROBLEMS.MANAGE'))
          $this->gateway->AppendLIBHandler (0, 'prbmanager', 'PAGE_ProblemsManager');
        $this->gateway->AppendLIBHandler   (0, 'problems',   'PAGE_Problems');
        $this->gateway->AppendLIBHandler   (0, 'monitor',    'PAGE_Monitor');
        $this->gateway->AppendLIBHandler   (0, 'status',     'PAGE_Status');
        $this->gateway->AppendLIBHandler   (0, 'submit',     'PAGE_Submit');
        $this->gateway->AppendLIBHandler   (0, 'solutions',  'PAGE_Solutions');

        if ($this->GetAllowed ('MONITOR.MEGAMONITOR')) {
          $this->gateway->AppendLIBHandler (0, 'megamonitor', 'PAGE_Megamonitor');
        }

        $this->problemsContainer = INFORMATICS_SpawnNewProblemsContainer ($this);
      }

      function RegisterRules ($class, $title, $id) {
        $this->rules[] = array ('class' => $class, 'title' => $title,
                                'id' => $id);
      }

      function CGMInformatics ($gw) {
        CGMVirtual::CGMVirtual ($gw);
        $this->SetClassName ('CGMInformatics');
        $this->SetModuleName ('Informatics');
      }

      ////
      // Rules' stuff
      function GetRules () { return $this->rules; }

      function GetRulesByID  ($id) {
        $n = count ($this->rules);

        for ($i = 0; $i < $n; $i++) {
          if ($this->rules[$i]['id'] == $id) {
            return $this->rules[$i];
          }
        }

        return array ();
      }

      function SpawnRulesLib ($contest_id = -1) {
        global $WT_contest_id;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        $c = WT_contest_by_id ($contest_id);
        $n = count ($this->rules);

        for ($i = 0; $i < $n; $i++) {
          $r = $this->rules[$i];

          if ($r['id'] == $c['settings']['rules']) {
            return new $r['class'] ($this, $this->problemsContainer);
          }
        }
      }

      ////
      //

      function DefaultContestSettings () {
        return array ('rules' => 0, 'timestamp' => time (), 'duration' => 0,
                      'trycount' => 0, 'penalty' => 0, 'compilers' => array (),
                      'groups' => array (), 'judges' => array (),
                      'freezetime' => 0, 'unfrozen' => false,
                      'viewdetail' => false, 'show_last_accepted' => false,
                      'ignore_ce' => false, 'autostart' => false,
                      'archive' => '');
      }

      function PerformCreation ($params) {
        $params = $this->DefaultContestSettings ();
      }

      function PerformContestDeletion ($id) {
        $c = WT_contest_by_id ($id);

        if ($c['settings']['archive']) {
          file_unlink_encrypted ($c['settings']['archive']);
        }
      }

      function PerformContestStateUpdate ($c, $state) {
        $blocked = ($state != 1 && $state != 2) ? (true) : (false);

        if ($c['settings']['archive'] != '') {
          if ($c['settings']['archive.blocked'] != $blocked) {
            file_block_encrypted ($c['settings']['archive'], $blocked);
            $c['settings']['archive.blocked'] = $blocked;
            db_update ('tester_contests',
                       array ('settings' => db_string (serialize ($c['settings']))),
                       '`id`='.$c['id']);
          }
        }
      }

      ////////
      // IPC

      function IPC_Contest_ToggleDisableProblem ($id, $pid) {
        return $this->Contest_ToggleDisableProblem ($id, $pid);
      }

      function IPC_Problem_DescriptionForm ($id, $cid, $backlink='') {
        if ($id>=0 && $this->Problem_Accessible ($id, $cid)) {
          return $this->Problem_DescriptionForm ($id, $backlink);
        } else {
          $tpl = manage_template_by_name ('Олимпиады / Informatics / Список задач');
          $this->CPrintLn ($tpl->GetText ());
        }
      }

      function IPC_Problem_Rejudge ($id) {
        return $this->Problem_Rejudge ($id);
      }

      function IPC_Monitor () {
        $this->PAGE_Monitor ();
        return preg_replace ('/\<div id\="monitor" style\="_position\: relative\;"\>(.*)\<\/div\>/si', '\1',
                             preg_replace ('/\<script[\s|\w|\=|\|\/"]*\>.*\<\/script\>/si', '',
                                           $this->GetContent ()));
      }

      function IPC_Problem_AddTag ($problem_id, $tag) {
        return $this->AddTagToProblem ($problem_id, $tag);
      }

      function IPC_Problem_RemoveTag ($problem_id, $tag) {
        return $this->RemoveTagFromProblem ($problem_id, $tag);
      }

      //////
      // Problems' stuff

      function AddTagToProblem ($problem_id, $tag) {
        if (!$this->GetAllowed ('PROBLEMS.EDIT')) {
          return false;
        }
        return $this->problemsContainer->AddTagToProblem ($problem_id, $tag);
      }

      function RemoveTagFromProblem ($problem_id, $tag) {
        if (!$this->GetAllowed ('PROBLEMS.EDIT')) {
          return false;
        }
        return $this->problemsContainer->RemoveTagFromProblem ($problem_id, $tag);
      }

      function GetProblemsAtContest ($contest_id = -1) {
        global $WT_contest_id;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        $q = db_select ('tester_tasks', array ('*'),
                        "`contest_id`=$contest_id", 'ORDER BY `letter`');

        $arr = array ();
        while ($r = db_row ($q)) {
          $t = $this->problemsContainer->GetById ($r['problem_id']);
          $t['letter'] = $r['letter'];
          $arr[] = $t;
        }

        return $arr;
      }

      function GetProblemsCountAtContest ($contest_id) {
        return db_count ('tester_tasks', "`contest_id`=$contest_id");
      }

      function GetNextProblemLetter ($contest_id) {
        $max = db_max ('tester_tasks', 'letter', "`contest_id`=$contest_id");
        return $max + 1;
      }

      function ProblemLetter ($contest_id, $problem_id) {
        return db_field_value ('tester_tasks', 'letter',
                               "`contest_id`=$contest_id AND ".
                               "`problem_id`=$problem_id");
      }

      ////////
      // Solutions' stuff

      function Solutions_ActionHandler () {
        global $action, $id;

        if (!$this->GetAllowed ('SOLUTIONS.MANAGE')) {
          return;
        }

        if ($action == 'setml')    $this->Solution_SetML ($id);
        if ($action == 'rejudge')  $this->Solution_Rejudge ($id);
        if ($action == 'view')     $this->Solution_DrawInformation ($id);
        if ($action == 'delete' && $this->GetAllowed ('SOLUTIONS.DELETE'))
          $this->Solution_Remove ($id);
      }

      function PutSolution ($contest_id, $user_id, $problem_id,
                            $compiler_id, $src) {
        $params = array ('src' => $src, 'compiler_id' => $compiler_id);
        db_insert ('tester_solutions', array ('lid' => 0,
                                              'contest_id' => $contest_id,
                                              'problem_id' => $problem_id,
                                              'user_id' => $user_id,
                                              'timestamp' => time (),
                                              'status' => 0,
                                              'parameters' => db_string (serialize ($params)),
                                              'errors' => '""',
                                              'points' => 0));
        return true;
      }

      function Solution_DBInfoByID ($solution_id) {
        return db_row_value ('tester_solutions', "`id`=$solution_id");
      }

      function Solution_ParametersByID ($solution_id) {
        $data = $this->Solution_DBInfoByID ($solution_id);
        return unserialize ($data['parameters']);
      }

      function Solution_Remove ($solution_id) {
        if (!$this->GetAllowed ('SOLUTIONS.DELETE')) {
          return;
        }

        WT_delete_solution_from_xpfs ("`id`=$solution_id");
        db_delete ('tester_solutions', "`id`=$solution_id");
      }

      function Solution_SetML ($solution_id) {
        if (!$this->GetAllowed ('SOLUTIONS.MANAGE')) {
          return;
        }

        $params = $this->Solution_ParametersByID ($solution_id);
        $params['force_status']='ML';
        db_update ('tester_solutions', array ('errors' => '"ML"',
                                              'points' => 0,
                                              'status' => 2,
                                              'parameters' => db_string (serialize ($params))),
                   "`id`=$solution_id");
      }

      function Solution_Rejudge ($solution_id) {
        if (!$this->GetAllowed ('SOLUTIONS.MANAGE')) {
          return;
        }

        $params = $this->Solution_ParametersByID ($solution_id);
        unset ($params['force_status']);
        db_update ('tester_solutions', array ('errors' => '""',
                                              'points' => 0,
                                              'status' => 0,
                                              'parameters' => db_string (serialize ($params))),
                   "`id`=$solution_id");
      }

      function Solution_DrawInformation ($solution_id) {
        global $redirect;

        $allow = $this->GetAllowed ('SOLUTIONS.MANAGE') ||
          $this->IsContestJudge ();
        $r = db_row_value ('tester_solutions', "`id`=$solution_id");

        if (db_affected () <= 0) {
          return;
        }

        $r['parameters'] = unserialize ($r['parameters']);
        $detail = false;

        if (!$allow) {
          if ($r['user_id'] != user_id ()) {
            return;
          }

          $c = WT_contest_by_id ($r['contest_id']);

          if ($c['settings']['viewdetail']) {
            $detail = true;
          }
        } else {
          $detail = true;
        }

        $this->CPrintLn (stencil_formo ('title=Информация о попытке;'));
        $this->InsertTemplate ('solution.info', array ('lib' => $this,
                                                       'data' => $r,
                                                       'backlink' => $redirect,
                                                       'detail' => $detail));
        $this->CPrintLn (stencil_formc ());
      }

      ////////
      // Contests' stuff

      function Contest_ActionHandler () {
        global $act, $uid, $id, $pid;
        $full = content_url_get_full ();

        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        if ($act=='add') {
          $this->Contenst_AddProblemToContest ($id, $uid);
        } else if ($act == 'massadd') {
          $this->Contenst_MassAddProblemToContest ($id, $uid);
        } else if ($act == 'massdel') {
          $this->Contenst_MassProblemDelete ($id, $uid);
        } else if ($act == 'view') {
          if (browser_engine ()!='DONKEY') {
            $this->CPrintLn ('<div id="view" style="margin-top: -6px; '.
                             'padding-top: 6px;">');
          } else {
            $this->CPrintLn ('<div id="view" style="_height: 1%;">');
          }

          $this->Problem_DrawDescription ($uid, content_url_get_full ());
          $this->CPrintLn ('</div>');
        } else if ($act == 'up') {
          $this->Problem_MoveUp ($id, $pid);
        } else if ($act == 'down') {
          $this->Problem_MoveDown ($id, $pid); 
        } else if ($act == 'drop') {
          $this->Problem_Drop ($id, $pid); 
        } else if ($act == 'rejudge') {
          $this->Problem_Rejudge ($pid);
        } else if ($act == 'delete') {
          $this->problemsContainer->Delete ($pid); 
        } else if ($act == 'savegroups') {
            $this->Contest_UpdateRecievedGroupUsed ($id);
        } else if ($act == 'savecompilers') {
          $this->Contest_UpdateRecievedCompilers ($id);
        } else if ($act=='togdis') {
          $this->Contest_ToggleDisableProblem ($id, $pid);
        }
      }

      function Contest_ToggleDisableProblem ($id, $pid) {
        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return false;
        }

        if (db_count ('tester_disabled_problems',
                      "(`contest_id`=$id) AND (`problem_id`=$pid)") > 0) {
          db_delete ('tester_disabled_problems', "(`contest_id`=$id) AND (`problem_id`=$pid)");
        } else {
          db_insert ('tester_disabled_problems',
                     array ('contest_id' => $id, 'problem_id' => $pid));
        }

        return true;
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
          if ($_POST[$list[$i]['id']])
            $arr[$list[$i]['id']] = 1;
        }

        $this->UpdateCompilers ($id, $arr);
      }

      function Contest_UpdateRecievedGroupUsed_Iterator ($id = -1, $name, $table) {
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

      function Contest_UpdateRecievedGroupUsed ($id=-1) {
        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $this->Contest_UpdateRecievedGroupUsed_Iterator ($id, 'usergroup',
                                                         'tester_contestgroup');
        $this->Contest_UpdateRecievedGroupUsed_Iterator ($id, 'judgegroup',
                                                         'tester_judgegroup');
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
        return $this->Contest_GetUserGroup_Iterator ($id,
                                                     'tester_contestgroup');
      }

      function Contest_GetJudgeGroup ($id = -1) {
        return $this->Contest_GetUserGroup_Iterator ($id, 'tester_judgegroup');
      }

      function Contenst_AddProblemToContest ($contest_id, $problem_id) {
        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        if (db_count ('tester_tasks', "`contest_id`=$contest_id AND ".
                      "`problem_id`=$problem_id") > 0) {
          return false;
        }

        $letter = $this->GetNextProblemLetter ($contest_id);
        db_insert ('tester_tasks', array ('contest_id' => $contest_id,
                                          'problem_id' => $problem_id,
                                          'letter' => $letter));

        return true;
      }

      function Contenst_MassAddProblemToContest ($contest_id, $ids) {
        if (!$this->GetAllowed ('CONTEST.MANAGE')) {
          return;
        }

        $ids = explode (',', $ids);
        $n = count ($ids);

        for ($i = 0; $i < $n; $i++) {
          $this->Contenst_AddProblemToContest ($contest_id, $ids[$i]);
        }
      }

      function Contenst_MassProblemDelete ($contest_id, $ids) {
        if (!$this->GetAllowed ('PROBLEMS.DELETE')) {
          return;
        }

        $ids = explode (',', $ids);
        $n = count ($ids);

        for ($i = 0; $i < $n; $i++) {
          $this->problemsContainer->Delete ($ids[$i]); 
        }
      }

      function Contest_Manager ($id, $clear=false) {
        content_url_var_push_global ('action');
        content_url_var_push ('id', $id);
        content_url_var_push_global ('cman');
        content_url_var_push_global ('pbrows');
        content_url_var_push_global ('prpage');

        $this->Contest_ActionHandler ();
        $contest=WT_contest_by_id ($id);
        $this->InsertTemplate ('contest.edit', array ('data' => $contest,
                                                      'lib' => $this));
      }

      function Contest_Save ($id, $clear = false) {
        global $noarchive;

        $contest = WT_contest_by_id ($id);
        $name = FormPOSTValue ('name', 'ContestSettings');

        if (trim ($name) == '') {
          add_info ('Название контеста не может быть пустым.');
          return false;
        }

        $settings = $contest['settings'];
        $settings['rules']      = atoi (FormPOSTValue ('rules',      'ContestSettings'));
        $settings['duration']   = atoi (FormPOSTValue ('duration',   'ContestSettings'));
        $settings['freezetime'] = atoi (FormPOSTValue ('freezetime', 'ContestSettings'));
        $settings['penalty']    = atoi (FormPOSTValue ('penalty',    'ContestSettings'));
        $settings['trycount']   = atoi (FormPOSTValue ('trycount',   'ContestSettings'));
        $settings['unfrozen']   = atoi (FormPOSTValue ('unfrozen',   'ContestSettings'));
        $settings['viewdetail'] = atoi (FormPOSTValue ('viewdetail', 'ContestSettings'));
        $settings['show_last_accepted'] = atoi (FormPOSTValue ('show_last_accepted',
                                                               'ContestSettings'));
        $settings['ignore_ce']  = atoi (FormPOSTValue ('ignore_ce',  'ContestSettings'));
        $settings['autostart']  = $_POST['contest_autostart'];

        if ($settings['autostart']) {
          $date = new CDCDate ();
          $date->Init ();
          $date->ReceiveValue ('contest_autostart_date');
          $settings['autostart.date'] = $date->GetValue ();

          $time = new CDCDate ();
          $time->Init ();
          $time->ReceiveValue ('contest_autostart_time');
          $settings['autostart.time'] = $time->GetValue ();
        }

        if (!$noarchive) {
          if ($_FILES['archive']['name'] != '') {
            file_unlink_encrypted ($settings['archive']);
            $data = $_FILES['archive'];

            if (WT_contest_running ($id) || WT_contest_finished ($id)) {
              $blocked = 0;
            } else {
              $blocked = 1;
            }
            $settings['archive'] = file_store_encrypted ($data, 1, $blocked);
          }
        } else {
          file_unlink_encrypted ($settings['archive']);
          $settings['archive'] = '';
        }

        $update = array ('name' => db_html_string ($name),
                         'settings' => db_string (serialize ($settings)));
        db_update ('tester_contests', $update, "`id`=$id");
        return true;
      }

      ////////////
      // Problems' stuff

      function Problem_IsAtContest ($problem_id, $contest_id) {
        return (db_count ('tester_tasks', "`contest_id`=$contest_id ".
                          "AND `problem_id`=$problem_id") > 0);
      }

      function Problem_Accessible ($problem_id, $contest_id = -1) {
        global $WT_contest_id;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        // TODO:
        // Add checking of contest state here

        if (!$this->IsContestJudge ($contest_id) &&
            (!WT_contest_running ($contest_id) &&
             !WT_contest_finished ($contest_id))) {
          return false;
        }

        return $this->Problem_IsAtContest ($problem_id, $contest_id);
      }

      function Problem_AccessibleForSubmit ($problem_id, $user_id,
                                            $silent = true, $contest_id = -1) {
        global $WT_contest_id;

        if ($contest_id < 0) {
          $contest_id = $WT_contest_id;
        }

        $manage = $this->IsContestJudge ();
        $contest = WT_contest_by_id ($contest_id);
        $add = '';

        if ($contest['settings']['ignore_ce']) {
          $add = ' AND (`errors`<>"CE") ';
        }

        if ($contest['settings']['trycount'] > 0 && !$manage)
          // TODO:
          // Optimize this stupid checking for correct working with ignored solutions
          if (db_count ('tester_solutions', "(`ignored`=0) ".
                        "AND (`errors`<>\"CR\") $add ".
                        "AND (`user_id`=$user_id) ".
                        "AND (`contest_id`=$contest_id) ".
                        "AND (`problem_id`=$problem_id)") >=
              $contest['settings']['trycount']) {
            add_info ('Невозможно послать решение задачи, так как превышен предел попыток.');
            return false;
          }

        if (db_count ('tester_disabled_problems', '(`contest_id`='.
                      $contest_id.') AND (`problem_id`='.$problem_id.')') > 0) {
          return false;
        }

        if (!WT_contest_running ($contest_id) && !$manage) {
          add_info ('Невозможно послать решение задачи, '.
                    'так как контест завершен.');
          return false;
        }

        if (!$this->Problem_IsAtContest ($problem_id, $contest_id)) {
          if (!$silent) {
            add_info ('Извините, но выбранная задача уже отсутсвует на контесте.');
          }
          return false;
        }

        return true;
      }

      function Problem_Drop ($contest_id, $problem_id) {
        $letter = $this->ProblemLetter ($contest_id, $problem_id);

        if ($letter < 0 || $letter == '') {
          return false;
        }

        db_delete ('tester_tasks', "`contest_id`=$contest_id ".
                   "AND `problem_id`=$problem_id");
        WT_delete_solution_from_xpfs ("`contest_id`=$contest_id ".
                                      "AND `problem_id`=$problem_id");
        db_delete ('tester_solutions', "`contest_id`=$contest_id ".
                   "AND `problem_id`=$problem_id");
        db_query ("UPDATE `tester_tasks` SET `letter`=`letter`-1 ".
                  "WHERE `contest_id`=$contest_id AND `letter`>$letter");
      }

      function Problem_MoveUp ($contest_id, $problem_id) {
        $letter = $this->ProblemLetter ($contest_id, $problem_id);

        if ($letter<=1) {
          return;
        }

        $letter2 = $letter - 1;
        $id1 = db_field_value ('tester_tasks', 'id',
                               "`contest_id`=$contest_id AND ".
                               "`problem_id`=$problem_id");
        $id2 = db_field_value ('tester_tasks', 'id',
                               "`contest_id`=$contest_id AND ".
                               "`letter`=$letter2");

        db_swap_values ('tester_tasks', $id1, $id2, 'letter', $idfield = 'id');
      }

      function Problem_MoveDown ($contest_id, $problem_id) {
        $letter = $this->ProblemLetter ($contest_id, $problem_id);
        $letter = db_field_value ('tester_tasks', 'letter',
                                  "`contest_id`=$contest_id AND ".
                                  "`problem_id`=$problem_id");
        $count = $this->GetProblemsCountAtContest ($contest_id);

        if ($letter >= $count) {
          return;
        }

        $letter2 = $letter + 1;
        $id1 = db_field_value ('tester_tasks', 'id',
                               "`contest_id`=$contest_id AND ".
                               "`problem_id`=$problem_id");
        $id2 = db_field_value ('tester_tasks', 'id',
                               "`contest_id`=$contest_id AND ".
                               "`letter`=$letter2");

        db_swap_values ('tester_tasks', $id1, $id2, 'letter', $idfield = 'id');
      }

      function Problem_DescriptionForm ($id, $backlink = '') {
        $edit = $this->GetAllowed ('PROBLEMS.EDIT');
        $arr = $this->problemsContainer->GetById ($id);
        $arr['description'] = $this->problemsContainer->ProblemDescription ($id);
        $res = $this->Template ('problems.description',
                                array ('data' => $arr,
                                       'backlink' => $backlink,
                                       'editbtn' => $edit,
                                       'lib' => $this));
        return $res;
      }

      function Problem_DrawDescription ($id, $backlink = '') {
        $this->CPrintLn ($this->Problem_DescriptionForm ($id, $backlink));
      }

      function Problem_GenerateEditorForm ($data = array (), $act = 'create',
                                           $backlink = '') {
        global $INFORMATICS_ProblemSettingsFields, $pageid, $id, $promtadd;

        $fields = $INFORMATICS_ProblemSettingsFields;
        $action = prepare_arg ('.?page=prbmanager&action='.
                               $act.(($id!='')?('&id='.$id):('')).'&'.
                               (($pageid!='')?('pageid='.$pageid):('')).
                               (($promtadd!='')?('&promtadd='.$promtadd):('')).
                               (($backlink!='')?('&redirect='.urlencode ($backlink)):('')));
        $desc = $data['desc'];

        if ($desc == '') {
          $tpl = manage_template_by_name ('Олимпиады / Informatics / Заготовка условия задачи');
          $desc = $tpl->GetText ();
        }

        $iframe = iframe_editor ('desc', $desc, $backlink != '',
                                 'ProblemSettings',
                                 $this->gateway->content_settings['iframe']);

        $tmp = handler_get_list ('ProblemSettings'); $arr=$tmp['onsubmit'];
        $onsubmit = '';

        for ($i = 0; $i < count ($arr); $i++) {
          $onsubmit .= ' '.handler_build_callback ($arr[$i]);
        }

        $form = new CVCForm ();
        $form->Init ('ProblemSettings',
                     'method=POST;enctype=multipart/form-data;action='.$action.
                     ';titlewidth=160;caption='.
                     (($act=='create')?('Создать'):('Сохранить')).
                     ';onsubmit='.prepare_arg ($onsubmit).';backlink='.
                     prepare_arg ($backlink).';');

        for ($i = 0, $n = count ($fields); $i < $n; $i++) {
          $f = $fields[$i];

          if ($f['action'] != '' && $f['action'] != $act) {
            continue;
          }

          $v = $data[$f['name']];
          if (strtolower ($f['type']) == 'custom') {
            $v = array ('src' => $f['src'], 'value' => $v,
                        'check_value' => $f['check_value']);
          }
          $form->AppendField ($f['title'], $f['postname'], $f['type'], $v,
                              array ('important'=>$f['important']));
        }

        if ($act != 'create') {
          $form->AppendField ('Состояние', '', 'CUSTOM',
                              array ('src' => $this->Template ('problems.state',
                                                               array ('data' => $data))));
        }

        if (isset ($promtadd)) {
          $form->AppendCheckBoxField ('Добавить на контест', 'addToContest',
                                      false);
        }

        $form->AppendCustomField (array ('src'=>'<center><b>Условие задачи</b>'.
                                         $iframe.'</center>'));

        return $form;
      }

      function Problem_GetDataFromPostData () {
        return INFORMATICS_GenerateProblemEditorForm ();
      }

      function Problem_GetDataById ($id) {
        global $INFORMATICS_ProblemSettingsFields;

        $fields = $INFORMATICS_ProblemSettingsFields;
        $arr = $this->problemsContainer->GetById ($id);
        $res = array ();
        $res['name'] = $arr['name'];
        $res['uploaded']  = $arr['uploaded'];
        $res['ERR']  = $arr['settings']['ERR'];
        $res['DESC'] = $arr['settings']['DESC'];
        $res['desc'] = $this->problemsContainer->ProblemDescription ($id);

        for ($i = 0; $i < count ($fields); $i++) {
          $f = $fields[$i];
          if (isset ($arr['settings'][$f['name']])) {
            $res[$f['name']] = $arr['settings'][$f['name']];
          }
        }

        return $res;
      }

      function Problem_Accepted ($id) {
        global $WT_contest_id;
        $user_id = user_id ();
        return db_count ('tester_solutions', "(`contest_id`=$WT_contest_id) ".
                         "AND (`user_id`=$user_id) AND (`problem_id`=$id) ".
                         "AND (`errors`='OK') AND (`status`=2)");
      }

      function Problem_Tried ($id) {
        global $WT_contest_id;
        $user_id = user_id ();
        return db_count ('tester_solutions', "(`contest_id`=$WT_contest_id) ".
                         "AND (`user_id`=$user_id) AND (`problem_id`=$id) ".
                         "AND (`status`=2)");
      }

      function Problem_DrawProblems () {
        $manage = $this->IsContestJudge ();

        if (!$manage) {
          return;
        }

        $delete  = $this->GetAllowed ('PROBLEMS.DELETE');
        $edit    = $this->GetAllowed ('PROBLEMS.EDIT');
        $rejudge = $this->GetAllowed ('PROBLEMS.REJUDGE');

        $list = $this->problemsContainer->GetList (stripslashes ($_GET['filter']));

        $n = count ($list);
        if ($n == 0) {
          $this->CPrintLn ('<span class="contentSub2">'.
                           'Нет задач для редактирования или просмотра.</span>');
          $this->CPrintLn ($this->Template ('problems.filter.form'));
          return;
        }

        $problemsPerPage = opt_get ('WT_problems_per_page');
        if (!$problemsPerPage) {
          $problemsPerPage = 15;
        }
        $page = $i = 0;

        $pages = new CVCPagintation();
        content_url_var_push_global ('filter');
        $pages->Init ('PAGES', 'pageid=pageid;bottomPages=false;skiponcepage=true;');

        $last = ceil ($n / $problemsPerPage);
        if ($_GET['pageid'] < 0) {
          $pageid = 0;
        } else if ($_GET['pageid'] >= $last) {
          $_GET['pageid'] = $last - 1;
        }

        while ($i < $n) {
          $c = 0;
          $arr = array ();

          $first = $list[$i];
          while ($c < $problemsPerPage && $i < $n) {
            $arr[] = $list[$i];
            $c++;
            $i++;
          }
          $last = $list[$i - 1];

          if (($page == $_GET['pageid']) || ($page == 0 && $_GET['pageid'] == '')) {
            $src = $this->Template ('problems.list.page',
                                    array ('lib' => $this,
                                           'data' => $arr,
                                           'page' => $page,
                                           'perpage' => $problemsPerPage,
                                           'acc.manage' => $manage,
                                           'acc.delete' => $delete,
                                           'acc.edit' => $edit,
                                           'acc.rejudge' => $rejudge));
          } else {
            $src = '';
          }

          $pages->AppendPage ($src,
            htmlspecialchars ($first['name']) .
              (($last['name']) ? (' .. ' . htmlspecialchars ($last['name'])) : ('')));
          $page++;
        }

        $this->CPrintLn ($pages->OuterHTML ());
        $this->CPrintLn ($this->Template ('problems.filter.form'));
      }

      function Problem_Rejudge ($id) {
        if ($this->GetAllowed ('PROBLEMS.REJUDGE')) {
          $this->problemsContainer->Rejudge ($id);
          return true;
        }
        return false;
      }

      ////
      //

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
        $p = $c['settings']['ignore_ce'];

        // Filling da `try` field
        $n = count ($arr);
        $tries = array ();

        for ($i = $n - 1; $i >= 0; $i--) {
          if (!WT_ForceStatusAffective ($arr[$i]['parameters']['force_status']) ||
              $arr[$i]['ignored'] || $arr[$i]['errors']=='CR' ||
              ($p &&$arr[$i]['errors']=='CE')) {
            $arr[$i]['try'] = $tries[$arr[$i]['user_id']][$arr[$i]['problem_id']];

            if (!isset ($arr[$i]['try'])) {
              $arr[$i]['try'] = 0;
            }

            continue;
          }

          $arr[$i]['try'] = ++$tries[$arr[$i]['user_id']][$arr[$i]['problem_id']];
        }

        return $arr;
      }

      function GetSolutionsCountEntry ($contest_id = -1, $clause = '') {
        global $WT_contest_id;

        if ($contest_id = -1) {
          $contest_id = $WT_contest_id;
        }

        return db_count ('tester_solutions',
                         "`contest_id`=$contest_id".
                         (($clause != '') ? (' AND '.$clause) : (''))."");
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

      function GetUserSolutionsCount ($contest_id = -1, $user_id) {
        return $this->GetSolutionsCountEntry ($contest_id,
                                              "`user_id`=$user_id");
      }

      function GetAllSolutionsCount ($contest_id = -1) {
        return $this->GetSolutionsCountEntry ($contest_id);
      }

      ////////////
      // Pages

      function PAGE_Problems () {
        global $WT_contest_id, $act, $id, $CORE;

        $this->gateway->AppendNavigation ('Список задач', '.?page=problems');

        $manage = $this->IsContestJudge ();

        if (!WT_contest_running ($WT_contest_id) &&
            !WT_contest_finished ($WT_contest_id) && !$manage) {
          $this->CPrintLn ('<span class="contentSub2">Список задач '.
                           'недоступен, так как контест незапущен.</span>');
          return;
        }

        $this->InsertTemplate ('problems.page', array ('lib' => $this));

        ////
        //

        if ($this->GetAllowed ('CONTEST.MANAGE')) {
          $this->AppendQuickLink ('Редактировать контест',
                                  '?page=contest&action=manage&id='.$WT_contest_id);
        }

        if ($this->GetAllowed ('PROBLEMS.CREATE')) {
          $this->AppendQuickLink ('Создать задачу',
                                  '?page=prbmanager&action=showcreate'.
                                  '&promtadd=1&redirect='.get_redirection ());
        }
      }

      function PAGE_Monitor () {
        global $WT_contest_id;

        //
        // TODO:
        //  Add checinkg stuff here
        //

        if (!WT_contest_running ($WT_contest_id) &&
            !WT_contest_finished ($WT_contest_id) && !$this->IsContestJudge ()) {
          return;
        }

        $this->gateway->AppendNavigation ('Монитор', '.?page=monitor');

        $lib = $this->SpawnRulesLib ();
        $this->InsertTemplate ('monitor.common');
        $lib->Monitor ();

        $this->AppendQuickLink ('В отдельном окне',
                                'JavaScript:monitor2fullscreen();');
      }

      function PAGE_Submit () {
        global $action, $WT_contest_id;

        $manage = $this->IsContestJudge ();
        redirector_add_skipvar ('action', 'submit');

        $contest = WT_contest_by_id ($WT_contest_id);

        if ($action == 'submit') {
          $url = content_url_get_full ();
          if ($contest['settings']['compilers'][WT_receive_compiler_from_selector ()]) {
            if ($this->Problem_AccessibleForSubmit ($_POST['problem_id'], user_id (), false)) {
              if ($this->PutSolution ($WT_contest_id, user_id (), $_POST['problem_id'],
                                      WT_receive_compiler_from_selector (),
                                      stripslashes ($_POST['src']))) {
                redirect ($url);
              }
            }
          }
        }

        $this->gateway->AppendNavigation ('Посылка решения задачи', '.?page=submit');

        if (WT_contest_running ($WT_contest_id) || $manage) {
          $this->CPrintLn (stencil_formo ());
          $list = $this->GetProblemsAtContest ($WT_contest_id);
          $this->InsertTemplate ('submit_form', array ('list' => $list,
                                                       'contest' => $contest,
                                                       'data' => $this));
          $this->CPrintLn (stencil_formc ());
        }
      }

      function PAGE_Status () {
        global $WT_contest_id, $action, $id;
        $manage = $this->IsContestJudge ();

        if (!WT_contest_running ($WT_contest_id) &&
            !WT_contest_finished ($WT_contest_id) && !$manage) {
          $this->CPrintLn ('<span class="contentSub2">Список задач '.
                           'недоступен, так как контест незапущен.</span>');
          return;
        }

        $this->gateway->AppendNavigation ('Статус по контесту', '.?page=submit');

        if ($action == 'view') {
          $this->Solution_DrawInformation ($id);
        }

        $this->CPrintLn (stencil_formo (''));
        $this->InsertTemplate ('status_form', array ('lib' => $this));
        $this->CPrintLn (stencil_formc ());
      }

      function PAGE_DrawCreateProblemForm ($backlink = '') {
        if (!$this->GetAllowed ('PROBLEMS.CREATE')) {
          return;
        }

        $f = $this->Problem_GenerateEditorForm ($this->Problem_GetDataFromPostData (),
                                                'create', $backlink);
        $this->CPrintLn (($f->OuterHTML ()));
      }

      function PAGE_DrawEditProblemForm ($id, $backlink = '', $tried = false) {
        if (!$this->GetAllowed ('PROBLEMS.CREATE')) {
          return;
        }

        if (!$tried) {
          $arr = $this->Problem_GetDataById ($id);
        } else {
          $arr = $this->Problem_GetDataFromPostData ();
        }

        $f = $this->Problem_GenerateEditorForm ($arr, 'save', $backlink);
        $this->CPrintLn (($f->OuterHTML ()));
      }

      function PAGE_ProblemsManager_ProblemsHandler () {
        global $action, $redirect, $id, $pageid;

        redirector_add_skipvar ('action', 'rejudge');

        if (!$this->GetAllowed ('PROBLEMS.MANAGE')) {
          return;
        }

        $create  = $this->GetAllowed ('PROBLEMS.CREATE');
        $delete  = $this->GetAllowed ('PROBLEMS.DELETE');
        $edit    = $this->GetAllowed ('PROBLEMS.EDIT');
        $rejudge = $this->GetAllowed ('PROBLEMS.REJUDGE');

        if ($edit) {
          $tried = false;
          if ($action == 'save') {
            if (!$this->problemsContainer->UpdateReceived ()) {
              $action = 'edit';
              $tried = true;
            } else {
              if ($redirect != '') {
                redirect ($redirect);
              }
              return;
            }
          } else if ($action == 'edit') {
            $this->gateway->AppendNavigation ('Редактирование задачи',
                                              '.?page=prbmanager&action=edit&id='.$id);
            $this->CPrintLn (stencil_formo ('title=Редактирование задачи;'));
            $this->PAGE_DrawEditProblemForm ($id, $redirect, $tried);
            $this->CPrintLn (stencil_formc ());
            return;
          }
        }

        if ($create) {
          if ($action == 'showcreate') {
            $this->gateway->AppendNavigation ('Создание задачи',
                                              '.?page=prbmanager&action=showcreate');
            $this->CPrintLn (stencil_formo ('title=Создать новую задачу;'));
            $this->PAGE_DrawCreateProblemForm ($redirect);
            $this->CPrintLn (stencil_formc ());
            return;
          } else if ($action == 'create') {
              $this->problemsContainer->CreateReceived ();
              if ($redirect != '') {
                redirect ($redirect);
              }
            }
        }

        if ($rejudge) {
          if ($action == 'rejudge') {
            $this->Problem_Rejudge ($id);
          }
        }

        if ($action == 'delete' && $delete) {
          $this->problemsContainer->Delete ($id);
        }

        if ($action == 'view') {
          $this->CPrintLn ('<table width="100%"><tr valign="top">'.
                           '<td width="40%" style="padding-right: 2px;">');
        }

        $this->CPrintLn (stencil_formo ('title=Список существующих задач;'));
        $this->Problem_DrawProblems ();
        $this->CPrintLn (stencil_formc ());

        if ($action == 'view') {
          $this->gateway->AppendNavigation ('Просмотр задачи', '');
          $this->CPrintLn ('</td><td style="padding-left: 2px;">');
          $this->Problem_DrawDescription ($id, $redirect);
          $this->CPrintLn ('</td></tr></table>');
        }

        if ($create) {
          $this->CPrintLn ('<script language="JavaScript" type="text/javascript">'.
                           'var descInited=false; function InitDesc () { '.
                           'if (descInited) return; '.
                           'iframeEditor_Init (\'desc\'); '.
                           'descInited=true; };</script>');
          $this->CPrintLn (stencil_dd_formo ('title=Создать новую задачу;onexpand=InitDesc ();'));
          $this->PAGE_DrawCreateProblemForm ();
          $this->CPrintLn (stencil_dd_formc ());
        }
      }

      function PAGE_ProblemsManager_CheckersHandler () {
        if (!$this->GetAllowed ('CHECKERS.MANAGE')) {
          return;
        }

        $this->InsertTemplate ('checkers.list', array ('lib' => $this));
        $this->gateway->AppendNavigation ('Управление чекерами',
                                          '?page=prbmanager&action=checkers'.
                                          (($pageid!='')?('&pageid='.$pageid):('')));
      }

      function PAGE_ProblemsManager () {
        global $action, $redirect, $id, $pageid;
        content_url_var_push_global ('pageid');
        if (!$this->GetAllowed ('PROBLEMS.MANAGE')) {
          return;
        }

        if (!$this->GetAllowed ('CHECKERS.MANAGE') && $action == 'checkers') {
          $action = '';
        }

        $this->gateway->AppendNavigation ('Управление задачами',
                                          '?page=prbmanager'.(($pageid!='')?('&pageid='.$pageid):('')));

        if ($action != 'checkers') {
          $this->PAGE_ProblemsManager_ProblemsHandler ();
          if ($this->GetAllowed ('CHECKERS.MANAGE') && $action!='showcreate') {
            $this->AppendQuickLink ('Управление чекерами',
                                    '?page=prbmanager&action=checkers');
          }
        } else {
          $this->PAGE_ProblemsManager_CheckersHandler ();
          $this->AppendQuickLink ('Управление задачами', '?page=prbmanager');
        }
      }

      function PAGE_Solutions () {
        global $pageid, $WT_contest_id;

        redirector_add_skipvar ('action');
        redirector_add_skipvar ('id');
        redirector_add_skipvar ('detail');

        if (!$this->GetAllowed ('SOLUTIONS.MANAGE')) {
          return;
        }

        $this->Solutions_ActionHandler ();
        $this->gateway->AppendNavigation ('Список решений участников олимпиады',
                                          '?page=solutions'.
                                          (($pageid!='')?('&pageid='.$pageid):('')));

        $this->CPrintLn (stencil_formo ());
        $this->InsertTemplate ('solutions_form',
                               array ('lib' => $this,
                                      'accDel' => $this->GetAllowed ('SOLUTIONS.DELETE')));
        $this->CPrintLn (stencil_formc ());
      }

      function PAGE_Megamonitor () {
        if (!$this->GetAllowed ('MONITOR.MEGAMONITOR')) {
          return;
        }

        $this->gateway->AppendNavigation ('Построение общего монитора',
                                          '?page=megamonitor');
        $this->InsertTemplate ('megamonitor', array ('lib'=>$this));
      }

      function InitIface () {
        global $WT_contest_id;

        $manage = $this->IsContestJudge ();
        $started = WT_contest_running ($WT_contest_id);
        $finished = WT_contest_finished ($WT_contest_id);
        $c = $this->GetProblemsCountAtContest ($WT_contest_id);

        if (WT_contest_running () || WT_contest_finished () ||
            $this->IsContestJudge ()) {
          $this->gateway->AppendMainMenuItem ('Монитор', '.?page=monitor', 'monitor');
        }

        if ($started || $finished || $manage)  {
          if ($c>0) $this->gateway->AppendMainMenuItem ('Список задач',
                                                        '.?page=problems' ,
                                                        'problems');
        }

        if (($started || $finished || $manage) &&
            $this->GetUserSolutionsCount ($WT_contest_id, user_id ())) {
          $this->gateway->AppendMainMenuItem ('Статус', '.?page=status', 'status');
        }

        if ($started || $manage) {
          if ($c > 0) {
            $this->gateway->AppendMainMenuItem ('Послать решение', '.?page=submit',    'submit');
          }
        }

        if ($this->GetAllowed ('SOLUTIONS.MANAGE') &&
            $this->GetAllSolutionsCount ($WT_contest_id)) {
          $this->gateway->AppendMainMenuItem ('Решения участников',
                                              '.?page=solutions',
                                              'solutions');
        }

        if ($this->GetAllowed ('PROBLEMS.MANAGE')) {
          $this->gateway->AppendMainMenuItem ('Управление задачами',
                                              '.?page=prbmanager',
                                              'prbmanager');
        }

        if ($this->GetAllowed ('MONITOR.MEGAMONITOR')) {
          $this->gateway->AppendMainMenuItem ('Мегамонитор',
                                              '.?page=megamonitor',
                                              'megamonitor');
        }
      }

      function IsContestJudge ($id=-1) {
        global $WT_contest_id;

        if ($id < 0) {
          $id = $WT_contest_id;
        }

        if (isset ($this->cache[$id]['IsContestJudge'])) {
          return $this->cache[$id]['IsContestJudge'];
        }

        $this->cache[$id]['IsContestJudge'] = $this->GetAllowed ('CONTEST.MANAGE');

        if ($this->cache[$id]['IsContestJudge']) {
          return true;
        }

        if ($id == '') {
          return;
        }

        $q = db_query ('SELECT COUNT(*) AS `c` FROM `usergroup` AS `ug`, '.
                       '`tester_judgegroup` AS `tjg` '.
                       ' WHERE (`ug`.`user_id`='.user_id ().
                       ') AND (`tjg`.`group_id`=`ug`.`group_id`) '.
                       'AND (`tjg`.`contest_id`='.$id.')');
        $r = db_row ($q);
        $res = $r['c'] > 0;
        $this->cache[$id]['IsContestJudge'] = $res;

        return $res;
      }

      function Subnav_Info () {
        global $WT_contest_id;

        if (!isset ($WT_contest_id) || $WT_contest_id == '') {
          return;
        }

        $data = WT_contest_by_id ($WT_contest_id);
        return $this->Template ('subnav_info',
                                array ('data' => $data, 'lib' => $this));
      }

      function GetLastAcceptedAtContest ($id = 1) {
        global $WT_contest_id;

        if ($id < 0) {
          $id = $WT_contest_id;
        }

        $s = WT_contest_by_id ($id);
        $s = $s['settings'];
        $arr = array ();

        $timestamp = -1;
        if ($s['duration'] && $s['freezetime'] > 0 &&
            (!$s['unfrozen'] && !$this->IsContestJudge ())) {
          $timestamp = $s['timestamp'] + ($s['duration'] - $s['freezetime']) * 60;
        }

        $user_clause = '`user_id` IN (SELECT `u`.`id` FROM `user` AS `u`, '.
          '`usergroup` AS `ug`, `tester_contestgroup` AS `tcg` WHERE '.
          '(`u`.`id`=`ug`.`user_id`) AND (`ug`.`group_id`=`tcg`.`group_id`) '.
          'AND (`tcg`.`contest_id`='.$id.') )';

        $skip_user_clause = 'NOT (`user_id` IN (SELECT `u`.`id` '.
          'FROM `user` AS `u`, `tester_judgegroup` AS `tjg`, '.
          '`usergroup` AS `ug` WHERE '.
          '(`u`.`id`=`ug`.`user_id`) AND (`ug`.`group_id`=`tjg`.`group_id`) '.
          'AND (`tjg`.`contest_id`='.$id.')))';

        $arr = db_row_value ('tester_solutions', " ($user_clause) AND ".
                             "$skip_user_clause AND (`contest_id`=$id) ".
                             "AND (`status`=2) AND (`errors`=\"OK\")".
                             (($timestamp > 0)?(" AND (`timestamp`<=$timestamp)"):(''))."",
                             "ORDER BY `timestamp` DESC");

        return $arr;
      }

      function GetSolutionTime ($solution) {
        $contest = WT_contest_by_id ($solution['contest_id']);
        if ($contest['settings']['duration'] == 0) {
          return format_date_time ($solution['timestamp']);
        } else {
          return Timer ($solution['timestamp']-$contest['settings']['timestamp']);
        }
      }

      function GetTestsForProblem ($problem_id) {
        return $this->problemsContainer->GetTests ($problem_id);
      }
    }

    function INFORMATICS_GenerateProblemEditorForm () {
      global $INFORMATICS_ProblemSettingsFields;
      $fields = $INFORMATICS_ProblemSettingsFields;
      $arr = array ();
      $arr['desc'] = strip_suspicious (stripslashes ($_POST['desc']));
  
      for ($i = 0, $n = count ($fields); $i < $n; $i++) {
        $f = $fields[$i];
        $arr[$f['name']] = FormPOSTValue ($f['postname'], 'ProblemSettings');
      }

      return $arr;
    }

    function INFORMATICS_SpawnNewProblemsContainer ($lib) {
      global $INFORMATICS_problemsContainer;
      if ($INFORMATICS_problemsContainer == nil) {
        $INFORMATICS_problemsContainer = new CGMInformaticsProblemsContainer ($lib);
        $INFORMATICS_problemsContainer->Init ();
      }

      return $INFORMATICS_problemsContainer;
    }

    function INFORMATICS_tests_info ($pid, $tests, $perline = 10,
                                     $gen_links = false, $links_url = '') {
      $arr = explode (' ', $tests);
      $res = '<table class="data tests_info">'."\n";

      if ($links_url == '') {
        $links_url = content_url_get_full ();
      }

      $opened = false;
      $n = count ($arr);
      $i = 0;

      while ($i < $n) {
        $m = min ($perline, $n-$i);
        $res .= '  <tr>'."\n    ";
        for ($j = 0; $j < $m; $j++) {
          if ($gen_links) {
            $link_pre = '<a href="'.$links_url.'#test'.($i + 1).'">';
            $link_post = '</a>';
          }
          $res .= '<th>'.$link_pre.($i+1).$link_post.'</th>';
          $i++;
        }

        for ($j = $m; $j < $perline; $j++) {
          $res .= '<td class="void"></td>';
        }

        $res .= '  </tr>'."\n";
        $i -= $m;
        $res .= '  <tr>'."\n    ";

        for ($j = 0; $j < $m; $j++) {
          if ($gen_links) {
            $link_pre = '<a href="'.$links_url.'#test'.($i + 1).'">';
            $link_post = '</a>';
          }
          $res .= '<td'.(($arr[$i]=='OK')?(' style="background: #cfc"'):('')).'>'.
            $link_pre.$arr[$i].$link_post.'</td>';
          $i++;
        }

        for ($j = $m; $j < $perline; $j++) {
          $res .= '<td class="void"></td>';
        }

        $res .= '  </tr>'."\n";
      }

      $res .= '</table>'."\n";

      return $res;
    }

    WT_library_register ('CGMInformatics', 'Informatics', 0);
  }
?>
