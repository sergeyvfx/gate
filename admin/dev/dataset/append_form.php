<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Script for generation form for appending field to dataset
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

  global $id;
  dd_formo ('title=Добавить поле в набор данных;');
?>
<script language="JavaScript" type="text/javascript">
  function appcheck (frm) {
    var ftitle   = getElementById ('ftitle').value;
    var field    = getElementById ('field').value;
    var datatype = getElementById ('datatype').value;

    if (qtrim (ftitle) == '') {
      alert ('Имя создаваемого поля не может быть пустым.');
      return false;
    }

    if (!isalphanum (field)) {
      alert ('Название поля может содержать лишь символы латинского алфавита и цифры.');
      return false;
    }

    if (datatype == '') {
      alert ('Не указан тип данных для использования.');
      return false;
    }

    frm.submit ();
  }
</script>

<form action=".?action=edit&act=append&id=<?=$id;?>" method="POST" onsubmit="appcheck (this); return false;">
  Имя нового поля для набора данных:
  <input type="text" id="ftitle" name="ftitle" value="<?=htmlspecialchars (stripslashes($_POST['ftitle']));?>" class="txt block"><div id="hr"></div>
  Название поля в базе данных для набора данных:
  <input type="text" id="field" name="field" value="<?=htmlspecialchars (stripslashes($_POST['field']));?>" class="txt block"><div id="hr"></div>
  Тип данных:
  <select name="datatype" class="block" id="datatype">
<?php
  $list = manage_datatype_getlist ();
  for ($i = 0; $i < count ($list); $i++) {
    $it = $list[$i];
?>
    <option value="<?=$it['id'];?>"<?=(($_POST['datatype']==$it['id'])?('selected'):(''))?>><?=$it['name']?></option>
<?php
  }
?>
  </select>
  <div class="formPast">
    <button class="submitBtn block" type="submit">Добавить</button>
  </div>
</form>
<?php
  dd_formc ();
?>
