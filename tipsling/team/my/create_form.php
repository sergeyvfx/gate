<?php
/**
 * Gate - Wiki engine and web-interface for WebTester Server
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

global $page;

dd_formo('title=Добавить команду;');
?>
<script language="JavaScript" type="text/javascript">
  function check(frm) {
    var grade  = getElementById ('grade').value;
    var teacher_full_name = getElementById('teacher_full_name').value;
    var pupil1_full_name   = getElementById ('pupil1_full_name').value;
    var pupil2_full_name = getElementById('pupil2_full_name').value;
    var pupil3_full_name = getElementById ('pupil3_full_name').value;

    if (qtrim(grade)==''){
        alert('Укажите класс команды')
        return;
    }

    if (!isnumber(grade)){
        alert('класс должен быть числом')
        return;
    }

    if (qtrim(teacher_full_name)==''){
        alert('ФИО учителя не может быть пустым');
        return;
    }

    if (qtrim(pupil1_full_name)==''){
        alert('ФИО первого ученика не может быть пустым');
        return;
    }

    //TODO Check fields on errors
    frm.submit ();
  }
</script>
<div>
  <form action=".?action=create&page=<?=$page?>" method="POST" onsubmit="check (this); return false;">
    <span class="error">*</span>Класс участников:
    <input type="text" id="grade" name="grade" value="<?= htmlspecialchars(stripslashes($_POST['grade'])); ?>" class="txt block"><div id="hr"></div>
    <span class="error">*</span>Полное имя учителя:
    <input type="text" id="teacher_full_name" name="teacher_full_name" value="<?= htmlspecialchars(stripslashes($_POST['teacher_full_name'])); ?>" class="txt block"><div id="hr"></div>
    <span class="error">*</span>Полное имя 1-го участника:
    <input type="text" id="pupil1_full_name" name="pupil1_full_name" value="<?= htmlspecialchars(stripslashes($_POST['pupil1_full_name'])); ?>" class="txt block"><div id="hr"></div>
    Полное имя 2-го участника:
    <input type="text" id="pupil2_full_name" name="pupil2_full_name" value="<?= htmlspecialchars(stripslashes($_POST['pupil2_full_name'])); ?>" class="txt block"><div id="hr"></div>
    Полное имя 3-го участника:
    <input type="text" id="pupil3_full_name" name="pupil3_full_name" value="<?= htmlspecialchars(stripslashes($_POST['pupil3_full_name'])); ?>" class="txt block"><div id="hr"></div>
    Платеж:
    <select id="payment_id" name="payment_id" class="block">
      <option value="-1">Нет платежей</option>
      <?php
      //TODO Fill select from payment table
      ?>
    </select><div id="hr"></div>
    <div class="formPast">
      <button class="submitBtn block" type="submit">Создать</button>
    </div>
  </form>
</div>
<?php
dd_formc ();
?>
