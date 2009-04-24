<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Logging service
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

  if ($_CSCLog_included_ != '#CSCLog_Included#') {
    $_CSCLog_included_ = '#CSCLog_Included#';

    class CSCLog extends CSCVirtual {
      var $sName;

      function InitInstance ($id = -1, $virtual = false) {
        $this->id = $id;
        $this->_virtual=$virtual;

        if (!$virtual) {
          content_url_var_push_global ('action');
          content_url_var_push_global ('id');
          editor_add_function ('Управление сервисом', 'Editor_LogManage');
          editor_add_function ('Просмотр журналов',   'Editor_LogView');
        }
      }

      function CSCLog () {
        $this->SetServiceName ('Бортовой журнал');
        $this->SetClassName ('CSCLog');
      }

      function ReceiveSettings () {
        $this->settings['active']=true;
      }

      function Create () {
        db_create_table_safe ('log', array (
          'id'               => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
          'timestamp'        => 'INT',
          'module'           => 'TEXT',
          'status'           => 'TEXT',
          'message'          => 'TEXT'
        ));
        $this->ReceiveSettings ();
        $this->AppendEntry ('CORE', 'MESSAGE', 'Подключен сервис журналирования');
      }

      function PerformDeletion () { db_destroy_table ('log'); }

      function CanCreate () {
        if (db_count ('service', '`sclass` = "CSCLog"') == 0) {
          return true;
        }

        add_info ('Может существовать лишь один серфис журналирования.');

        return false;
      }

      function FreeLogs () { db_delete ('log'); }

      function AppendEntry ($module, $status, $message) {
        if (!$this->settings['active']) {
          return false;
        }

        $message = trim ($message);

        if ($message == '') {
          return false;
        }

        $module  = db_html_string ($module);
        $status  = db_html_string ($status);
        $message = db_html_string ($message);

        db_insert ('log', array ('timestamp'=>time (), 'module'=>"$module",
          'status'=>"$status", 'message'=>"$message"));
        return true;
      }

      function Editor_LogView () {
        global $CORE;
        $CORE->AddStyle ('log');

        formo ('title=Просмотр журналов');

        global $count, $display;

        if ($count != 512 && $count != 1024 && $count != -1) {
          $count = 256;
        }

        $clause = '';

        if ($display == 'error')   $clause = '`status`="ERROR"';
        if ($display == 'warning') $clause = '`status`="WARNING"';
        if ($display == 'message') $clause = '`status`<>"WARNING" AND `status`<>"ERROR"';

        $q = db_select ('log', array ('*'), $clause, 'ORDER BY `timestamp` DESC '.
          ($count > 0) ? ('LIMIT '.$count):(''));
?>
          <script language="JavaScript" type="text/javascript">
              function update () {
                var prefix = '<?=content_url_get_full ();?>';
                var count = getElementById ('count').value;
                var display = getElementById ('display').value;
                nav (prefix + '&count=' + count + '&display = '+display);
              }
           </script>

          <table width="100%"><tr>
            <td width="196">
              Количество записей:&nbsp;&nbsp;<select id="count">
                <option value="256"<?=(($count==256)?(' selected'):(''));?>>256</option>
                <option value="512"<?=(($count==512)?(' selected'):(''));?>>512</option>
                <option value="1024"<?=(($count==1024)?(' selected'):(''));?>>1024</option>
                <option value="-1"<?=(($count==-1)?(' selected'):(''));?>>Все</option>
              </select>
            </td>

            <td width="208">
              Отображать:&nbsp;&nbsp;<select id="display">
                <option value="all"<?=(($display=='all')?(' selected'):(''));?>>Все сообщения</option>
                <option value="warning"<?=(($display=='warning')?(' selected'):(''));?>>Предупреждения</option>
                <option value="error"<?=(($display=='error')?(' selected'):(''));?>>Ошибки</option>
                <option value="message"<?=(($display=='message')?(' selected'):(''));?>>Сообщения</option>
              </select>
            </td>

            <td align="right">
              <button class="submitBtn" onclick="update ();">Обновить</button>
            </td>
          </tr></table><div id="hr"></div>
<?php
         if (db_affected () > 0) {
          println ('<div class="scroll" style="height: 320px; margin-top: 4px;" id="log">');

          while ($r = db_row ($q)) {
            $class = "msg";
            if ($r['status'] == 'ERROR') $class = 'err';
            if ($r['status'] == 'WARNING') $class = 'warning';

            $status = 'Сообщение';

            if ($r['status'] == 'ERROR') $status = 'Ошибка';
            if ($r['status'] == 'WARNING') $status = 'Предупреждение';

            println ('<div class="'.$class.'"><table><tr><td class="time">'.
              format_ltime ($r['timestamp']).'</td><td class="module">'.$r['module'].
              '</td><td class="status">'.$status.'</td><td class="msg">'.$r['message'].
              '</td></tr></table></div>');
            }

          println ('</div>');
        } else println ('<center><i>Файл журнала пуст</i></center>');

        formc ();
      }

      function Editor_LogManage () {
        global $active, $act, $CORE;
        $CORE->AddStyle ('log');

        if (isset ($active)) {
          if ($active == '0') {
            $this->settings['active'] = false;
          } else {
            $this->settings['active'] = true;
          }
          $this->UpdateSettings ();
        }

        if ($act == 'free') {
          $this->FreeLogs ();
        }

        redirector_add_skipvar ('act', 'free');
        $url = content_url_get_full ();
        $update_active = 'nav (\''.$url.'&active='.(($this->settings['active'])?('0'):('1')).'\');';
        $free_logs = 'cnav (\'Вы уверены что хотите очистить все файлы журналов?\', \''.$url.'&act=free\');';
        formo ('title=Управление сервисом');
?>
  <input type="checkbox" class="cb" onclick="<?=$update_active;?>" value="1"<?=(($this->settings['active'])?(' checked'):(''));?>>&nbsp;Вести журналирование <i>(если данная опция неактивна, то сервис журналирования продолжит существовать, однако все журналирование будет отключено)</i><div id="hr"></div>
  <button class="block" onclick="<?=$free_logs;?>">Очистить файлы журналов</button>
<?php
        formc ();
      }
    }

    content_Register_SCClass ('CSCLog', 'Бортовой журнал');
  }
?>
