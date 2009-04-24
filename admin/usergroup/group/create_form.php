<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Group creation form
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF != '') {
    print ('HACKERS?'); die;
  }

  dd_formo ('title=Создать новую группу;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var name = getElementById ('name').value;
    if (qtrim (name) == '') {
      alert ('Имя создаваемой группы не может быть пустым.');
      return false;
    }
    frm.submit ();
  }
</script>

<form action=".?action=create" method="POST" onsubmit="check (this); return false;">
  Имя группы:
  <input type="text" id="name" name="name" value="<?=htmlspecialchars (stripslashes ($_POST['name']));?>" class="txt block"><div id="hr"></div>
  Добавлять в эту группу вновь регистрируемых пользователей<br>
<?php
  manage_setting_print ('CSCCheckBox', 'default_group');
?>
  <div id="hr"></div>
  <div class="formPast">
    <button class="submitBtn block" type="submit">Создать</button>
  </div>
</form>
<?php
  dd_formc ();
?>
