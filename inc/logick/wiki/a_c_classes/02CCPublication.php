<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Publication Wiki page class
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

  if ($_CCPublication_ != '#CCPublication_Included#') {
    $_CCPublication_='#CCPublication_included#';

    class CCPublication extends CCVirtual {
      var $data, $ids, $uids, $dbids, $dbuids,$idlink;
      var $scripts = array (
          array ('script' => 'data', 'file' => 'data.php',
                 'desc' => 'Скрипт просмотра статьи'),
          array ('script' => 'edit', 'file' => 'edit.php',
                 'desc'=>'Скрипт редактироваия статьи')
        );

      function CCPublication () { $this->SetClassName ('CCPublication'); }

      function SpawnDataset () {
        return manage_spawn_dataset ($this->settings['dataset']['id'],
                                     $this->settings['dataset']['settings']);

        if ($this->empty_dataset == nil) {
          $this->empty_dataset = manage_spawn_dataset (
            $this->settings['dataset']['id'],
            $this->settings['dataset']['settings']);
        }

        return $this->empty_dataset;
      }

      function UpdateScripts () {
        if ($this->settings['detailed']) {
          $this->scripts[] = array ('script' => 'text/data',
                                    'file'   => 'text/data.php');

          $this->scripts[] = array ('script' => 'text/edit',
                                    'file'   => 'text/edit.php');

          $this->scripts[] = array ('script' => 'text/history',
                                    'file'   => 'text/history.php');

          $this->scripts[] = array ('script' => 'text/index',
                                    'file'   => 'text/index.php');
        }
      }

      function Init ($content_id = -1, $security = nil) {
        global $action, $id;
        CCVirtual::Init ($content_id, $security);

        $this->InitInstance ();
        editor_initialize ($this->GetClassName ());

        if ($this->GetAllowed ('EDIT') || $this->GetAllowed ('EDITINFO')) {
          editor_add_function ('Просмотр', 'DrawNavigation',
                               $this->GetClassName ());
          if ($this->GetAllowed ('ADDINFO')) {
            editor_add_function ('Добавление', 'DrawAddPublication',
                                 $this->GetClassName ());
          }
        }

        editor_add_function ('Настройка скриптов', 'Editor_ManageScripts',
                             'default', 'action='.$action.'&id='.$id);

        $this->ReceiveContent ();
      }

      function InitInstance () {
        if (CCVirtual::InitInstance ()) {
          return false;
        }

        if ($this->settings['dataset']['id'] == '') {
          $this->settings['dataset']['id']=-1;
        }

        $this->dataset = $this->SpawnDataSet ();
        $this->UpdateScripts ();

        return true;
      }

      function DrawSettingsForm ($formname = '') {
        println ('<input type="checkbox" name="'.$formname.
                 '_detailed" value="1"> Использовать &laquo;подробные'.
                 '&raquo; публикации<div id="hr"></div>');
        manage_draw_dataset_selector_for_content ($formname);
      }

      function ReceiveSettings ($formname = '') {
        $this->dataset = manage_receive_dataset_from_selector ($formname);

        if ($this->dataset == null) {
          return false;
        }

        if ($this->dataset->GetID () < 0) {
          add_info ('Не указан набор данных');
          return false;
        }

        $this->dataset->Ref ();
        $fields = array ('uid' => 'INT', 'order'=>'INT');
        $arr = $this->dataset->GenCreateFields ();

        foreach ($arr as $k => $v) {
          $fields[$k] = $v;
        }

        $this->settings['dataset'] = array ('id' => $this->dataset->GetID (),
                                 'settings' => $this->dataset->GetSettings ());
        $this->settings['content'] =
          content_create_support_table ($this->content_id,
                                        $this->dataset->GetID (), $fields);

        $this->settings['script'] = -1;
        $this->settings['itemScript'] = -1;
        $this->settings['fullScript'] = -1;

        $this->settings['detailed'] = ($_POST[$formname.'_detailed'])?(1):(0);
        $this->UpdateScripts ();

        $s = 'content_'.$this->content_id.'_count';
        manage_settings_create ('Количество элементов на странице для раздела '.
                                '«'.htmlspecialchars ($this->GetName ()).'»',
                                'Разделы', $s, 'CSCSignedNumber');
        opt_set ($s, '10');

        return true;
      }

      function PerformDeletion  () {
        // Add global deletion here
        $this->dataset->Unref ();

        content_destroy_support_table ($this->content_id,
                                       $this->settings['dataset']['id']);

        manage_settings_delete_by_ident ('content_'.$this->content_id.'_count');
      }

      //////
      function ReceiveContentByQuery ($clause = '') {
        $this->idlink = $this->dbuids = $this->uids = $this->dbids =
          $this->ids = $this->data = array ();

        if ($this->settings['content'] == '') {
          return;
        }

        $q = db_select ($this->settings['content'], array ('*'),
                        $clause, 'ORDER BY '.
                          (($this->IsDated ())?(' `date`,'):('')).
                        ' `order`, `timestamp` ');

        $arr = array ();
        $ids = array ();
        $uids = array ();
        $usage = array ();

        while ($r=db_row ($q)) {
          if (isset ($usage[$r['uid']])) {
            if ($arr[$usage[$r['uid']]]['timestamp'] < $r['timestamp']) {
              $strip[$usage[$r['uid']]] = true;
            } else {
              $strip[count ($arr) + 1] = true;
            }
          }

          $usage[$r['uid']] = count ($arr);
          $arr[] = $r;
        }

        $save = $arr;
        $n = count ($arr);
        $arr = array ();

        for ($i = 0; $i < $n; $i++) {
          if (!isset ($strip[$i])) {
            $arr[] = $save[$i];
          }
        }

        $arr[] = array ('uid'=>'-1');
        $n = count ($arr);
        $luid = -1;

        for ($i = 0; $i < $n; $i++) {
          if ($luid==-1) {
            $luid = $arr[$i]['uid'];
          }

          if ($arr[$i]['uid'] != $luid) {
            $this->ids[] = $arr[$i-1]['uid'];
            $this->dbids[] = $arr[$i-1]['id'];
            $this->dbuids[] = $arr[$i-1]['uid'];
            $this->uids[$arr[$i-1]['uid']] = count ($this->ids)-1;
            $this->data[] = $arr[$i-1];
            $this->idlink[$arr[$i-1]['id']] = count ($this->ids)-1;
            $luid = $arr[$i]['uid'];
          }
        }
      }

      function ReceiveContent () { $this->ReceiveContentByQuery (''); }

      //////
      function Editor_Save ($formname = '', $receive = true) {
        global $redirect;

        if (!$this->GetAllowed ('ADDINFO')) {
          return;
        }

        if ($receive) {
          $this->dataset->ReceiveData ($formname);
        }

        $arr = $this->dataset->GetFieldValues (true);
        $clause = '';
        if ($this->IsDated ()) {
          $clause='`date`="'.$this->dataset->FieldValue ('date').'"';
        }

        $arr['order'] = db_next_order ($this->settings['content'], $clause);
        $arr['timestamp'] = time ();
        $arr['user_id'] = '"'.user_id ().'"';
        $arr['ip'] = '"'.get_real_ip ().'"';
        db_insert ($this->settings['content'], $arr);
        $uid = db_last_insert ();
        db_update ($this->settings['content'], array ('uid'=>$uid),
                   '`id`='.$uid);
        $this->dataset->FreeValues ();
        $this->ReceiveContent ();

        if ($redirect != '') {
          redirect ();
        }
      }

      function CheckExistment ($id) {
        $tmp = $this->dataset->BuildCompareQuery ();

        if ($id == '') {
          return false;
        }

        $clause = "`id`=$id".(($tmp!='')?(" AND $tmp"):(''));
        return db_count ($this->settings['content'], $clause) > 0;
      }

      function Editor_SavePublication ($formname = '') {
        global $id, $uid, $redirect;

        if ($id == '') {
          $id = $this->dbids[$this->uids[$uid]];
        }

        if (!isnumber ($id)) {
          return;
        }

        if (!$this->GetAllowed ('EDITINFO')) {
          return;
        }

        $r = db_row_value ($this->settings['content'], '`id`='.$id);
        $this->dataset->ReceiveData ($formname);
        $cid = db_max ($this->settings['content'], 'id', '`uid`='.$uid);

        if ($this->CheckExistment ($cid)) {
          if ($redirect != '') {
            redirect ();
          }
          return;
        }

        $arr = $this->dataset->GetFieldValues (true);
        $arr['uid'] = $r['uid'];
        $arr['order'] = $r['order'];
        $arr['timestamp'] = time ();
        $arr['user_id'] = '"'.user_id ().'"';
        $arr['ip'] = '"'.get_real_ip ().'"';
        db_insert ($this->settings['content'], $arr);
        $this->dataset->FreeValues ();
        $this->ReceiveContent ();

        if ($redirect != '') {
          redirect ();
        }
      }
    
      function DrawPublistEntry ($formname, $titleField, $data) {
        println ('<table class="list smb">');
        content_url_var_push_global ('function');
        $n = count ($data);
        $url = content_url_get_full ();
        $del = $this->GetAllowed ('DELETE');
        $edit = $this->GetAllowed ('EDIT');

        for ($i = 0; $i < $n; $i++) {
          $this->dataset->SetFieldValues ($data[$i]);
          $t=$this->dataset->Field ($titleField);
          $title = $t->Value ();
          $title = htmlspecialchars ($title);

          if (trim ($title) == '') {
            $title='<i>Нет значения</i>';
          }

          $title = '<a href="'.$url.'&uid='.$data[$i]['uid'].'">'.
            $title.'</a>';

          println ('  <tr'.(($i==$n-1)?(' class="last"'):('')).
                   '><td class="n">'.($i+1).'.</td><td>'.$title.'</td>');
          $actions = '';

          if ($edit) {
            $actions = stencil_updownbtn ($i, $n, $data[$i]['uid'], $url);
          }

          if ($del) {
            $actions .= stencil_ibtnav ('cross.gif',
                                        content_url_get_full ().
                                        '&action=delete&pid='.$data[$i]['uid'],
                                        'Удалить', 'Удалить эту публикацию?');
          }

          if ($actions == '') {
            $actions = '&nbsp;';
          }

          println ('<td align="right">'.$actions.'</td>');
          println ('</tr>');
        }
        $this->dataset->FreeValues ();
        println ('</table>');
      }

      ////////
      function IsDated () {
        $fields=$this->dataset->Fields ();
        $f = $fields[0]->GetDataClass ();
        return ($f == 'CDCDate');
      }

      function GetCurDate () {
        global $year, $month, $day;
        return "$year-$month-$day";
      }
    
      function GetYears () {
        $res = array ();
        $usage = array ();
        $n = count ($this->data);

        for ($i = 0; $i < $n; $i++) {
          $y = preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si', '\1',
                             $this->data[$i]['date']);
          if (!$usage[$y]) {
            $res[] = array ('title' => $y, 'val' => $y);
            $usage[$y] = true;
          }
        }
        // array_multisort ($res, SORT_ASC, SORT_STRING);
        return $res;
      }

      function GetDays ($year, $month) {
        $res = array ();
        $usage = array ();
        $n = count ($this->data);

        for ($i = 0; $i < $n; $i++) {
          $y = preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si', '\1',
                           $this->data[$i]['date']);
          $m = preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si', '\2',
                           $this->data[$i]['date']);
          $d = preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si', '\3',
                           $this->data[$i]['date']);

          if (!$usage[$d] && $y == $year && $m == $month) {
            $res[] = array ('title' => $d, 'val' => $d);
            $usage[$d] = true;
          }
        }
        // array_multisort ($res, SORT_ASC, SORT_STRING);
        return $res;
      }

      function GetMonths ($year) {
        global $months;
        $res = array ();
        $usage = array ();
        $n = count ($this->data);

        for ($i = 0; $i < $n; $i++) {
          $y = preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si', '\1',
                             $this->data[$i]['date']);
          $m = preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si', '\2',
                             $this->data[$i]['date']);

          if (!$usage[$m] && $y == $year) {
            $res[] = array ('title' => $months[$m], 'val' => $m);
            $usage[$m] = true;
          }
        }
        // array_multisort ($res, SORT_ASC, SORT_STRING);
        return $res;
      }

      function DrawDatedHead ($titleField='') {
        global $year, $month, $day, $months, $uid, $action;
        $sub = '';
        $url = content_url_get_full ();
        $t = htmlspecialchars ($this->data[$this->uids[$uid]][$titleField]);

        if (trim ($t) == '') {
          $t='<i>Пустое поле</i>';
        }

        // navigator
        if ($year != '') {
          $sub='<b>'.$year.'</b> год.';
          println ('<div id="snavigator"><a href="'.$url.'">Выбор года</a>');
          if ($month != '') {
            $sub = '<b>'.$months[$month].'</b>, '.$sub;
            print ('<a href="'.$url.'&year='.$year.'">Выбор месяца</a>');
            if ($day == '') {
              println ('Выберите число</div>');
            } else {
              $sub = '<b>'.$day.'</b>, '.$sub;
              print ('<a href="'.$url.'&year='.$year.'&month='.$month.
                     '">Выбор числа</a>');
              if ($uid == '') {
                println ('Список публикаций</div>');
              } else {
                print ('<a href="'.$url.'&year='.$year.'&month='.$month.
                       '&day='.$day.'">Список публикаций</a>');
                if ($action != 'edit') {
                  println ($t.'</div>');
                } else {
                  println ('<a href="'.$url.'&year='.$year.'&month='.
                           $month.'&day='.$day.'&uid='.$uid.'">'.$t.
                           '</a>Редактирование</div>');
                }
              }
            }
          } else {
            println ('Выберите месяц</div>');
          }
        } else {
          println ('<div id="snavigator">Выберите год</a></div>');
        }

        // Subinfo
        if ($sub != '' && $uid == '') {
          println ('<div class="contentSub">Публикации за '.$sub.'</div>');
        }
      }

      function DrawDatedIterator ($title, $arr, $url_prefix) {
        $n = count ($arr);
        $this->DrawDatedHead ();
        println ('<table class="list smb">');

        for ($i = 0; $i < $n; $i++) {
          println ('<tr'.(($i==$n-1)?(' class="last"'):('')).
                   '><td class="n">'.($i+1).'.</td><td><a href="'.
                   $url_prefix.$arr[$i]['val'].'">'.$arr[$i]['title'].
                   '</a></td></tr>');
        }
        println ('</table>');
      }

      function DrawYears () {
        $arr = $this->GetYears ();
        content_url_var_push_global ('function');
        $url = content_url_get_full ().'&year=';
        $this->DrawDatedIterator ('год', $arr,$url);
      }

      function DrawMonths ($year) {
        $arr = $this->GetMonths ($year);
        content_url_var_push_global ('function');
        $url=content_url_get_full ().'&year='.$year.'&month=';
        $this->DrawDatedIterator ('месяц', $arr,$url);
      }

      function DrawDays ($year, $month) {
        $arr = $this->GetDays ($year,$month);
        content_url_var_push_global ('function');
        $url = content_url_get_full ().'&year='.$year.
          '&month='.$month.'&day=';
        $this->DrawDatedIterator ('день', $arr,$url);
      }

      function GetDataWithDate ($y, $m, $d) {
        $d = "$y-$m-$d";
        $arr = array ();
        $n = count ($this->data);

        for ($i = 0; $i < $n; $i++) {
          if ($this->data[$i]['date'] == $d) {
            $arr[] = $this->data[$i];
          }
        }

        return $arr;
      }

      function DrawDatedPublist ($formname, $titleField) {
        global $year, $month, $day, $uid, $action;

        if ($uid != '' &&
            $this->data[$this->uids[$uid]]['date'] != "$year-$month-$day") {
          $uid = '';
        }

        if ($day != '' &&
            !count ($this->GetDataWithDate ($year, $month, $day))) {
          $day = '';
          $uid = '';
        }

        if ($year == '') {
          $this->DrawYears ();
        } else {
          if ($month == '') {
            $this->DrawMonths ($year);
          } else {
            if ($day == '') {
              $this->DrawDays ($year, $month);
            } else {
              $this->DrawDatedHead ($titleField);
              content_url_var_push_global ('year');
              content_url_var_push_global ('month');
              content_url_var_push_global ('day');

              if ($uid == '') {
                $arr = $this->GetDataWithDate ($year, $month, $day);
                $this->DrawPublistEntry ('', $titleField, $arr);
              } else {
                if ($action!='edit') {
                  println ('<div class="contentSub"><span class="arr">'.
                           '<a href="'.content_url_get_full ().'&uid='.
                           $uid.'&action=edit">Редактировать</a> последнюю '.
                           'версию</span></div>');
                  $this->DrawPublicationHistory ($uid, $formname);
                } else {
                  if ($this->GetAllowed ('EDITINFO')) {
                    $this->DrawPublicationEditor ($uid, $formname, $id);
                  }
                }
              }
            }
          }
        }
      }

      function DrawSimplePublist ($formname, $titleField) {
        global $uid, $action;
        $url = content_url_get_full ();

        // Navbar
        if ($uid != '') {
          $r = $this->data[$this->uids[$uid]];
          $t = htmlspecialchars ($r[$titleField]);
          print ('<div id="snavigator"><a href="'.$url.
                 '">Список публикаций</a>');

          if ($action != 'edit') {
            println ($t.'</div>');
            println ('<div class="contentSub"><a href="'.
                     content_url_get_full ().'&uid='.$uid.
                     '&action=edit">Редактировать</a> последнюю версию</div>');
            $this->DrawPublicationHistory ($uid, $formname);
          } else {
            println ('<a href="'.content_url_get_full ().'&uid='.$uid.'">'.
                     $t.'</a>Редактирование</div>');

            if ($this->GetAllowed ('EDITINFO')) {
              $this->DrawPublicationEditor ($uid, $formname, $id);
            }
          }
        } else {
          println ('<div id="snavigator">Список публикаций</div>');
          $this->DrawPublistEntry ($formname, $titleField, $this->data);
        }
      }

      function DrawPublicationHistory ($uid, $formname = '',$args = '',
                                       $head = true) {
        if ($head) {
          println ('<span class="contentSub">История публикации:</span>');
        }

        $q = db_select ($this->settings['content'],
                        array ('id', 'user_id', 'ip', 'timestamp'),
                        '`uid`='.$uid, 'ORDER BY `timestamp` DESC');
        println ('<ul id="history" style="margin-top: 4px;">');

        $i = 0;
        $del = $this->GetAllowed ('DELETE');
        $delinfo = $this->GetAllowed ('DELETEINFO');
        $editinfo = $this->GetAllowed ('DELETEINFO');
        content_url_var_push_global ('function');
        content_url_var_push_global ('uid');

        $url = content_url_get_full ();
        $one = db_affected () <= 1;

        if ($args == '') {
          $args = 'action=edit&id';
        }

        while ($r = db_row ($q)) {
          $time = '<a href="'.$url.'&'.$args.'='.$r['id'].'">'.
            format_ltime ($r['timestamp']).'</a>';
          $user = user_generate_info_string ($r['user_id']);
          $actions = '';

          if ($editinfo) {
            $actions .= '[<a href="'.content_url_get_full ().
              '&action=rollback&id='.$r['id'].'">Вернуться к этой версии</a>]';
          }

          if ($delinfo && !($one && !$del)) {
            $actions .= stencil_ibtnav ('minus_s.gif', content_url_get_full ().
                                        '&action=delete&id='.$r['id'],
                                        'Удалить',
                                        'Удалить эту версию публикации?');
          }

          if ($actions != '') {
            $actions = ' | '.$actions;
          }

          println ('  <li><div'.(($i<2)?(' class="top"'):('')).'>'.$time.
                   ' | '.$user.$actions.'</div></li>');
          $i++;
        }
        println ('</ul>');
      }

      function DrawPublicationEditor ($uid, $formname = '', $id = '') {
        $tmp = '';

        if ($id == '') {
          $id = $this->dbids[$this->uids[$uid]];
        }

        if (!$this->GetAllowed ('EDITINFO')) {
          return;
        }

        content_url_var_push_global ('redirect');
        content_url_var_push_global ('function');
        content_url_var_push_global ('uid');

        $r = db_row_value ($this->settings['content'], '`id`='.$id);
        $c = $this->SpawnDataset ();
        $c->SetFieldValues ($r);

        if ($tmp!='') {
          println ('<span class="contentSub">Редактирование публикации за <b>'.
                   format_ltime ($r['timestamp']).'</b></span>');
        }

        $c->DrawEditorForm ($formname, content_url_get_full ().'&id='.$id);
      }

      function DrawNavigation ($formname = '') {
        global $uid, $action, $id;

        if (count ($this->data) == 0) {
          println ('<span class="contentSub2">Публикации отсутсвуют</span>');
          return;
        }

        $fields = $this->dataset->Fields ();

        $f = $fields[0]->GetField ();

        if ($f!='date') {
          $this->DrawSimplePublist ($formname, $f);
        } else {
          $this->DrawDatedPublist ($formname, $fields[1]->GetField ());
        }
      }

      function DrawAddPublication ($formname = '') {
        $this->dataset->DrawEditorForm ($formname, content_url_get_full ());
      }

      function DeletePublication ($id, $update = false) {
        if (!$this->GetAllowed ('DELETEINFO')) {
          return;
        }

        $r = db_row (db_select ($this->settings['content'],
                                array ('*'), '`id`='.$id));

        if (db_affected () <= 1 && !$this->GetAllowed ('DELETE')) {
          return;
        }

        $this->dataset->SetFieldValues ($r);
        $this->dataset->FreeContent ();
        db_delete ($this->settings['content'], '`id`='.$id);

        if ($update) {
          $this->ReceiveContent ();
        }
      }

      function DeleteEntirePublication ($uid) {
        if (!$this->GetAllowed ('DELETE')) {
          return;
        }

        $q = db_select ($this->settings['content'], array ('id'),
                        '`uid`='.$uid);

        while ($r = db_row ($q)) {
          $this->DeletePublication ($r['id']);
        }

        $this->ReceiveContent ();
      }
    
      function Rollback ($id) {
        if (!$this->GetAllowed ('EDITINFO')) {
          return;
        }

        $r = db_row (db_select ($this->settings['content'], array ('id')));
        $nid = db_next_field ($this->settings['content'], 'id');

        db_update ($this->settings['content'],
                   array ('id' => $nid,
                          'timestamp' => time (), 
                          'ip' => '"'.get_real_ip ().'"',
                          'user_id' => user_id ()), '`id`='.$id);
      }

      function Move ($dir,$uid) {
        if (!$this->GetAllowed ('EDIT')) {
          return;
        }

        $clause = '';
        if ($this->IsDated ()) {
          $clause = '`date`="'.$this->GetCurDate ().'"';
        }

        if ($dir == 'down') {
          db_move_down ($this->settings['content'], $uid, $clause, 'uid');
        } else if ($dir == 'up') {
          db_move_up ($this->settings['content'], $uid, $clause, 'uid');
        }

        $this->ReceiveContent ();
      }

      function Editor_ExecAction ($formname = '') {
        global $action, $uid, $id, $pid;
        // if (!$this->GetAllowed ('EDIT')) return;

        if ($action == 'save') {
          if ($this->GetAllowed ('ADDINFO') && !isset ($uid)) {
            $this->Editor_Save ($formname);
          } else {
            if ($this->GetAllowed ('EDITINFO') && isset ($uid)) {
              $this->Editor_SavePublication ($formname);
            }
          }
        } else if ($action == 'delete') {
          if ($this->GetAllowed ('DELETE') && isset ($pid)) {
            $this->DeleteEntirePublication ($pid);
          } else {
            if ($this->GetAllowed ('DELETEINFO') && isset ($id)) {
              $this->DeletePublication ($id, true);
            }
          }
        } else if ($action == 'rollback') {
          if ($this->GetAllowed ('EDITINFO') && isset ($id)) {
            $this->Rollback ($id);
          }
        } else if ($action == 'up') {
          if ($this->GetAllowed ('EDIT') && isset ($id)) {
            $this->Move ('up', $id);
          }
        } else if ($action=='down') {
          if ($this->GetAllowed ('EDIT') && isset ($id)) $this->Move ('down', $id);
        }
      }

      function Editor_EditForm ($formname = '') {
        $this->Editor_ExecAction ($formname);
        editor_draw_menu ($this->GetClassName ());
        $f = editor_get_function ($this->GetClassName ());

        if ($f != '') {
          $this->$f ($formname);
        }
      }

      function Editor_ItemEditForm ($formname = '') {
        global $uid;

        if (!isset ($this->uids[$uid])) {
          redirect ('..');
        }

        $this->Editor_ExecAction ($formname);
        $this->DrawPublicationEditor ($uid, $formname);
      }

      function Editor_DrawHistory ($formname='') {
        global $uid, $action, $oldid, $id;
        $this->Editor_ExecAction ($formname);

        if (!isset ($this->uids[$uid])) {
          redirect ('..');
        }

        if ($action == 'view') {
          redirect ('.?uid='.$uid.'&oldid='.$id);
        }

        $this->DrawPublicationHistory ($uid, $formname,
                                       'action=view&id', false);
      }

      function Editor_DrawItem ($uid, $params = array ()) {
        if (!isset ($this->uids[$uid])) {
          redirect ('..');
        }

        global $pIFACE; $pIFACE = $this;
        tpl_srcp ($this->FullScript (), $params);
      }

      // IFACE stuff
      function GetListLength () { return count ($this->data); }

      function GetListRow ($i) {
        $arr = $this->data[$i];
        $this->dataset->SetFieldValues ($arr);
        $r = $this->dataset->GetFieldValues (false, true);
        $this->dataset->FreeValues ();

        foreach ($r as $k => $v) {
          $arr[$k]=$v;
        }

        return $arr;
      }

      function GetListRowByUid ($uid) {
        return $this->GetListRow ($this->uids[$uid]);
      }

      function GetListRowById ($id) {
        $res = db_row_value ($this->settings['content'], '`id`='.$id);
        $this->dataset->SetFieldValues ($res);
        $r = $this->dataset->GetFieldValues (false, true);
        $this->dataset->FreeValues ();

        foreach ($r as $k => $v) {
          $res[$k] = $v;
        }

        return $res;
      }

      function GetList () {
        $arr = array ();
        $n = $this->GetListLength ();

        for ($i = 0; $i < $n; $i++) {
          $arr[] = $this->GetListRow ($i);
        }

        return $arr;
      }

      function GetPagedList ($page = 0) {
        $s = 'content_'.$this->content_id.'_count';
        $perPage = opt_get ($s);

        $list = $this->GetList ();
        $n = count ($list);
        $pageCount = ceil ($n/$perPage);
      
        if ($perPage <= 0) {
          return $list;
        }

        if ($pageCount <= 0) {
          return array ();
        }

        if ($page < 0) {
          $page = 0;
        }

        if ($page >= $pageCount) {
          $page = $pageCount - 1;
        }

        $start = $perPage * $page;
        $end = min ($start + $perPage, $n);

        $res = array ();
        for ($i = $start; $i < $end; $i++) {
          $res[] = $list[$i];
        }

        return $res;
      }

      function PageCount () {
        $s = 'content_'.$this->content_id.'_count';
        $perPage = opt_get ($s);

        if ($perPage <= 0) {
          return '';
        }

        return ceil ($n/$perPage);
      }

      function GetPagintation ($page = 0, $pageid = 'page') {
        $list = $this->GetList ();
        $n = count ($list);

        $s = 'content_'.$this->content_id.'_count';
        $perPage = opt_get ($s);

        if ($perPage <= 0) {
          return '';
        }

        $pageCount = ceil ($n/$perPage);
        if ($pageCount <= 1) {
          return '';
        }

        return stencil_pagintation ($pageCount, $page,
                                    content_url_get_full (), $pageid);
      }

      function GetItemEditorLink ($id) {
        $wiki = content_url_var_pop ('wiki');
        $url = content_url_get_full ();
        $data = $this->data [$this->idlink[$id]];
        $redirect = get_redirection ();

        if ($this->IsDated ()) {
          $y = preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si',
                             '\1', $data['date']);
          $m = preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si',
                             '\2', $data['date']);
          $d = preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si',
                             '\3', $data['date']);

          return $url.'&wiki=edit&action=edit&year='.$y.'&month='.
            $m.'&day='.$d.'&uid='.$data['uid'].'&redirect='.$redirect;
        } else {
          return $url.'&wiki=edit&action=edit&uid='.$data['uid'].
            '&redirect='.$redirect;
        }

        content_url_var_push ('wiki', $wiki);
      }

      function Editor_ManageScripts () {
        global $act;
        formo ('title=Управление скрптами отображения списка публикаций;');

        if ($act == 'save') {
          $this->settings['script'] =
            manage_template_receive_from_selector ($this->GetClassName ().
                                                   '_list');
          $this->settings['itemScript'] =
            manage_template_receive_from_selector ($this->GetClassName ().
                                                   '_short');

          $this->settings['fullScript'] =
            manage_template_receive_from_selector ($this->GetClassName ().
                                                   '_full');
          $this->SaveSettings ();
        }

        settings_formo (content_url_get_full ().'&act=save');
        println ('Скрипт отображения списка публикаций:');
        manage_template_draw_selector_for_script ($this->GetClassName ().
                                                  '_list',
                                                  $this->settings['script']);
        println ('<div id="hr"></div>');

        println ('Скрипт отображения публикации:');
        manage_template_draw_selector_for_script ($this->GetClassName ().
                                                  '_short',
                                                  $this->settings['itemScript']);
        println ('<div id="hr"></div>');

        if ($this->settings['detailed']) {
          println ('Скрипт отображения подробной публикации:');
          manage_template_draw_selector_for_script ($this->GetClassName ().
                                                  '_full',
                                                  $this->settings['fullScript']);
          println ('<div id="hr"></div>');
        }

        settings_formc ();
        formc ();
      }

      function DisplayScript () {
        if ($this->force_displayScript != false) {
          return $this->force_displayScript;
        }

        $c = manage_spawn_template ($this->settings['script']);

        return $c->GetText ();
      }

      function ItemScript () {
        $c = manage_spawn_template ($this->settings['itemScript']);
        return $c->GetText ();
      }

      function FullScript () {
        $c = manage_spawn_template ($this->settings['fullScript']);
        return $c->GetText ();
      }

      function GetRSSData ($limit) {
        $res = array ();
        $arr = $this->data;
        $n = count ($arr);
        $l = max (0, $n - $limit);

        if ($this->IsDated ()) {
          for ($i = $n - 1; $i >= $l; $i--) {
            $it = $arr[$i];
            $tmp = array ('title'       => $it['title'],
                          'link'        => 'text/?uid='.$it['uid'],
                          'pubdate'     => $it['timestamp'],
                          'description' => fakecode ($it['short']),
                          );
            $res[] = $tmp;
          }
        }

        return $res;
      }
    }

    content_Register_CClass ('CCPublication', 'Публикация');
  }
?>
