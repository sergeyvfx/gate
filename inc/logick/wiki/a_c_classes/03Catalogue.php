<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Structured catalog Wiki page class
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

  if ($_CCCatalogue_Included_ != '##CCCatalogue_Included##') {
    $_CCCatalogue_Included_ = '##CCCatalogue_Included##';

    class CCCatalogue extends CCVirtual {
      var $scripts = array (
          array ('script' => 'data', 'file' => 'data.php'),
          array ('script' => 'edit', 'file' => 'edit.php')
        );

      var $cache;

      function CCCatalogue () { $this->SetClassName ('CCCatalogue'); }  

      function Init ($content_id = -1, $security = nil) {
        global $action, $id;
        CCVirtual::Init ($content_id, $security);

        editor_add_function ('Структура каталога', 'Editor_Structure',
                             'default', "action=$action&id=$id");
        editor_add_function ('Настройка скриптов', 'Editor_Scripts',
                             'default', "action=$action&id=$id");

        $this->cache = array ();

        content_url_var_push_global ('ids');
      }

      ////////
      // FRONT

      function GetCatalogueData ($depth, $pid) {
        $table  = $this->GetSupportTableByDepth ($depth);
        $cat_id = $this->GetCatIDByDepth ($depth);
        $clause = ($pid > 0) ? ("`pid`=$pid") : ('');

        $tmp = arr_from_ret_query (db_select ($table, array ('*'),
                                              $clause,
                                     'ORDER BY `order` ASC, `timestamp` DESC'));

        $uids_usage = array ();

        $arr = array ();
        $n = count ($tmp);

        for ($i = 0; $i < $n; $i++) {
          $it = $tmp[$i];

          if (isset ($uids_usage[$it['uid']])) {
            continue;
          }

          $uids_usage[$it['uid']] = 1;

          $it = $this->ParseDataRow ($cat_id, $it);

          $arr[] = $it;
        }

        return $arr;
      }
    
      function GetCatalogueItem ($depth, $uid, $id = -1, $preparse = true) {
        $table  = $this->GetSupportTableByDepth ($depth);
        $cat_id = $this->GetCatIDByDepth ($depth);

        if ($id < 0) {
          $arr = db_row_value ($table, "`uid`=$uid", 'ORDER BY `id` DESC');
        } else {
          $arr = db_row_value ($table, "`id`=$id");
        }

        if ($preparse) {
          $arr = $this->ParseDataRow ($cat_id, $arr);
        }

        return $arr;
      }

      function DisplayScript () {
        if ($this->force_displayScript != false) {
          return $this->force_displayScript;
        }

        $c = manage_spawn_template ($this->settings['script']);
        return $c->GetText ();
      }

      ////////
      //

      function ReceiveSettings ($formname = '') {
        $this->settings['content'] =
          content_create_support_table ($this->content_id, -1,
                                array ('name' => 'TEXT', 'dataset' => 'INT'));
        $this->settings['script'] = -1;

        return true;
      }

      function PerformDeletion () {
        $this->DeleteCatRecursive (-1);
        content_destroy_support_table ($this->content_id, -1);
      }

      ////////
      //
    
      function CatNameById ($id) {
        return db_field_value ($this->settings['content'], 'name', "`id`=$id");
      }

      function GetNextUID ($id, $pid) {
        $table = content_support_table_name ($this->content_id,
                                             $this->GetCatDatasetID ($id), $id);
        return db_max ($table, 'uid') + 1;
      }

      function GetNextOrder ($id, $pid) {
        $table = content_support_table_name ($this->content_id,
                                             $this->GetCatDatasetID ($id), $id);
        return db_max ($table, 'order', "`pid`=$pid") + 1;
      }

      function MaxDepth () {
        if (isset ($this->cache['MaxDepth'])) {
          return $this->cache['MaxDepth'];
        }

        $this->cache['MaxDepth'] = db_count ($this->settings['content']);

        return $this->cache['MaxDepth'];
      }

      function GetNextCatID ($id) {
        if (isset ($this->cache['NextCatID'][$id])) {
          return $this->cache['NextCatID'][$id];
        }

        $r = db_min ($this->settings['content'], 'id',  "`id`>$id");

        if ($r <= 0) {
          $r = -1;
        }

        $this->cache['NextCatID'][$id]=$r;
        return $r;
      }

      function GetCatItemField ($cat_id, $uid, $field) {
        $table = $this->GetSupportTableByCatID ($cat_id);
        return db_field_value ($table, $field, "`uid`=$uid");
      }

      function GetCatItemParent ($cat_id, $uid) {
        $r = $this->GetCatItemField ($cat_id, $uid, 'pid');

        if ($r == '' || $r <= 0) {
          return -1;
        }

        return $r;
      }

      function GetCatItemOrder ($cat_id, $uid) {
        return $this->GetCatItemField ($cat_id, $uid, 'order');
      }

      function GetCatDatasetID ($id) {
        $dataset_id = -1;

        if (isset ($this->cache['Catalogue.DatasetID'][$id])) {
          return $this->cache['Catalogue.DatasetID'][$id];
        } else {
          $dataset_id = $this->cache['Catalogue.DatasetID'][$id] =
            db_field_value ($this->settings['content'], 'dataset', "`id`=$id");
        }

        return $dataset_id;
      }

      function GetCatDataset ($cat_id) {
        if (isset ($this->cache['Catalogue.Dataset'][$cat_id])) {
          return $this->cache['Catalogue.Dataset'][$cat_id];
        }

        $dataset = manage_spawn_dataset ($this->GetCatDatasetId ($cat_id));
        $dataset->SetSettings ($this->settings['cat_'.$cat_id.'_dsset']);
        $this->cache['Catalogue.Dataset'][$cat_id] = $dataset;

        return $dataset;
      }

      function GetCatTitleField ($id) {
        $dataset = $this->GetCatDataset ($id);
        $f = $dataset->FieldN (0);

        return $f->field;
      }

      function GetCatIDByDepth ($depth) {
        if ($this->cache['Catalogue.Depth'][$depth]) {
          return $this->cache['Catalogue.Depth'][$depth];
        }

        $id = db_field_value ($this->settings['content'], 'id', '',
                              'ORDER BY `id` LIMIT '.$depth.',1');

        $this->cache['Catalogue.Depth'][$depth] = $id;

        return $id;
      }

      function GetSupportTableByDepth ($depth) {
        if ($this->cache['SupportTable.Depth'][$depth]) {
          return $this->cache['SupportTable.Depth'][$depth];
        }

        $id = $this->GetCatIDByDepth ($depth);
        $table = content_support_table_name ($this->content_id,
                                             $this->GetCatDatasetID ($id), $id);
        $this->cache['SupportTable.Depth'][$depth] = $table;

        return $table;
      }

      function GetSupportTableByCatID ($id) {
        if ($this->cache['SupportTable.ID'][$id]) {
          return $this->cache['SupportTable.ID'][$id];
        }

        $table = content_support_table_name ($this->content_id,
                                             $this->GetCatDatasetID ($id), $id);
        $this->cache['SupportTable.ID'][$id] = $table;

        return $table;
      }

      function GetCatDepthByID ($cat_id) {
        if (isset ($this->cache['CatDepthByID'][$cat_id])) {
          return $this->cache['CatDepthByID'][$cat_id];
        }

        $q = db_select ($this->settings['content'], array ('*'), '',
                        'ORDER BY `id`');
        $depth = 0;
        $res = -1;
        while ($r = db_row ($q)) {
          if ($r['id'] == $cat_id) {
            $res = $depth;
            break;
          }
          $depth++;
        }
        $this->cache['CatDepthByID'][$cat_id] = $res;
        return $res;
      }

      function GetLastCatItemID ($cat_id, $uid) {
        $table = $this->GetSupportTableByCatID ($cat_id);
        return db_field_value ($table, 'id', "`uid`=$uid",
                               'ORDER BY `timestamp` DESC LIMIT 1');
      }

      ////////
      //

      function ParseDataRow ($cat_id, $data) {
        $dataset = $this->GetCatDataset ($cat_id);
        $dataset->SetFieldValues ($data);
        $tmp=$dataset->GetFieldValues (false, true);
        $arr = $data;

        foreach ($tmp as $k => $v) {
          $arr[$k] = $v;
        }

        return $arr;
      }
    
      function ParseDataArr ($cat_id, $arr) {
        $n = count ($arr);

        for ($i = 0; $i < $n; $i++) {
          $arr[$i] = $this->ParseDataRow ($cat_id, $arr[$i]);
        }

        return $arr;
      }

      function AppendCatFromDataset ($name, $dataset) {
        if ($dataset == nil) {
          return false;
        }

        if (trim ($name) == '') {
          add_info ('Название подкаталога не может быть пустым.');
          return false;
        }

        $fields = array ('pid'=>'INT', 'uid'=>'INT', 'order'=>'INT');
        $arr = $dataset->GenCreateFields ();
        foreach ($arr as $k => $v) {
          $fields[$k] = $v;
        }

        db_insert ($this->settings['content'],
                   array ('name' => db_html_string ($name),
                          'dataset' => $dataset->GetID (),
                          'timestamp' => time (),
                          'user_id' => user_id (),
                          'ip' => db_html_string (get_real_ip ())));
        $cat_id = db_last_insert ();

        $this->settings['cat_'.$cat_id.'_dsset'] = $dataset->settings;

        content_create_support_table ($this->content_id,
                                      $dataset->GetID (), $fields, $cat_id);
        $dataset->Ref ();

        $this->SaveSettings ();
      
        return true;
      }

      function DeleteCatRecursive_Iterator ($content_id, $dataset, $uid) {
        $q = db_select (content_support_table_name ($content_id,
                                                    $dataset->GetID (), $uid));
        while ($r = db_row ($q)) {
          $dataset->SetFieldValues ($r);
          $dataset->FreeContent ();
        }
      }

      function DeleteCatRecursive ($id) {
        $q = db_select ($this->settings['content'],
                        array ('id', 'dataset'), "`id`>=$id");

        while ($r = db_row ($q)) {
          unset ($this->settings['cat_'.$r['id'].'_dsset']);
          $dataset = manage_spawn_dataset ($r['dataset']);
          $this->DeleteCatRecursive_Iterator ($this->content_id,
                                              $dataset, $r['id']);
          $dataset->UnRef ();

          content_destroy_support_table ($this->content_id,
                                         $r['dataset'], $r['id']);

          db_delete ($this->settings['content'], '`id`='.$r['id']);
        }

        $this->SaveSettings ();
      }

      function AppendReceivedCat () {
        $dataset = manage_receive_dataset_from_selector ();

        if ($dataset == nil) {
          return false;
        }

        $name = stripslashes ($_POST['name']);

        if ($this->AppendCatFromDataset ($name, $dataset)) {
          $_POST = array ();
          return true;
        }

        return false;
      }

      function UpdateCat ($id, $name) {
        if (trim ($name) == '') {
          add_info ('Название подкаталога не может быть пустым.');
          return false;
        }

        db_update ($this->settings['content'],
                   array ('name' => db_html_string ($name)), "`id`=$id");

        return true;
      }

      function UpdateRecievedCat ($id) {
        $name = stripslashes ($_POST['edname']);

        if ($this->UpdateCat ($id, $name)) {
          $_POST = array ();
          return true;
        }

        return false;
      }

      function BuildDBArrayFromDataset ($dataset, $pid, $uid, $order) {
        $arr = $dataset->GetFieldValues (true);

        $arr['timestamp'] = time ();
        $arr['user_id']   = user_id ();
        $arr['ip']        = db_string (get_real_ip ());
        $arr['pid']       = $pid;
        $arr['uid']       = $uid;
        $arr['order']     = $order;

        return $arr;
      }

      function AppendDataToCatFromDataset ($cat_id, $pid, $dataset) {
        if (!$this->GetAllowed ('ADDINFO')) {
          return;
        }

        $arr = $this->BuildDBArrayFromDataset ($dataset, $pid,
                                             $this->GetNextUID ($cat_id, $pid),
                                             $this->GetNextUID ($cat_id, $pid));
        $table = $this->GetSupportTableByCatID ($cat_id);
        db_insert ($table, $arr);
      }

      function CheckCatItemExistment ($cat_id, $uid, $dataset) {
        $query = $dataset->BuildCompareQuery ();
        $id = $this->GetLastCatItemID ($cat_id, $uid);
        $table = $this->GetSupportTableByCatID ($cat_id);

        return db_count ($table, "`id`=$id".((trim ($query)!='')?
                                             (" AND $query"):(''))) > 0;
      }

      function UpdateCatItemFromDataset ($cat_id, $uid, $dataset) {
        if (!$this->GetAllowed ('EDITINFO')) {
          return;
        }

        if ($this->CheckCatItemExistment ($cat_id, $uid, $dataset)) {
          return;
        }

        $pid   = $this->GetCatItemParent ($cat_id, $uid);
        $order = $this->GetCatItemOrder ($cat_id, $uid);
        $arr   = $this->BuildDBArrayFromDataset ($dataset, $pid, $uid, $order);
        $table = $this->GetSupportTableByCatID ($cat_id);

        db_insert ($table, $arr);
      }
    
      function RollbackToCatItemID ($cat_id, $uid, $iid) {
        if (!$this->GetAllowed ('EDITINFO')) {
          return;
        }

        $dataset = $this->GetCatDataset ($cat_id);
        $depth   = $this->GetCatDepthByID ($cat_id);
        $data    = $this->GetCatalogueItem ($depth, $uid, $iid, false);

        $dataset = $this->GetCatDataset ($cat_id);
        $dataset->SetFieldValues ($data);

        if ($this->CheckCatItemExistment ($cat_id, $uid, $dataset)) {
          return;
        }

        $this->UpdateCatItemFromDataset ($cat_id, $uid, $dataset);
      }

      function DeleteFromCat_Iterator ($cat_id, $pid) {
        if ($cat_id < 0) {
          return;
        }

        $next_id = $this->GetNextCatID ($cat_id);
        $table = $this->GetSupportTableByCatID ($cat_id);
        $dataset = $this->GetCatDataset ($cat_id);

        $q = db_select ($table, array ('*'), "`pid`=$pid");

        while ($r = db_row ($q)) {
          $dataset->SetFieldValues ($r);
          $dataset->FreeContent ();
          $this->DeleteFromCat_Iterator ($next_id, $r['uid']);
        }

        db_delete ($table, "`pid`=$pid");
      }

      function DeleteFromCat ($cat_id, $uid) {
        if (!$this->GetAllowed ('DELETEINFO')) {
          return;
        }

        $dataset = $this->GetCatDataset ($cat_id);
        $table   = $this->GetSupportTableByCatID ($cat_id);

        $q = db_select ($table, array ('*'), "`uid`=$uid");

        while ($r = db_row ($q)) {
          $dataset->SetFieldValues ($r);
          $dataset->FreeContent ();
        }

        db_delete ($table, "`uid`=$uid");
        $this->DeleteFromCat_Iterator ($this->GetNextCatID ($cat_id), $uid);
      }

      function FreeContentData ($cat_id, $data) {
        $dataset = $this->GetCatDataset ($cat_id);
        $dataset->SetFieldValues ($data);
        $dataset->FreeContent ();
      }
    
      function DeleteCatItem ($cat_id, $id) {
        if (!$this->GetAllowed ('DELETEINFO')) {
          return;
        }

        $depth = $this->GetCatDepthByID ($cat_id);
        $data  = $this->GetCatalogueItem ($depth, -1, $id, false);
      
        $this->FreeContentData ($cat_id, $data);

        $table = $this->GetSupportTableByCatID ($cat_id);

        db_delete ($table, "`id`=$id");
      }

      function MoveItemUp ($cat_id, $uid) {
        if (!$this->GetAllowed ('EDIT')) {
          return;
        }

        $pid = $this->GetCatItemParent ($cat_id, $uid);
        db_move_up ($this->GetSupportTableByCatID ($cat_id),
                    $uid, "`pid`=$pid", 'uid');
      }

      function MoveItemDown ($cat_id, $uid) {
        if (!$this->GetAllowed ('EDIT')) {
          return;
        }

        $pid = $this->GetCatItemParent ($cat_id, $uid);
        db_move_down ($this->GetSupportTableByCatID ($cat_id),
                      $uid, "`pid`=$pid", 'uid');
      }

      function GetCatItemHistory ($cat_id, $uid, $preparse = true) {
        $table = $this->GetSupportTableByCatID ($cat_id);
        $arr = arr_from_ret_query (db_select ($table, array ('*'),
                                              "`uid`=$uid",
                                              'ORDER BY `timestamp` DESC'));

        if ($preparse) {
          $arr = $this->ParseDataArr ($cat_id, $arr);
        }

        return $arr;
      }

      /////////
      // Some useful outputting

      function DrawCatItemHistory ($cat_id, $uid) {
        $data = $this->GetCatItemHistory ($cat_id, $uid);
        $n = count ($data);
        println ('<ul id="history">');
        $delinfo  = $this->GetAllowed ('DELETEINFO');
        $editinfo = $this->GetAllowed ('EDITINFO');
        $full = content_url_get_full ();

        for ($i = 0; $i < $n; $i++) {
          $r = $data[$i];
          $actions = '';
          $time = '<a href="'.$full.'&iid='.$r['id'].'">'.
            format_ltime ($r['timestamp']).'</a>';
          $user = user_generate_info_string ($r['user_id']);

          if ($editinfo) {
            $actions .= '[<a href="'.$full.'&action=rollback&iid='.
              $r['id'].'">Вернуться к этой версии</a>]';
          }

          if ($delinfo) {
            $actions .= stencil_ibtnav ('minus_s.gif', $full.
                                        '&action=delete&iid='.$r['id'],
                                        'Удалить', 'Удалить этот эдемент?');
          }

          println ('<li><div'.(($i < 2)?(' class="top"'):('')).'>'.
                   "$time | $user".(($actions!='')?(" | $actions"):('')).
                   '</div></li>');
        }

        println ('</ul>');
      }

      ////////
      //

      function Editor_ActionHandler () {
        global $act, $pid;

        if ($act == 'create') {
          $this->AppendReceivedCat ();
        } else if ($act == 'delete') {
          $this->DeleteCatRecursive ($pid);
        } else if ($act == 'save') {
          if (!$this->UpdateRecievedCat ($pid)) {
            $act = 'edit';
          }
        } else if ($act == 'edit') {
          $this->Editor_EditCat ($pid);
        }
      }

      function Editor_DrawCurrentStructure () {
        $q = db_select ($this->settings['content'], array ('*'), '',
                        'ORDER BY `id`');

        if (db_affected () <= 0) {
          return;
        }

        formo ('title=Текущая структура каталога');
        $interior = 0;
        $full = content_url_get_full ();

        while ($r = db_row ($q)) {
          $actions = stencil_ibtnav ('edit.gif', $full.
                                     '&act=edit&pid='.$r['id']);
          $actions .= stencil_ibtnav ('cross.gif', $full.
                                    '&act=delete&pid='.$r['id'], 'Удалить',
                                    'Удалить этот подкаталог и все вложенные?');

          println ('<table class="list" width="100%" style="margin: 2px 0 2px '.
                   ($interion*24).'px;"><tr class="h"><th class="first">'.
                   $r['name'].'</th><th width="80" style="text-align: right;" '.
                   'class="last">'.$actions.'</th></tr></table>');

          $interion++;
        }

        formc ();
      }

      function Editor_CheckScript () {
        println ('<script language="JavaScript" type="text/javascript">'.
                 'function check (frm, id) { '.
                 'if (qtrim (getElementById (id).value)=="") { '.
                 'alert ("Название подкаталога не может быть пустым."); '.
                 'return false; } frm.submit (); } </script>');
      }

      function Editor_EditCat ($id) {
        $full=content_url_get_full ();
        formo ('title=Редактирование каталога;');
        settings_formo ($full.'&act=save&pid='.$id, 'POST',
                        'onsubmit="check (this, \'edname\'); return false;"');
        $value = $this->CatNameById ($id);

        if ($_POST['edname'] != '') {
          $value = htmlspecialchars (stripslashes ($_POST['edname']));
        }

        println ('Название:<input type="text" class="txt block" '.
                 'name="edname" id="edname" value="'.$value.'">');
        settings_formc ($full);
        formc ();
      }

      function Editor_Structure () {
        $this->Editor_ActionHandler ();
        $this->Editor_DrawCurrentStructure ();
        $this->Editor_CheckScript ();

        dd_formo ('title=Создать новый подкаталог;');
        settings_formo (content_url_get_full ().'&act=create', 'POST',
                        'onsubmit="check (this, \'name\'); return false;"');
        println ('Название:<input type="text" class="txt block" '.
                 'name="name" id="name"><div id="hr"></div>');
        manage_draw_dataset_selector_for_content ();
        settings_formc ('');
        dd_formc ();
      }

      ////////
      // Content editor iface

      function Editor_EditForm_ActionHandler ($formname='') {
        global $action, $id, $iid;
        $cid = $this->Editor_EditForm_CurCatId ();

        if ($action == 'save') {
          if ($iid == '') {
            $pid     = $this->Editor_EditForm_CurCatPID ();
            $dataset = $this->GetCatDataset ($cid);
            $dataset->ReceiveData ($formname);
            $this->AppendDataToCatFromDataset ($cid, $pid, $dataset);
          } else {
            $dataset = $this->GetCatDataset ($cid);
            $dataset->ReceiveData ($formname);
            $this->UpdateCatItemFromDataset ($cid, $id, $dataset);
          }
        } else if ($action=='delete') {
          if ($iid == '') {
            $this->DeleteFromCat ($cid, $id);
          } else {
            $this->DeleteCatItem ($cid, $iid);
          }
        } else if ($action == 'up') {
          $this->MoveItemUp ($cid, $id);
        } else if ($action == 'down') {
          $this->MoveItemDown  ($cid, $id);
        } else if ($action == 'rollback') {
          $this->RollbackToCatItemID ($cid, $id, $iid);
        }
      }

      function Editor_EditForm_CurCatId () {
        $depth = $this->Editor_EditForm_CurCatDepth ();

        return $this->GetCatIDByDepth ($depth);
      }

      function Editor_EditForm_CurCatPID () {
        global $ids;
        $depth = $this->Editor_EditForm_CurCatDepth ();
        $arr = explode (',', $ids);

        if (isset ($arr[$depth-1])) {
          return $arr[$depth-1];
        }

        return -1;
      }

      function Editor_EditForm_CurCatDepth () {
        global $ids;

        if ($ids == '') {
          return 0;
        }

        $arr = explode (',', $ids);

        return count ($arr);
      }

      function Editor_EditForm_DrawNavigator ($formname='') {
        global $ids, $action, $id, $iid;
        $depth = $this->Editor_EditForm_CurCatDepth ();
        $arr = explode (',', $ids);
        $newIDS = '';
        //$id=$this->Editor_EditForm_CurCatId ();

        print ('<div id="snavigator">');

        for ($i = 0; $i <= $depth; $i++) {
          $cat_id = $this->GetCatIDByDepth ($i);
          $title = $this->CatNameById ($cat_id);
          $title_id = $this->GetCatTitleField ($cat_id);

          if ($i < $depth || $action == 'edit') {
            $uid = ($action == 'edit' && $i == $depth) ? ($id) : ($arr[$i]);
            $tmp = $this->GetCatalogueItem ($i, $uid);
            print (htmlspecialchars ($tmp[$title_id]).'&nbsp;');
            $title = '('.$title.')';
          }

          if ($i < $depth || $action == 'edit') {
            $title = '<a href=".?wiki=edit'.(($newIDS!='')?('&ids='.
                         $newIDS):('')).'">'.$title.'</a>';
          }

          $newIDS .= (($mewIDS!='')?(','):('')).$ids[$i];

          print $title;
        }

        if ($action == 'edit') {
          if ($iid!='') {
            print ('<a href=".?wiki=edit'.
                   (($newIDS!='') ? ('&ids='.$newIDS) : ('')).
                   '&action=edit&id='.$id.'">История</a>');
          }

          print ('Редактирование');
        }

        println ('</div>');
      }

      function Editor_EditForm_DrawItems ($formname = '') {
        global $ids;

        $id = $this->Editor_EditForm_CurCatId ();
        $depth = $this->Editor_EditForm_CurCatDepth ();
        $pid = $this->Editor_EditForm_CurCatPID ();
        $items = $this->GetCatalogueData ($depth, $pid);
        $n = count ($items);

        if ($n <= 0) {
          return;
        }

        $title_id = $this->GetCatTitleField ($id);
        $url = '.?wiki=edit';
        $full = content_url_get_full ();
        $max_depth = $this->MaxDepth ();
        $mids = $ids.(($ids!='')?(','):(''));

        $del      = $this->GetAllowed ('DELETEINFO');
        $edit     = $this->GetAllowed ('EDIT');
        $editinfo = $this->GetAllowed ('EDITINFO');

        formo ('title=Элементы в текущем подкаталоге;');
        println ('<table class="list" width="100%">');

        for ($i = 0; $i < $n; $i++) {
          $it = $items [$i];
          $class = ($i == $n - 1) ? ('last') : ('');
          $title = htmlspecialchars ($it[$title_id]);
          $actions = '';

          if ($editinfo) {
            $actions .= stencil_ibtnav ('edit.gif', $full.
                                        '&action=edit&id='.$it['uid']);
          }

          if ($edit) {
            $actions .= stencil_updownbtn ($i, $n, $it['uid'], $full);
          }

          if ($del) {
            $actions .= stencil_ibtnav ('cross.gif', $full.
                                        '&action=delete&id='.$it['uid'],
                                        'Удалить',
                                        'Удалить этот элемент и все его вложения?');
          }

          if ($depth < $max_depth - 1) {
            $title = '<a href="'.$url.'&ids='.$mids.$it['uid'].'">'.
              $title.'</a>';
          }

          println ('<tr'.(($class != '') ? (" class=\"$class\"") : ('')).
                   '><td class="n">'.($i+1).'.</td><td>'.$title.
                   '</td><td width="96" align="right">'.$actions.'</td></tr>');
        }
        println ('</table>');
        formc ();
      }

      function Editor_EditForm_EditItem ($id, $formname = '') {
        global $iid;

        if (!$this->GetAllowed ('EDITINFO')) {
          return;
        }

        content_url_var_push_global ('action');
        content_url_var_push_global ('id');
        content_url_var_push_global ('iid');

        $full   = content_url_get_full ();
        $cat_id = $this->Editor_EditForm_CurCatId ();
        $data   = $this->GetCatItemHistory ($cat_id, $id);

        if ($iid == '') {
          println ('<span class="contentSub2 arr"><a href="'.$full.
                   '&iid='.$data[0]['id'].
                   '">Редактировать</a> последнюю версию</span><br><br>');
          println ('<span class="contentSub2">История элемента:</span>');
          $this->DrawCatItemHistory ($cat_id, $id);
        } else {
          $depth   = $this->Editor_EditForm_CurCatDepth ();
          $data    = $this->GetCatalogueItem ($depth, $id, $iid, false);
          $dataset = $this->GetCatDataset ($cat_id);
          $dataset->SetFieldValues ($data);
          $dataset->DrawEditorForm ($formname, $full);
        }
      }

      function Editor_EditForm_CreateForm ($formname = '') {
        if (!$this->GetAllowed ('ADDINFO')) {
          return;
        }

        $id = $this->Editor_EditForm_CurCatId ();
        $dataset = $this->GetCatDataset ($id);
        $dataset->FreeValues ();

        println ('<script language="JavaScript" type="text/javascript">');
        println ('  var initialized=false;');
        println ('  function Init () {');
        println ('    if (initialized) return;');
        print   ($dataset->BuildInitScript ($formname));
        println ('    intialized=true;');
        println ('  }');
        println ('</script>');
        dd_formo ('title=Добавить элемент в этот подкаталог;onexpand=Init ();');
        $dataset->DrawEditorForm ($formname, content_url_get_full (),
                                  false, 'Добавить');
        dd_formc ();
      }

      function Editor_EditForm ($formname = '') {
        global $action, $id;

        if (!$this->GetAllowed ('EDIT')) {
          return;
        }

        if ($this->MaxDepth ()<=0) {
          println ('<span class="contentSub2">Структура католока пуста</span>');
          return;
        }

        if ($action!='edit') {
          redirector_add_skipvar ('id');
          redirector_add_skipvar ('iid');
        }

        $this->Editor_EditForm_ActionHandler ($formname);
        $this->Editor_EditForm_DrawNavigator ($formname);

        if ($action!='edit') {
          // Draw items at current catalogue
          $this->Editor_EditForm_DrawItems ($formname);
          // Create form
          $this->Editor_EditForm_CreateForm ($formname);
        } else {
          $this->Editor_EditForm_EditItem ($id, $formname);
        }
      }

      ////////
      //

      function Editor_Scripts () {
        global $act;

        if ($act == 'save') {
          $this->settings['script'] =
            manage_template_receive_from_selector ('display');
          $this->SaveSettings ();
        }

        $full = content_url_get_full ();
        formo ('title=Настройка скриптов');
        settings_formo ($full.'&act=save');
        println ('Скрипт отображения статьи:');
        manage_template_draw_selector_for_script ('display',
                                                  $this->settings['script']);
        settings_formc ('');
        formc ();
      }
    }

    content_register_CClass ('CCCatalogue', 'Настраиваемый каталог');
  }
?>
