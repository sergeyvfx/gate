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
    var comment = qtrim(getElementById('comment').value);

    if (qtrim(grade)==''){
      alert('Укажите класс команды')
      return;
    }

    if (!isnumber(grade)){
      alert('Класс должен быть числом')
      return;
    }

    if (qtrim(teacher_full_name)==''){
      alert('ФИО учителя не может быть пустым');
      return;
    }

    if (qtrim(pupil1_full_name)==''){
      alert('ФИО первого участника не может быть пустым');
      return;
    }

    if (comment.length > <?=opt_get('max_comment_len');?>) {
      alert("Поле \"Комментарий\" не может содержать более <?=opt_get('max_comment_len');?> символов");
      return;
    }

    frm.submit ();
  }
</script>
<div>
  <form action=".?action=create&page=<?= $page ?>" method="POST" onsubmit="check (this); return false;">
    Класс участников: <span class="error">*</span>
    <input type="text" id="grade" name="grade" value="<?= htmlspecialchars(stripslashes($_POST['grade'])); ?>" class="txt block"><div id="hr"></div>
    Полное имя учителя: <span class="error">*</span>
    <?php
    $teacher_full_name = htmlspecialchars(stripslashes($_POST['teacher_full_name']));
    if ($teacher_full_name == '') {
      $u = user_get_by_id(user_id());
      $teacher_full_name = $u['surname'] . ' ' . $u['name'] .
              (($u['patronymic'] == '') ? ('') : (' ' . $u['patronymic']));
    }
    print('<input type="text" id="teacher_full_name" name="teacher_full_name" value="' . $teacher_full_name . '" class="txt block"><div id="hr"></div>');
    ?>
    Полное имя 1-го участника: <span class="error">*</span>
    <input type="text" id="pupil1_full_name" name="pupil1_full_name" value="<?= htmlspecialchars(stripslashes($_POST['pupil1_full_name'])); ?>" class="txt block"><div id="hr"></div>
    Полное имя 2-го участника:
    <input type="text" id="pupil2_full_name" name="pupil2_full_name" value="<?= htmlspecialchars(stripslashes($_POST['pupil2_full_name'])); ?>" class="txt block"><div id="hr"></div>
    Полное имя 3-го участника:
    <input type="text" id="pupil3_full_name" name="pupil3_full_name" value="<?= htmlspecialchars(stripslashes($_POST['pupil3_full_name'])); ?>" class="txt block"><div id="hr"></div>
    Платеж:
    <select id="payment_id" name="payment_id" class="block">
      <option value="-1"></option>
      <?php
      $payments = payment_list(user_id());
      foreach ($payments as $p) {
        $amount = $p['amount'];
        if (!preg_match('/\./', $amount)) {
          $amount = $amount . '.00';
        }
        $amount = $amount . ' руб.';
      ?>
        <option value="<?= $p['id'] ?>"><?= $p['date'] . ' ' . $p['cheque_number'] . ' ' . $amount ?></option>
      <?php
      }
      ?>
    </select><div id="hr"></div>
    Примечание:
    <input type="text" id="comment" name="comment" value="<?= htmlspecialchars(stripslashes($_POST['comment'])); ?>" class="txt block"><div id="hr"></div>

    <div class="formPast">
      <button class="submitBtn block" type="submit">Создать</button>
    </div>
  </form>
</div>
<?php
      dd_formc ();
?>
