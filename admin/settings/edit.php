<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Setting edit form generation
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

  global $id, $section;
  $d = manage_settings_get_section_element ($id);
  formo ('title=Информация о настройке;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var name     = getElementById ('name').value;
    var section = getElementById ('section').value;

    if (qtrim (section) == '') {
      alert ('Нельзя сменить секцию опции на пустую.');
      return false;
    }

    if (qtrim (name) == '') {
      alert ('Нельзя сменить имя опции на пустое.');
      return false;
    }

    frm.submit ();
  }
</script>

<form action=".?section=<?=$section;?>&action=save_name&id=<?=$id;?>" method="post" onsubmit="check (this); return false;">
  Секция:
  <input type="text" id="section" name="section" value="<?=$d['section'];?>" class="txt block"><div id="hr"></div>
  Имя опции данных:
  <input type="text" id="name" name="name" value="<?=$d['name'];?>" class="txt block">
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.?section=<?=$section;?>');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<?php
  formc ();
?>
