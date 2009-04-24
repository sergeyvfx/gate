<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Datatype editing form generation script
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
  $d = manage_spawn_datatype ($id);
  formo ('title=Информация о типе данных;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var className = getElementById ('className').value;

    if (qtrim (className) == '') {
      alert ('Нельзя сменить имя типа данных на пустое.');
      return false;
    }

    frm.submit ();
  }
</script>

<form action=".?action=save&id=<?=$id;?>" method="post" onsubmit="check (this); return false;">
  Название типа данных:
  <input type="text" id="className" name="className" value="<?=$d->GetName ();?>" class="txt block">
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<?php
  formc ();
?>
