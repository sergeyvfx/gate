<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Datatype field editing form
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

  global $id, $viewelement;
  formo ('title=Поле набора данных;');

  $d = new CDataField ();
  $d->Init ($viewelement);
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var title = getElementById ('title').value;

    if (qtrim (title) == '') {
      alert ('Нельзя сменить имя поля набора данных на пустое.');
      return false;
    }

    frm.submit ();
  }
</script>

<form action=".?action=edit&id=<?=$id;?>&act=saveelement&eid=<?=$viewelement;?>" method="post" onsubmit="check (this); return false;">
  Название поля набора данных:
  <input type="text" id="title" name="title" value="<?=$d->GetTitle ();?>" class="txt block"><div id="hr"></div>
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.?action=edit&id=<?=$id;?>');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</submit>
  </div>
</form>
<?php
  formc ();
?>
