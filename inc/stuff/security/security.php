<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Security checking stuff
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

  if ($_sec_included_ != '#sec_Included#') {
    $_sec_included_ = '#sec_Included#';

    define ('ACCESS_GUEST', 0);
    define ('ACCESS_USER',  1);
    define ('ACCESS_ROOT',  7);
    define ('CORRECT_LOGIN', 'random_seed');

    $security_orders = array ('allow_deny' => 'Разрешить, запретить',
                              'deny_allow' => 'Запретить, разрешить');
    $security_actions=array ('AllowAll'    => 'Разрешить всем',
                             'AllowUser'   => 'Разрешить пользователю',
                             'DenyUser'    => 'Запретить пользователю',
                             'AllowGroup'  => 'Разрешить группе',
                             'DenyGroup'   => 'Запретить группе',
                             'DenyAll'     =>'Запретить всем');

    class CSecurityInformation extends CVirtual {
      var $data;
      var $name;
      var $canInherit;
      var $precompiled = array ();

      var $security_limits = array ('ALL' => 'Все', 'READ' => 'Чтение',
                                    'EDIT'       => 'Изменение',
                                    'DELETE'     => 'Удаление',
                                    'ADDINFO'    => 'Добавление информации',
                                    'EDITINFO'   => 'Изменение информации',
                                    'DELETEINFO' =>'Удаление информации');

      function CSecurityInformation () {
        $this->SetClassName ('CSecurityInformation');
      }

      function Init ($name = '', $data = null) {
        $this->SetDefaultData ();
        $this->SetName ($name);
        $this->canInherit = true;

        if ($data != null) {
          $this->SetData ($data);
        }

        $this->RefreshCachedData ();
      }

      function SetDefaultData () {
        $this->data = array ('inherit'=>true);
      }

      function SetData ($data) {
        $this->data = $data;
        $this->RefreshCachedData ();
      }

      function UnserializeData ($d) {
        $d = unserialize ($d);
        $this->SetData ($d);
        $this->RefreshCachedData ();
      }

      function ReceiveData () {
        global $security_actions, $security_orders;
        $res = array ();
        $data = $_POST[$this->name.'_postdata'];
        $limits = db_unpack ($data);

        if ($_POST[$this->name.'_inherit'] && $this->canInherit) {
          $res['inherit']=true;
        } else {
          foreach ($limits as $l=>$v) {
            if (preg_match ('/\{\{\{\{GROUP[0-9]+\}\}\}\}/', $l) ||
                !isset ($this->security_limits[$l])) {
              continue;
            }

            $limit = db_unpack ($v);
            $order = $limit['order'];
            $res[$l]['order'] = $order;

            if (!isset ($security_orders[$order])) {
              continue;
            }

            $acts = db_unpack ($limit['acts']);
            $i = 0;

            while (isset ($acts[$i])) {
              $act = db_unpack ($acts[$i]);
              if (isset ($security_actions[$act['act']]))
                $res[$l]['acts'][] = array ('act' => $act['act'],
                                            'val' => $act['val']);
              $i++;
            }
          }
        }
        $this->data = $res;
        $this->RefreshCachedData ();
      }

      function SerializeData () { return serialize ($this->data); }

      function GetData ()    { return $this->data; }
      function GetInherit () { return $this->data['inherit']; }

      function EditForm () {
        global $CORE, $security_orders, $security_actions;
        global $securoty_builtin_included;

        if (!isset ($securoty_builtin_included)) {
          $CORE->AddScript ( 'language=JavaScript;',
                             tpl ('back/security/script',
                                  array ('limits' => $this->security_limits,
                                         'orders' => $security_orders,
                                         'actions' => $security_actions),
                                  false));
          $CORE->AddStyle ('security');
          $securoty_builtin_included = true;
        }

        print tpl ('back/security/edit_form',
                   array ('name' => $this->name, 'data' => $this->data,
                          'canInherit' => $this->canInherit,
                          'limits' => $this->security_limits));
      }

      function GetName ()   { return $this->name; }
      function SetName ($v) { $this->name = $v; }

      function GetCanInherit () { return $this->canInherit; }
      function SetCanInherit ($v) {
        $this->canInherit = $v;
        $this->data['inherit'] = $v;
      }

      function RefreshCachedData ($user_id=-1) {
        if (isset ($this->precompiled[$user_id]['user_group'])) {
          return;
        }

        $user_group = array ();

        if ($user_id > 0) {
          $q = db_select ('usergroup', array ('group_id'), "`user_id`=$user_id");
          while ($r=db_row ($q)) {
            $user_group[$r['group_id']] = true;
          }
        }

        $this->precompiled[$user_id]['user_group'] = $user_group;
      }

      function GetAllowedByUser_entry ($uid, $action) {
        $user_group = $this->precompiled[$uid]['user_group'];
        $data = $this->data[$action];
        $allowed = $data['order']=='allow_deny';
        $acts = $data['acts'];

        for ($i = 0; $i < count ($acts); $i++) {
          $act = $acts[$i];
          if ($act['act'] ==' AllowAll')  $allowed=true;
          if ($act['act'] == 'AllowUser') if ($act['val'] == $uid) $allowed = true;
          if ($act['act'] == 'DenyUser')  if ($act['val'] == $uid) $allowed = false;
          if ($act['act'] == 'AllowGroup') if (isset ($user_group[$act['val']])) $allowed = true;
          if ($act['act'] == 'DenyGroup')  if (isset ($user_group[$act['val']])) $allowed = false;
          if ($act['act'] == 'DenyAll')   $allowed = false;
        }
        return $allowed;
      }

      function GetAllowedToUser ($uid, $action) {
        $this->RefreshCachedData ($uid);
        if (isset ($this->data[$action]))
          return $this->GetAllowedByUser_entry ($uid, $action); else
          return $this->GetAllowedByUser_entry ($uid, 'ALL');
      }

      function GetAllowed ($action) {
        return $this->GetAllowedToUser (user_id (), $action);
      }
    }

    function security_initialize () {
      if (config_get ('check-database')) {
        if (!db_table_exists ('user')) {
          db_create_table ('user', array (
            'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'name'       => 'TEXT',
            'login'      => 'TEXT',
            'password'   => 'TEXT',
            'access'     => 'INT',
            'email'      => 'TEXT',
            'authorized' => 'BOOL',
            'timestamp'  => 'INT DEFAULT 0',
            'last_act'   => 'INT DEFAULT 0',
            'settings'   => 'TEXT DEFAULT ""'
          ));

          db_insert ('user', array ('name' => '"root"',
                                    'login' => '"root"',
                                    'password' =>'MD5("root#RANDOM_SEED#assword")',
                                    'access' => '7',
                                    'authorized' => '1',
                                    'settings' => '""',
                                    'email' => '"postmaster@localhost"'));
        }

        db_create_table_safe ('group', array (
                                'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                'name'       => 'TEXT',
                                'default'    => 'INT',
                                'refcount'   => 'INT DEFAULT 0',
                                'settings'   => 'TEXT DEFAULT ""'
                             ));
        db_create_table_safe ('usergroup', array (
                                'id'         => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                                'user_id'    => 'INT',
                                'group_id'   => 'INT'
                             ));
      }
    }

    function security_spawn_data_information ($data = null) {
      $c = new CSecurityInformation ();
      $c->Init ($data);
      return $c;
    }
  
    function security_groups () {
      return array (
        array ('title'=>'Гость', 'access'=>'0', 'desc'=>'Гость, которому запрещено внесение любых изменений в структуру и содеожимое сайта.'),
        array ('title'=>'Пользователь', 'access'=>'1', 'desc'=>'Обычный пользователь, который может вносить изменения в содержимое сайта.'),
        array ('title'=>'Администратор', 'access'=>'7', 'desc'=>'Администратор сайта. Имеет доступ к полному управлению структуры, пользователей и настройкам сайта.')
                    );
    }

    function security_group_by_access ($acc) {
      $arr = security_groups ();
      for ($i = 0; $i < count ($arr); $i++) {
        if ($arr[$i]['access'] == $acc) {
          return $arr[$i];
        }
      }
      return $arr;
    }

    function security_group_name_by_access ($acc) {
      $arr = security_group_by_access ($acc);
      return $arr['title'];
    }

    function security_group_desc_by_access ($acc) {
      $arr = security_group_by_access ($acc);
      return $arr['desc'];
    }

    function security_access_title ($a) {
      $arr = security_groups ();

      for ($i=0; $i<count ($arr); $i++) {
        if ($arr[$i]['access'] == $a) {
          return $arr[$i]['title'];
        }
      }

      return '<Неизвестно>';
    }
  }
?>
