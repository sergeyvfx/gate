<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Template editing form generator
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
  $t = manage_spawn_template ($id);
  formo ('title=Информация о шаблоне;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var name = getElementById ('name').value;

    if (qtrim (name) == '') {
      alert ('Нельзя сменить имя шаблона данных на пустое.');
      return false;
    }

    frm.submit ();
  }
</script>

<form action=".?action=save&id=<?=$id;?>" method="post" onsubmit="check (this); return false;">
  Название шаблона:
  <input type="text" id="name" name="name" value="<?=$t->GetName ();?>" class="txt block"><div id="hr"></div>
  Текст:
  <textarea class="block" rows="14" name="text"><?=htmlspecialchars (ecranvars ($t->GetText ()));?></textarea><div id="hr"></div>
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<?php
  formc ();
?>
