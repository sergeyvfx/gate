<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Dataset creation form generator
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

  dd_formo ('title=Создать новый набор данных;');
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var name = getElementById ('name').value;

    if (qtrim (name) == '') {
      alert ('Имя создаваемого набора данных не может быть пустым.');
      return false;
    }

    frm.submit ();
  }
</script>

<form action=".?action=create" method="POST" onsubmit="check (this); return false;">
  Название нового набора данных:
  <input type="text" id="name" name="name" value="<?=htmlspecialchars (stripslashes($_POST['name']));?>" class="txt block">
  <div class="formPast">
    <button class="submitBtn block" type="submit">Создать</button>
  </div>
</form>

<?php
  dd_formc ();
?>
