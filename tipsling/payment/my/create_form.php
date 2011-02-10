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

dd_formo('title=Добавить платеж;');
?>
<script language="JavaScript" type="text/javascript">
  function check(frm) {
    var date = qtrim(getElementById ('date').value);
    var cheque_number = qtrim(getElementById('cheque_number').value);
    var payer_full_name   = qtrim(getElementById ('payer_full_name').value);
    var amount = qtrim(getElementById('amount').value);
    var comment = qtrim(getElementById('comment').value);

    if (date == '') {
      alert("Поле \"Дата\" обязательно для заполнения");
      return;
    }

    if (cheque_number == '') {
      alert("Поле \"Номер чека-ордера\" обязательно для заполнения");
      return;
    }

    if (payer_full_name == '') {
      alert("Поле \"Полное имя плательщика\" обязательно для заполнения");
      return;
    }

    if (amount == '') {
      alert("Поле \"Сумма платежа\" обязательно для заполнения");
      return;
    }

    if (!isRealNumber(amount)) {
      alert("В поле \"Сумма платежа\" должно быть число с двумя знаками после запятой");
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
  <form action=".?action=create&page=<?=$page?>" method="POST" onsubmit="check(this); return false;">
    Дата платежа: <span style="color: red">*</span>
    <?= calendar('date', htmlspecialchars(stripslashes($_POST['date']))) ?>
    <div id="hr"></div>
    Номер чека-ордера: <span style="color: red">*</span>
    <input type="text" id="cheque_number" name="cheque_number" value="<?= htmlspecialchars(stripslashes($_POST['cheque_number'])); ?>" class="txt block">
    <div id="hr"></div>
    Полное имя плательщика: <span style="color: red">*</span>
    <input type="text" id="payer_full_name" name="payer_full_name" value="<?= htmlspecialchars(stripslashes($_POST['payer_full_name'])); ?>" class="txt block">
    <div id="hr"></div>
    Сумма платежа: <span style="color: red">*</span>
    <input type="text" id="amount" name="amount" value="<?= htmlspecialchars(stripslashes($_POST['amount'])); ?>" class="txt block">
    <div id="hr"></div>
    Комментарий:
    <input type="text" id="comment" name="comment" value="<?= htmlspecialchars(stripslashes($_POST['comment'])); ?>" class="txt block"><div id="hr"></div>
    <div class="formPast">
      <button class="submitBtn block" type="submit">Создать</button>
    </div>
  </form>
</div>
<?php
dd_formc ();
?>
