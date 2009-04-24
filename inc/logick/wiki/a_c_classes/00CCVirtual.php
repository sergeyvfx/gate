<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Virtual Wiki page class
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

  if ($_CCVirtual_ != '#CCVirtual_Included#') {
    $_CCVirtual_ = '#CCVirtual_included#';

    class CCVirtual extends CVirtual {
      var $instanceInitialized=false;
      var $scripts = array ();
      var $content_id, $security, $name;
      var $force_displayScript = false;

      function CCVirtual () { $this->SetClassName ('CCVirtual'); }

      function Init ($content_id = -1, $security = nil)  {
        if ($content_id >= 0) {
          $r = db_row_value ('content', "`id`=$content_id");
          $this->SetName ($r['name']);
        }
        $this->content_id = $content_id;
        $this->security = $security;
        $this->InitInstance ($content_id);
      }

      function InitInstance () {
        if ($this->instanceInitialized) {
          return false;
        }
        $this->instanceInitialized = true;
        return true;
      }

      function DrawSettingsForm ($formname = '') {
        println ('<span class="shade">Настройки данного '.
                 'класса отсутствуют.</span>');
      }
      function ReceiveSettings  ($formname = '') { return true; }
      function PerformDeletion  () { }

      function Editor_CreateDirContent ($d) {
        $tplDir = $this->TemplatesDir ();
        $index = get_file ($tplDir.'/index');
        $up = content_get_up_to_root ($d);
        $index = preg_replace ('/\$\{up_to_root\}/', $up, $index);
        create_file ($d.'/index.php', $index);
        for ($i = 0; $i < count ($this->scripts); $i++) {
          $s = $this->scripts[$i];
          $mk = dirname ($s['file']);
          if ($mk != '' && $mk != '.' && !file_exists ($d.'/'.$mk)) {
            mkdir ($d.'/'.$mk);
            chmod ($d.'/'.$mk, 0775);
          }

          $src = get_file ($tplDir.'/'.$s['script']);
          if (preg_match ('/index\.php$/', $s['file'])) {
            $src = preg_replace ('/\$\{up_to_root\}/', $up, $src);
          }

          create_file ($d.'/'.$s['file'], $src);
        }
      }
    
      function Editor_DeleteDirContent ($d) {
        unlink ($d.'/index.php');

        for ($i = 0; $i < count ($this->scripts); $i++) {
          $s = $this->scripts[$i];
          unlink ($d.'/'.$s['file']);
        }
      }
    
      function Editor_MoveDirContent ($oldPath, $path) {
        content_recursive_move ($oldPath, $path);
      }

      function Editor_EditForm ($formname = '') {  }
      function Editor_Save     ($fprmname = '') {  }
      function Editor_DeleteContentById ($id) {  }

      function Editor_DrawContent  ($vars = array ()) {
        global $pIFACE;
        $pIFACE = $this;
        tpl_srcp ($this->DisplayScript (), $vars);
      }

      function Editor_DrawHistory () {  }

      function DisplayScript () { return ''; }
      function OverwriteDisplayScript ($val) {
        $this->force_displayScript=$val;
      }

      function TemplatesDir () {
        return tpl_dir ().'/back/cclasses/'.$this->GetCLassName ();
      }

      function GetAllowed ($act) {
        if (!$this->security) {
          return false;
        }

        return $this->security->GetAllowed ($act);
      }

      function SaveSettings () {
        $settings = unserialize (db_field_value ('content', 'settings',
                                                 '`id`='.$this->content_id));
        $settings['data'] = $this->GetSettings ();
        $s = '"'.addslashes (serialize ($settings)).'"';
        db_update ('content', array ('settings' => $s),
                   '`id`='.$this->content_id);
      }
    
      function SetName ($v) { $this->name = $v; }
      function GetName ()   { return $this->name; }

      function GetRSSData ($limit) { return array (); }
    }

  }
?>
