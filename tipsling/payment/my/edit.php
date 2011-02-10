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

global $id, $page;
formo('title=Редактирование платежа;');

$payment = payment_get_by_id($id);
?>
<script language="JavaScript" type="text/javascript">
  function check (frm) {
    var date = qtrim(getElementById ('date').value);
    var cheque_number = qtrim(getElementById('cheque_number').value);
    var payer_full_name   = qtrim(getElementById ('payer_full_name').value);
    var amount = qtrim(getElementById('amount').value);

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

    frm.submit ();
  }
</script>

<form action=".?action=save&id=<?= $id; ?>&<?= (($page != '') ? ('&page=' . $page) : ('')); ?>" method="POST" onsubmit="check (this); return false;">
  Дата платежа: <span style="color: red">*</span>
  <?= calendar('date', htmlspecialchars($payment['date'])) ?>
  <div id="hr"></div>
  Номер чека-ордера: <span style="color: red">*</span>
  <input type="text" id="cheque_number" name="cheque_number" value="<?= htmlspecialchars($payment['cheque_number']); ?>" class="txt block">
  <div id="hr"></div>
  Полное имя плательщика: <span style="color: red">*</span>
  <input type="text" id="payer_full_name" name="payer_full_name" value="<?= htmlspecialchars($payment['payer_full_name']); ?>" class="txt block">
  <div id="hr"></div>
  Сумма платежа: <span style="color: red">*</span>
  <input type="text" id="amount" name="amount" value="<?= htmlspecialchars($payment['amount']); ?>" class="txt block">
  <div id="hr"></div>
  Комментарий:
  <input type="text" id="comment" name="comment" value="<?= htmlspecialchars($payment['comment']); ?>" class="txt block"><div id="hr"></div>
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.?<?= (($page != '') ? ('&page=' . $page) : ('')); ?>');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<?php
  formc ();
?>
