<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Web-interface for WebTester Server
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

  if ($_gateway_included_ != '##gateway_Included##') {
    $_gateway_included_ = '##gateway_Included##';

    global $WT_gateway;

    $WT_main_menu = array (
        array ('Список контестов', '?page=contest', 'contest',
               'Page_Contest', ACCESS_GUEST),
      );

    $WT_gateway=nil;

    class CGateway extends CVirtual {
      var $main_menu;
      var $nav;
      var $security;
      var $content_settings;
      var $current_contest;
      var $LIB_Handlers = array ();
      var $qlinks = array ();

      ////////
      // DB stuff
      function CheckTables () {
        if (!config_get ('check-database')) {
          return;
        }

        if (!db_table_exists ('tester')) {
          db_create_table_safe ('tester', array (
                                'security'  => 'TEXT DEFAULT ""',
                                'content'   => 'TEXT DEFAULT ""'
                                ));

          db_insert ('tester', array ('security' => '"'.serialize (array ()).'"',
                                      'content' => '"'.serialize (array ()).'"'));
        }

        if (!db_table_exists ('tester_problems')) {
          db_create_table_safe ('tester_problems', array (
                                  'id'          => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                  'lid'         => 'INT',
                                  'name'        => 'TEXT DEFAULT ""',
                                  'description' => 'LONGTEXT DEFAULT ""',
                                  'settings'    => 'LONGTEXT DEFAULT ""',
                                  'uploaded'    => 'INT DEFAULT 0'
                                 ));

          manage_settings_create ('Количество задач на странице редактирования',
                                  'Олимпиады', 'WT_problems_per_page', 'CSCNumber');

          manage_settings_create ('Количество задач на странице браузера',
                                  'Олимпиады', 'WT_problems_per_browser_page',
                                  'CSCNumber');

          manage_settings_create ('Количество записей на странице статуса',
                                  'Олимпиады', 'WT_items_per_status_page', 'CSCNumber');

          opt_set ('WT_problems_per_page', 15);
          opt_set ('WT_problems_per_browser_page', 10);
          opt_set ('WT_items_per_status_page', 15);
          manage_setting_use ('WT_problems_per_page');
          manage_setting_use ('WT_items_per_status_page');
          manage_setting_use ('WT_problems_per_browser_page');
        }

        if (!db_table_exists ('tester_tasks')) {
          db_create_table_safe ('tester_tasks', array (
                                  'id'          => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                  'contest_id'  => 'INT',
                                  'problem_id'  => 'INT',
                                  'letter'      => 'INT',
                                  'catid'       => 'INT DEFAULT 0',
                                  'settings'    => 'LONGTEXT DEFAULT ""'
                                                       ));
        }
        if (!db_table_exists ('tester_checkers')) {
          db_create_table_safe ('tester_checkers', array (
                                  'id'          => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                  'name'        => 'LONGTEXT',
                                  'uploaded'    => 'BOOL DEFAULT 0',
                                  'settings'    => 'LONGTEXT DEFAULT ""'
                                ));
        }

        if (!db_table_exists ('tester_solutions')) {
          db_create_table_safe ('tester_solutions', array (
                                  'id'          => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                  'lid'         => 'INT',
                                  'contest_id'  => 'INT',
                                  'problem_id'  => 'INT',
                                  'user_id'     => 'INT',
                                  'timestamp'   => 'INT',
                                  'status'      => 'INT',
                                  'parameters'  => 'LONGTEXT DEFAULT ""',
                                  'errors'      => 'TEXT',
                                  'points'      => 'INT',
                                  'ignored'     => 'BOOL DEFAULT 0'
                                 ));
        }

        if (!db_table_exists ('tester_contestgroup')) {
          db_create_table_safe ('tester_contestgroup', array (
                                  'contest_id'  => 'INT',
                                  'group_id'    => 'INT'
                               ));
        }

        if (!db_table_exists ('tester_judgegroup')) {
          db_create_table_safe ('tester_judgegroup', array (
                                  'contest_id'  => 'INT',
                                  'group_id'    => 'INT'
                                ));
        }

        if (!db_table_exists ('tester_disabled_problems')) {
          db_create_table_safe ('tester_disabled_problems', array (
                                  'contest_id'    => 'INT',
                                  'problem_id'    => 'INT'
                                ));
        }
      }

      function InitMenus () {
        global $WT_main_menu;
        $this->main_menu = new CVCMenu ();
        $this->main_menu->Init ('GWMainMenu', 'type=hor;colorized=true;border=thin');

        $n = count ($WT_main_menu);

        for ($i = 0; $i < $n; $i++)
          $this->AppendMainMenuItem ($WT_main_menu[$i][0],
                                     $WT_main_menu[$i][1],
                                     $WT_main_menu[$i][2],
                                     $WT_main_menu[$i][4]);
      }

      function UpdateCurrentContest () {
        global $WT_contest_id;

        $cnt = $this->SpawnContestContainer ();
        $list = $cnt->GetAccessibleList (user_id ());
        $this->current_contest = $cnt->ContestById ($WT_contest_id);
        if (!isset ($WT_contest_id) || !isset ($this->current_contest['id'])) {
          session_register ('WT_contest_id');
          $this->current_contest = (isset ($list[0]))?($list[0]):(array ());
          $WT_contest_id = $this->current_contest['id'];
        }
      }

      function CGateway () {
        global $page, $WT_main_menu, $CORE, $WT_gateway;

        $this->SetClassName ('CGateway');
        $this->CheckTables ();
        $this->InitMenus ();
        $this->page = ($page != '') ? ($page) : ($WT_main_menu[0][2]);

        $this->nav = array ();
        $this->security = new CGWSecurityInformation ();
        $this->security->Init ('WT_security');
      
        $r = db_row_value ('tester');

        $this->security->UnserializeData ($r['security']);
        $this->content_settings=unserialize ($r['content']);

        $WT_gateway = $this;

        $CORE->PAGE->AppendTitle ('WebTester');
        $CORE->AddStyle ('tester');
        $this->UpdateCurrentContest ();

        $this->current_lib =
          WT_spawn_new_library ($this->current_contest['lid'], $this);
      }
    
      function SpawnContestContainer () {
        return WT_spawn_new_contest_container ();
      }

      function SpawnLibraryContainer () {
        return WT_spawn_new_library_container ();
      }

      ////////
      //

      function AppendMainMenuItem ($title, $url, $tag, $access = 0) {
        if ($access != '' && user_access () < $access) {
          return;
        }

        $this->main_menu->AppendItem ($title, $url, $tag);
      }

      function  AppendLIBHandler ($lid, $name, $handler) {
        $this->LIB_Handlers[$lid][$name] = $handler;
      }

      function AppendNavigation ($title, $url, $hint = '') {
        $this->nav[] = array ('title' => $title,
                              'url' => $url, 'hint' => $hint);
      }

      function PrependNavigation ($title, $url, $hint = '') {
        $t = $this->nav;
        $this->nav=array ();
        $this->AppendNavigation ($title, $url, $hint);

        for ($i = 0, $n = count ($t); $i < $n; $i++) {
          $this->nav[] = $t[$i];
        }
      }

      function CPrint      ($text) { $this->content.=$text; }
      function CPrintLn    ($text) { $this->content.=$text."\n"; }
      function GetContent  ()      { return $this->content; }
    
      function AppendQuickLink ($cpt, $url) {
        $this->qlinks[] = array ('cpt' => $cpt, 'url' => $url);
      }
    
      ////////
      //

      function GetCurrentContest () {
        return $this->current_contest;
      }

      ////////
      // Outputing stuff

      function InsertTemplate ($name, $args = array ()) {
        $this->CPrintLn (tpl ('front/tester/'.$name, $args));
      }

      function DrawNavigator () {
        if (!isset ($this->current_contest['id'])) {
          $this->PrependNavigation ('Олимпиады', '/');
        } else {
          $this->PrependNavigation ($this->current_contest['name'],
                                    '/?action=manage&id='.$this->current_contest['id'],
                                    'Управление контестом');
        }

        $root = config_get ('document-root').'/tester';
        $n = count ($this->nav);
        print ('<div id="snavigator">');

        for ($i = 0; $i < $n - 1; $i++) {
          print ('<a href="'.$root.$this->nav[$i]['url'].'"'.
                 (($this->nav[$i]['hint']!='')?('title="'.$this->nav[$i]['hint'].'"'):('')).
                 '>'.$this->nav[$i]['title'].'</a>');
        }

        print ($this->nav[$n-1]['title']);
        println ('</div>');

        if (wiki_admin_page ()) {
          global $CORE;
          $CORE->PAGE->AppendTitle ('Администрирование', true);
          println ('<div class="contentSub">Администрирование</div>');
        } else {
          if ($this->current_contest['lid'] != '') {
            $lib = WT_spawn_new_library ($this->current_contest['lid'], $this);
            $sub = $lib->Subnav_Info ();

            if (trim ($sub) != '') {
              println ('<div class="contentSub">'.$sub.'</div>');
            }
          }
        }
      }

      function AdminMode () {
        $static=config_get ('static-privacy-rules');
        return user_access_root () || $static[strtolower (user_login ())]['/tester/admin.php']=='ROOT';
      }

      function DrawManage () {
        global $action;
 
       if (!$this->AdminMode ()) {
         println ('HACKERS?');
         return;
       }

        if ($action == 'save') {
          $this->Handle_SaveSecurity ();
          $this->Handle_SaveIFRAMESettings ();
        }

        $this->InsertTemplate ('admin', array ('self' => $this));
      }

      function Draw () {
        $this->DrawNavigator ();

        if ($this->current_contest['lid']!='') {
          $lib = WT_spawn_new_library ($this->current_contest['lid']);

          if ($lib != nil) {
            $lib->InitIface ();
          }
        }

        if (wiki_admin_page () && $this->AdminMode ()) {
          $this->DrawManage ();
          print ($this->content);
          return;
        }

        $this->main_menu->SetActive ($this->page);
        $this->main_menu->Draw ();
        println ('${information}');
        print ($this->content);

        if (count ($this->qlinks) > 0) {
          println ('<div class="cats" style="margin-top: 6px">');
          println ('  <span>Быстрые ссылки:</span>');

          for ($i = 0; $i < count ($this->qlinks); $i++) {
            println ('  '.(($i>0)?('::'):('')).'<a href="'.$this->qlinks[$i]['url'].
                     '">'.$this->qlinks[$i]['cpt'].'</a>');
          }
          println ('</div>');
        }
      }

      ////////
      //
      function GetAllowed ($action) {
        return $this->security->GetAllowed ($action);
      }

      ////////
      // Handlers
      function Handle () {
        global $WT_main_menu;
        content_url_var_push_global ('page');

        if (wiki_admin_page ()) {
          return false;
        }

        // Local handlers
        $handler = '';
        $n = count ($WT_main_menu);

        for ($i = 0; $i < $n; $i++) {
          if ($WT_main_menu[$i][2] == $this->page) {
            $handler = $WT_main_menu[$i][3];
            break;
          }
        }

        if ($handler) {
          $this->$handler ();
          return true;
        }

        // Library handlers
        $handler =
          $this->LIB_Handlers[$this->current_contest['lid']][$this->page];

        if (isset ($handler)) {
          $lib = WT_spawn_new_library ($this->current_contest['lid']);
          $lib->$handler ();
        }
      }

      function SwitchToContest ($id) {
        $cnt = WT_spawn_new_contest_container ();
        $new = $cnt->ContestById ($id);

        if (isset ($new['id'])) {
          global $WT_contest_id;
          $this->current_contest=$new;
          $WT_contest_id = $new['id'];
        }
      }

      ////////
      // Pages

      function Page_DrawContentCreate () {
        $this->CPrintLn (stencil_dd_formo ('title=Создать новый контест'));
        $this->InsertTemplate ('contest.create');
        $this->CPrintLn (stencil_dd_formc ());
      }

      function Page_ContestManager ($id, $clear = false) {
        $this->AppendNavigation ('Управление контестом',
                                 '/?page=contest&action=manage&id'.$id);
        $cpt = '';

        if ($clear) {
          $cpt = WT_contest_clear_manage_caption ($id);
        }

        if ($cpt=='') {
          $cpt = 'Управление контестом';
        }

        $this->CPrintLn (stencil_formo ('title='.prepare_arg ($cpt).';'));
        WT_draw_contest_manage_form ($id, $clear);
        $this->CPrintLn (stencil_formc ());
      }

      function Page_Contest () {
        global $action, $id, $changeto, $clear;
        $this->AppendNavigation ('Список контестов', '/?page=contest');

        redirector_add_skipvar ('action', 'restart');
        redirector_add_skipvar ('action', 'start');
        redirector_add_skipvar ('action', 'delete');

        if ($action != 'manage') {
          redirector_add_skipvar ('id');
        }

        $ccnt=$this->SpawnContestContainer ();

        $create = $this->GetAllowed ('CONTEST.CREATE');
        $del    = $this->GetAllowed ('CONTEST.DELETE');
        $manage = $this->GetAllowed ('CONTEST.MANAGE');

        $pageManage=$action == 'manage' && $manage;

        if (!isset ($clear)) {
          if (isset ($changeto)) {
            $this->SwitchToContest ($changeto);
            if ($manage) {
              redirect ('.?page=contest&action=manage&id='.$changeto);
            }
          }

          if ($action == 'create' && $create)  {
            $ccnt->CreateReceived ();
            redirect ('SELF', array ('action' => ''));
          }

          if ($action == 'delete' && $del) {
            $ccnt->Delete ($id);
            redirect ('SELF', array ('action' => '', 'id' => ''));
          }

          if ($manage) {
            if ($action == 'stop' || $action == 'start' ||
                $action == 'restart' )  {
              $ccnt->$action ($id);
            }

            if ($action == 'save' && $manage)  {
              $cnt = WT_spawn_new_contest_container (0);
              $action = 'manage';
              $cnt->Save ($id);
            }
          }

          if ($action == 'manage' && !$manage) {
            unset ($action);
            unset ($id);
          }

          if ($pageManage) {
            $this->CPrintLn ('<table width="100%"><tr valign="top">'.
                             '<td width="50%" style="padding-right: 2px;">');
          }

          $this->CPrintLn (stencil_formo ('title=Список доступных контестов;'.
                ((!$this->GetAllowed ('CONTEST.CREATE'))?('smb=true;'):(''))));

          $this->InsertTemplate ('contest.list',
                                 array ('data' => $ccnt->GetAccessibleList (user_id ()),
                                        'current_contest' => $this->current_contest,
                                        'accManage' => $manage, 'accDel' => $del));
          $this->CPrintLn (stencil_formc ());

          if ($pageManage) {
            $this->CPrintLn ('</td><td style="padding-left: 2px;">');
            $this->Page_ContestManager ($id);
            $this->CPrintLn ('</td></tr></table>');
          }

          if ($this->GetAllowed ('CONTEST.CREATE')) {
            $this->Page_DrawContentCreate  ();
          }
        } else {
          if ($pageManage) {
            $this->Page_ContestManager ($id, true);
          }
        }
      }

      function Handle_SaveSecurity () { 
        $this->security->ReceiveData ();
        db_update ('tester',
                   array ('security' =>
                          db_string ($this->security->SerializeData ())));
      }

      function Handle_SaveIFRAMESettings () {
        $iframe = new CDCIFrame ();
        $iframe->Init ();
        $iframe->ReceiveContentSettings ('', 'null');

        $arr = array ('iframe' => $iframe->settings);
        $this->content_settings = $arr;

        db_update ('tester', array ('content' => db_string (serialize ($arr))));
      }
    }

    function WT_spawn_new_gateway () {
      global $WT_gateway;

      if ($WT_gateway != nil) {
        return $WT_gateway;
      }

      $WT_gateway = new CGateway ();
      return $WT_gateway;
    }

    function WT_CalculateMaxPoints ($tests, $bonus = 0) {
      $arr = explode (' ', $tests);
      $n = count ($arr);
      $res = 0;

      for ($i = 0; $i < $n; $i++) {
        $res += $arr[$i];
      }

      return $res + $bonus;
    }
  }
?>
