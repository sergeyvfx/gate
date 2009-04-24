<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Edit group script
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
  $d = group_get_by_id ($id);
  formo ('title=Информация о группе пользователей;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var name = getElementById ('name').value;
    if (qtrim (name)=='') {
      alert ('Нельзя сменить имя группы на пустое.');
      return false;
    }
    frm.submit ();
  }
</script>

<form action=".?action=save&id=<?=$id;?>" method="post" onsubmit="check (this); return false;">
  Имя группы:
  <input type="text" id="name" name="name" value="<?=$d['name'];?>" class="txt block"><div id="hr"></div>
  Добавлять в эту группу вновь регистрируемых пользователей<div id="hr"></div>
<?php
  $t = new CSCCheckBox ();
  $t->Init ('default_group', $d['default']);
  $t->Draw ();
?>
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<?php
  formc ();
?>
