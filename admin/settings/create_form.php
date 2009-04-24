<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Setting creation form
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF!='') {
    print ('HACKERS?');
    die;
  }

  dd_formo ('title=Добавить опцию;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var section   = getElementById ('section').value;
    var name      = getElementById ('name').value;
    var ident     = getElementById ('ident').value;
    var classname = getElementById ('classname').value;

    if (qtrim (section) == '') {
      alert ('Имя секции не может быть пустым.');
      return false;
    }

    if (qtrim (name) == '') {
      alert ('Имя создаваемой опции не может быть пустым.');
      return false;
    }

    if (!isalphanum (ident)) {
      alert ('Имя идентификатора может состоять лишь из букв латинского алфавита и цифр.');
      return false;
    }

    if (qtrim (classname) == '') {
      alert ('Не указан тип создаваемой опции.');
      return false;
    }

    frm.submit ();
  }
</script>
<form action=".?action=create" method="POST" onsubmit="check (this); return false;">
  Секция:
  <input type="text" id="section" name="section" value="<?=htmlspecialchars (stripslashes ($_POST['section']));?>" class="txt block"><div id="hr"></div>
  Название новой опции:
  <input type="text" id="name" name="name" value="<?=htmlspecialchars (stripslashes ($_POST['name']));?>" class="txt block"><div id="hr"></div>
  Идентификатор:
  <input type="text" id="ident" name="ident" value="<?=htmlspecialchars (stripslashes ($_POST['ident']));?>" class="txt block"><div id="hr"></div>
  Тип:
  <select id="classname" name="classname" class="block">
<?php
  $items = manage_settings_class_get_registered ();
  for ($i = 0; $i < count ($items); $i++) {
    $it = $items[$i];
?>
    <option value="<?=$it['class']?>"<?=(($_POST['classname']==$it['class'])?(' selected'):(''));?>><?=$it['pseudonym'];?></option>
<?php
  }
?>
  </select>
  <div class="formPast">
    <button class="submitBtn block" type="submit">Создать</button>
  </div>
</form>
<?php
  dd_formc ();
?>
