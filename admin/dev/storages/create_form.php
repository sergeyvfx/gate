<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Script for storage creation form generation
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF != '') {
    print ('HACKERS?');
    die;
  }

  dd_formo ('title=Создать новое хранилище данных;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var name = getElementById ('name').value;
    var path = getElementById ('path').value;

    if (qtrim (name) == '') {
      alert ('Название создаваемого хранилища данных не может быть пустым.');
      return false;
    }

    if (!checkDir ('/'+path)) {
      alert ('Указан некорректный путь. В названиях каталогов могут быть лишь буквы латинского алфавита и цифры.');
      return false;
    }

    frm.submit ();
  }

  function update_path_check (http_request) {
    if (http_request.readyState == 4) {
      if (http_request.responseText == '-ERR')
        show_msg ('path_check_res', 'ok', 'Данный путь к хранилищу данных является корректным и Вы можете его использовать.'); else
        show_msg ('path_check_res', 'err', 'Данный путь к хранилищу данных уже используется.');
    }
  }

  function check_path () {
    var path = getElementById ('path').value;

    if (!checkDir ('/'+path)) {
      show_msg ('path_check_res', 'err', 'Указан некорректный путь. В названиях каталогов могут быть лишь буквы латинского алфавита и цифры.');
      return false;
    }

    ipc_send_request ('/', 'ipc=check_path_exists&cpath=<?=config_get ('storage-root');?>/'+path, update_path_check);
  }
</script>

<form action=".?action=create" method="POST" onsubmit="check (this); return false;">
  Навание хранилища данных:
  <input type="text" id="name" name="name" value="<?=htmlspecialchars (stripslashes($_POST['name']));?>" class="txt block"><div id="hr"></div>
  Путь на сервере:
  <input type="text" id="path" name="path" value="<?=htmlspecialchars (stripslashes($_POST['path']));?>" class="txt block">
  <button class="block" type="button" onclick="check_path ();">Проверить</button>
  <div id="path_check_res" style="display: none;"></div>
  <div class="formPast">
    <button class="submitBtn block" type="submit">Создать</button>
  </div>
</form>
<?php
  dd_formc ();
?>
