<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Simple list Wiki page class
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

  if ($_CCList_ != '#CCList_Included#') {
    $_CCList_ = '#CCList_included#';

    class CCList extends CCVirtual {
      var $dataset, $timestamp, $user_id;
      var $scripts = array (
        array ('script' => 'data',    'file' => 'data.php'),
        array ('script' => 'edit',    'file' => 'edit.php'),
        array ('script' => 'history', 'file' => 'history.php')
                            );

      function CCList () { $this->setClassName ('CCList'); }

      function Init ($content_id = -1, $security=nil) {
        CCVirtual::Init ($content_id, $security);
        $this->ReceiveContent ();
      }

      function InitInstance () {
        global $id, $action;

        if (!CCVirtual::InitInstance ()) {
          return false;
        }

        if ($this->settings['dataset']['id'] == '') {
          $this->settings['dataset']['id']=-1;
        }

        $this->dataset = manage_spawn_dataset (
          $this->settings['dataset']['id'],
          $this->settings['dataset']['settings']);

        editor_add_function ('Настройка скриптов',
                             'Editor_ManageScripts', 'default',
                             'action='.$action.'&id='.$id);

        return true;
      }

      function DrawSettingsForm () {
        manage_draw_dataset_selector_for_content ($this->GetClassName ());
      }

      function ReceiveSettings () {
        $this->dataset = manage_receive_dataset_from_selector (
              $this->GetClassName ());

        if ($this->dataset == null) {
          return false;
        }

        if ($this->dataset->GetID () < 0) {
          add_info ('Не указан набор данных');
          return false;
        }

        $this->dataset->Ref ();
        $this->settings['dataset'] = array ('id' => $this->dataset->GetID (),
                                   'settings'=>$this->dataset->GetSettings ());
        $this->settings['content'] = content_create_support_table (
              $this->content_id, $this->dataset->GetID (),
              $this->dataset->GenCreateFields ());
        $this->settings['script'] = -1;

        return true;
      }

      function PerformDeletion  () {
        $this->dataset->FreeContent ();
        $this->dataset->Unref ();
        content_destroy_support_table ($this->content_id,
                                       $this->settings['dataset']['id']);
      }
    
      //////
      //
      function ReceiveContentByQuery ($clause = '', $suff = '') {
        if (isset ($this->settings['content'])) {
          $r = db_row (db_select ($this->settings['content'],
                                  array ('*'), $clause, $suff));
        } else {
          $r = array ();
        }

        $this->timestamp = $r['timestamp'];
        $this->userid = $r['user_id'];
        $this->dataset->SetFieldValues ($r);
      }

      function ReceiveLastContentId () {
        $r = db_field_value ($this->settings['content'], 'id', '',
                           'ORDER BY `timestamp` DESC  LIMIT 1');
        if ($r == '') {
          return -1;
        }

        return $r;
      }

      function ReceiveLastContent () {
        $this->ReceiveContentByQuery ('', 'ORDER BY `timestamp` DESC  LIMIT 1');
      }

      function ReceiveContentWithTime ($timestamp) {
        $this->ReceiveContentByQuery ("`timestamp`=$timestamp");
      }

      function ReceiveContentWithId ($id) {
        if ($id == '') {
          $id=-1;
        }

        $this->ReceiveContentByQuery ("`id`=$id");
      }

      function ReceiveContent () {
        $this->ReceiveLastContent ();
      }

      function CheckExistment () {
        $id = $this->ReceiveLastContentId ();
        $tmp = $this->dataset->BuildCompareQuery ();

        if ($id == '') {
          return false;
        }

        $clause = "`id`=$id".(($tmp!='')?(" AND $tmp"):(''));
        return db_count ($this->settings['content'], $clause) > 0;
      }

      //////
      //
      function Editor_GetHistory () {
        $arr = array ();
        if ($this->settings['content'] != '') {
          $arr = arr_from_query ('SELECT `id`, `timestamp`, `user_id`, '.
                                 '`ip` FROM '.$this->settings['content'].
                                 ' ORDER BY `id`');
        }

        return $arr;
      }

      function Editor_ManageScripts () {
        global $act;
        formo ('title=Управление скрптами отображения раздела;');

        if ($act == 'save') {
          $this->settings['script'] =
            manage_template_receive_from_selector ($this->GetClassName ().
                                                   '_display');
          $this->SaveSettings ();
        }

        settings_formo (content_url_get_full ().'&act=save');
        println ('Скрипт отображения статьи:');
        manage_template_draw_selector_for_script ($this->GetClassName ().
                                                  '_display',
                                                  $this->settings['script']);
        settings_formc ();

        formc ();
      }

      function Editor_EditForm ($formname = '') {
        global $action;

        if ($action == 'save') {
          $this->Editor_Save ($formname);
        }

        $this->dataset->DrawEditorForm ($formname, content_url_get_full ());
      }

      function Save () {
        if (!$this->GetAllowed ('EDIT')) {
          return;
        }

        if ($this->CheckExistment ()) {
          return;
        }

        $arr = $this->dataset->GetFieldValues (true);
        $arr['timestamp'] = time ();
        $arr['user_id'] = "'".user_id ()."'";
        $arr['ip'] = "'".get_real_ip ()."'";

        db_insert ($this->settings['content'], $arr);
      }

      function Editor_Save ($formname = '') {
        if (!$this->GetAllowed ('EDIT')) {
          return;
        }

        $this->dataset->ReceiveData ($formname);
        $this->Save ();
      }

      function Rollback ($commit_id) {
        if (!$this->GetAllowed ('EDIT')) {
          return;
        }

        if ($commit_id == '') {
          return;
        }

        if ($commit_id == $this->ReceiveLastContentId ()) {
          return;
        }

        // TODO: Add reporting stuff here
        if ($commit_id == $this->content_id) {
          return;
        }

        $this->ReceiveContentWithId ($commit_id);
        $this->Save ();

        db_delete ($this->settings['content'], '`id`='.$commit_id);
      }

      function DeleteContentById ($id) {
        if (!$this->GetAllowed ('DELETE')) {
          return;
        }

        if ($this->settings['content'] != '') {
          $this->ReceiveContentWithId ($id);
          $this->dataset->FreeContent ();
          $this->ReceiveContentWithId ($this->content_id);
          db_delete ($this->settings['content'], "`id`=$id");

          if ($this->content['id'] == $id) {
            $this->ReceiveLastContent ();
          }
        }
      }

      function DisplayScript () {
        if ($this->force_displayScript != false) {
          return $this->force_displayScript;
        }

        $c = manage_spawn_template ($this->settings['script']);
        return $c->GetText ();
      }

      function Editor_DrawHistory () {
        global $action, $id;

        $del = $this->GetAllowed ('DELETE');
        $edit = $this->GetAllowed ('EDIT');

        if ($action == 'delete') {
          redirector_add_skipvar ('id');

          if ($del) {
            $this->DeleteContentById ($id);
          }
        } else
          if ($action == 'rollback') {
            redirector_add_skipvar ('action', 'rollback');
            redirector_add_skipvar ('id');

            if ($edit) {
              $this->Rollback ($id);
            }
          }

        $q = db_select ($this->settings['content'], array ('*'),
                        '', 'ORDER BY `timestamp` DESC');

        if (db_affected () > 0) {
          println ('<ul id="history">');
          $i = 0;
          while ($r = db_row ($q)) {
            $time = format_ltime ($r['timestamp']);
            $time = '<a href=".?oldid='.$r['id'].'">'.$time.'</a>';
            $user = user_generate_info_string ($r['user_id']);
            $actions = '';

            if ($edit) {
              $actions .= '[<a href=".?wiki=history&action=rollback&id='.
                $r['id'].'">Вернуться к этой версии</a>]';
            }

            if ($del) {
              $actions .= stencil_ibtnav('minus_s.gif',
                                         content_url_get_full().
                                         '&action=delete&id='.$r['id'],
                                         'Удалить',
                                         'Удалить эту версию статьи?');
            }

            if ($actions != '') {
              $actions = ' | '.$actions;
            }

            if ($i < 2) {
              println ('  <li><div class="top">'.$time.' | '.$user.' '.
                       $actions.'</div></li>');
            } else {
              println ('  <li><div>'.$time.' | '.$user.' '.
                       $actions.'</div></li>');
            }
            $i++;
          }
          println ('</ul>');
        }  else
          println ('<span class="contentSub2">'.
                   '<i>Журнал изменений пуст</i></span>');
      }

      function Field ($field) {
        return $this->dataset->FieldValue ($field);
      }

      function Editor_DrawContent  ($vars = array ()) {
        global $pIFACE, $oldid;
        $pIFACE = $this;

        if ($oldid != '') {
          $this->ReceiveContentWithId ($oldid);
        }

        tpl_srcp ($this->DisplayScript (), $vars);
      }

      function GetTimestamp () { return $this->timestamp; }
      function GeUserId     () { return $this->userid; }
    }
  
    content_Register_CClass ('CCList', 'Лист');
  }
?>
