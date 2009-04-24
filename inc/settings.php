<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Settings manipulation
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

  if ($_manage_config_included_ != '#manage_config_Included#') {
    $_manage_config_included_ = '#manage_config_Included#';
    $manage_config_classes = array ();

    /////////////////////
    // Setting classes

    class CSDTCVirtual extends CVirtual {
      var $ident, $settings;

      function Init ($id) { $this->ident = $id; }
      function ConfigFormSource () { return ''; }
      function ReceiveSettings ()  {  }
      function CheckScript ()      { return ''; }
      function Draw () { println ( $this->ConfigFormSource ()); }
      function GetValue ()     { return $this->settings['value']; }
      function SetValue ($v)   { $this->settings['value']=$v; }
    }

    class CSCText extends CSDTCVirtual { // Simple text setting

      function Init ($id, $text = '') {
        $this->ident = $id;
        $this->settings['value'] = $text;
      }

      function ConfigFormSource () {
        return '<input type="text" value="'.htmlspecialchars ($this->settings['value']).'" name="setting_'.$this->ident.'" class="txt block">';
      }

      function ReceiveSettings () {
        global $_POST;
        $this->settings['value'] = stripslashes ($_POST['setting_'.$this->ident]);
      }
    }

    class CSCPassword extends CSCText { // Password entry
      function ConfigFormSource () {
        return '<input type="password" value="'.htmlspecialchars ($this->settings['value']).
            '" name="setting_'.$this->ident.'" class="txt block">';
      }
    }

    class CSCCheckBox extends CSDTCVirtual { // CheckBox-based setting
      function Init ($id, $checked = false) {
        $this->ident = $id;
        $this->settings['value'] = $checked;
      }

      function ConfigFormSource () {
        return 'Активен&nbsp;<input type="checkbox" class="cb" name="setting_'.
            $this->ident.'" value="1"'.
            (($this->settings['value'])?(' checked'):('')).'>';
      }

      function ReceiveSettings () {
        global $_POST;
        if ($_POST['setting_'.$this->ident]) {
          $this->settings['value'] = true;
        } else {
          $this->settings['value'] = false;
        }
      }

      function SetValue ($v) {
        if ($v) {
          $this->settings['value'] = true;
        } else {
          $this->settings['value'] = false;
        }
      }
    }

    class CSCNumber extends CSDTCVirtual { // Nuber setting
      function Init ($id, $value = '0') {
        if (!isnumber ($value)) {
          $value = 0;
        }
        $this->ident = $id;
        $this->settings['value'] = $value;
      }

      function ConfigFormSource () {
        return '<input type="text" value="'.$this->settings['value'].
          '" name="setting_'.$this->ident.'" id="setting_'.
          $this->ident.'" class="txt block">';
      }

      function ReceiveSettings () {
        global $_POST;
        $value = $_POST['setting_'.$this->ident];
        if (!isnumber ($value)) {
          $value = 0;
        }
        $this->settings['value']=$value;
      }

      function CheckScript () {
        return 'if (!isnumber (getElementById (\'setting_'.$this->ident.'\').value)) {alert (\'Поле `'.$this->ident.'`: \'+getElementById (\'setting_'.$this->ident.'\').value+\' не является корректным числом.\'); return false;}';
      }

      function SetValue ($v) {
        if (isnumber ($v)) {
          $this->settings['value']=$v;
        } else {
          $this->settings['value']=0;
        }
      }
    }

    class CSCSignedNumber extends CSDTCVirtual { // Signed nuber setting
      function Init ($id, $value = '0') {
        if (!isnumber ($value, true)) {
          $value = 0;
        }
        $this->ident = $id;
        $this->settings['value'] = $value;
      }

      function ConfigFormSource () {
        return '<input type="text" value="'.$this->settings['value'].
          '" name="setting_'.$this->ident.'" id="setting_'.
          $this->ident.'" class="txt block">';
      }

      function ReceiveSettings () {
        global $_POST;
        $value = $_POST['setting_'.$this->ident];

        if (!isnumber ($value, true)) {
          $value = 0;
        }

        $this->settings['value']=$value;
      }

      function CheckScript () {
        return 'if (!isSignedNumber (getElementById (\'setting_'.
          $this->ident.'\').value)) {alert (\'Поле `'.
          $this->ident.'`: \'+getElementById (\'setting_'.
          $this->ident.'\').value+\' не является корректным '.
          'числом со знаком.\'); return false;}';
      }

      function SetValue ($v) {
        if (isnumber ($v, true)) {
          $this->settings['value'] = $v;
        } else {
          $this->settings['value'] = 0;
        }
      }
    }

    class CSCEmail extends CSDTCVirtual { // E-Mail setting
      function Init ($id, $addr = 'localadmin@localhost') {
        if (!check_email ($addr)) {
          $addr = 'localadmin@localhost';
        }
        $this->ident = $id;
        $this->settings['value'] = $addr;
      }

      function ConfigFormSource () {
        return '<input type="text" value="'.$this->settings['value'].
          '" name="setting_'.$this->ident.'" id="setting_'.$this->ident.
          '" class="txt block">';
      }

      function ReceiveSettings () {
        global $_POST;
        $addr = $_POST['setting_'.$this->ident];

        if (!check_email ($addr)) {
          $addr = 'localadmin@localhost';
        }

        $this->settings['value']=$addr;
      }

      function CheckScript () {
        return 'if (!check_email (getElementById (\'setting_'.
          $this->ident.'\').value)) {alert (\'Поле `'.$this->ident.
          '`: \'+getElementById (\'setting_'.$this->ident.
          '\').value+\' не является корректным адресом '.
          'электронной почты.\'); return false;}';
      }

      function SetValue ($v) {
        if (check_email ($v)) {
          $this->settings['value'] = $v;
        } else {
          $this->settings['value'] = ' ';
        }
      }
    }

    ////////////////////
    // Setting's stuff

    function manage_settings_create ($name, $section, $ident, $classname)  {
      global $_POST;
      $name = addslashes (htmlspecialchars (trim ($name)));
      $section = addslashes (htmlspecialchars (trim ($section)));
      $ident = trim ($ident);

      if ($name == '' || $section == '' || $ident == '' ||
          !isalphanum ($ident)) {
        return;
      }

      if (db_count ('settings', '`ident`="'.$ident.'"')>0) {
        add_info ('Опция с таким именем уже существует');
        return false;
      } else {
        $t = new $classname;
        $t->Init ($ident);
        $settings = addslashes ($t->SerializeSettings ());
        db_insert ('settings', array ('name' => "\"$name\"",
                                      'section' => "\"$section\"",
                                      'ident' => "\"$ident\"",
                                      'class' => "\"$classname\"",
                                      'settings' => "\"$settings\""));
        return true;
      }
    }
  
    function manage_settings_create_received () {
      if (manage_settings_create (stripslashes ($_POST['name']),
                                  stripslashes ($_POST['section']),
                                  $_POST['ident'], $_POST['classname'])) {
        $_POST = array ();
      }
    }

    function manage_settings_get_sections () {
      $q = db_query ('SELECT `section` FROM `settings` GROUP '.
                     'BY `section` ORDER BY `section`');
      $arr = array ();

      while ($r = db_row ($q)) {
        $arr[] = $r['section'];
      }

      return $arr;
    }

    function manage_settings_get_section_elements ($section) {
      return arr_from_query ('SELECT * FROM `settings` WHERE `section`="'.
                             $section.'" ORDER BY `name`');
    }

    function manage_settings_get_section_element ($id) {
      return db_row (db_query ('SELECT * FROM `settings` WHERE `id`='.$id));
    }
  
    function manage_settings_update_name ($id) {
      global $_POST;

      $name = htmlspecialchars (trim ($_POST['name']));
      $section = htmlspecialchars (trim ($_POST['section']));

      if ($name == '' || $section == '') {
        return;
      }

      db_update ('settings', array ('name' => "\"$name\"",
                                    'section' => "\"$section\""), '`id`='.$id);
    }
  
    function manage_setting_id_by_ident ($ident) {
      $id = db_field_value ('settings', 'id', "`ident`=\"$ident\"");

      if ($id != '') {
        return $id;
      }

      return -1;
    }
  
    function manage_settings_delete_by_ident ($ident) {
      manage_settings_delete (manage_setting_id_by_ident ($ident));
    }

    function manage_settings_delete ($id) {
      if ($id == '') {
        return;
      }

      if (!manage_setting_used_by_id ($id)) {
        db_query ('DELETE FROM `settings` WHERE `id`='.$id);
      }
    }

    function manage_settings_class_register ($className, $pseudonym) {
      global $manage_config_classes;
      $manage_config_classes[] = array ('class' => $className,
                                        'pseudonym' => $pseudonym);
    }

    function manage_settings_class_get_registered () {
      global $manage_config_classes;

      return $manage_config_classes;
    }

    function manage_setting_create_class ($class, $ident,$settings) {
      $t = new $class;
      $t->Init ($ident);
      $t->UnserializeSettings ($settings);
      return $t;
    }

    function manage_settings_get_config_form ($class, $ident,$settings) {
      $t = manage_setting_create_class ($class, $ident,$settings);
      return $t->ConfigFormSource ();
    }

    function manage_settings_get_check_script ($class, $ident,$settings) {
      $t = manage_setting_create_class ($class, $ident,$settings);
      return $t->CheckScript ();
    }

    function manage_settings_update_from_post () {
      global $section;

      $q = db_query ('SELECT `id`, `ident`, `class` FROM `settings` '.
                     'WHERE `section`="'.$section.'"');

      while ($r = db_row ($q)) {
        $t = new $r['class'];
        $t->Init ($r['ident']);
        $t->ReceiveSettings ();
        db_update ('settings', array ('settings' => '"'.
                                      addslashes ($t->SerializeSettings ()).'"'),
                   '`id`='.$r['id']);
      }
    }
  
    function manage_setting_print ($class, $id) {
      $t = new $class ();
      $t->Init ($id);
      $t->Draw ();
    }

    function manage_setting_get_received ($id) { return $_POST['setting_'.$id]; }

    function manage_setting_check_script ($class, $id) {
      $t = new $class ();
      $t->Init ($id);
      return $t->CheckScript ();
    }
  
    function opt_get ($id) {
      $r = db_row_value ('settings', '`ident`="'.addslashes ($id).'"');

      if ($r['class'] == '') {
        return '';
      }

      $c = new $r['class'];
      $c->UnserializeSettings ($r['settings']);
      return $c->GetValue ();
    }

    function opt_set ($ident, $v) {
      $q = db_query ('SELECT `id`, `ident`, `class` FROM `settings` '.
                     'WHERE `ident`="'.$ident.'"');

      if (db_affected ()<=0) {
        return false;
      }

      $r = db_row ($q);
      $t = new $r['class'];
      $t->Init ($r['ident']);
      $t->SetValue ($v);
      db_update ('settings', array ('settings' => '"'.
                                    addslashes ($t->SerializeSettings ()).'"'),
                 '`id`='.$r['id']);
      return true;
    }

    function manage_setting_used_by_id ($id) {
      return db_field_value ('settings', 'used', "`id`=$id");
    }

    function manage_setting_used  ($ident) {
      return manage_setting_used_by_id (manage_setting_id_by_ident ($ident));
    }

    function manage_setting_use   ($ident) {
      $id = manage_setting_id_by_ident ($ident);
      db_update ('settings', array ('used' => 1), "`id`=$id");
    }

    function manage_setting_unuse ($ident) {
      $id = manage_setting_id_by_ident ($ident);
      db_update ('settings', array ('used' => 0), "`id`=$id");
    }

    // Registering da classes
    manage_settings_class_register ('CSCText',         'Текст');
    manage_settings_class_register ('CSCNumber',       'Число');
    manage_settings_class_register ('CSCCheckBox',     'Флажок');
    manage_settings_class_register ('CSCSignedNumber', 'Число со знаком');
    manage_settings_class_register ('CSCEmail',        'Адрес электронной почты');
    manage_settings_class_register ('CSCPassword',     'Пароль');
  }
?>
