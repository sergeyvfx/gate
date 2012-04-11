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

dd_formo('title=Новый платеж;');
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
      alert("Поле \"Сумма платежа\" заполнено некорректно");
      return;
    }

    if (comment.length > <?=opt_get('max_comment_len');?>) {
      alert("Поле \"Комментарий\" не может содержать более <?=opt_get('max_comment_len');?> символов");
      return;
    }

    frm.submit ();
  }

  function check_frm_date() {
    var date = getElementById ('date').value;

    if (qtrim(date)=='') {
        show_msg ('date_check_res', 'err', 'Это поле обязательно для заполнения');
        return;
    }

    hide_msg('date_check_res');
  }

  function check_frm_cheque() {
    var cheque = getElementById ('cheque_number').value;

    if (qtrim(cheque)=='') {
        show_msg ('cheque_check_res', 'err', 'Это поле обязательно для заполнения');
        return;
    }

    hide_msg('cheque_check_res');
  }

  function check_frm_payer() {
    var payer = getElementById ('payer_full_name').value;

    if (qtrim(payer)=='') {
        show_msg ('payer_check_res', 'err', 'Это поле обязательно для заполнения');
        return;
    }

    hide_msg('payer_check_res');
  }
  function check_frm_amount() {
    var amount = getElementById ('amount').value;

    if (qtrim(amount)=='') {
        show_msg ('amount_check_res', 'err', 'Это поле обязательно для заполнения');
        return;
    }
    if (!isRealNumber(amount)) {
        show_msg ('amount_check_res', 'err', 'Поле заполненно некорректно');
        return;
    }

    hide_msg('amount_check_res');
  }
  function check_frm_comment() {
    var comment = getElementById ('comment').value;

    if (comment.length > <?=opt_get('max_comment_len');?>) {
        show_msg ('comment_check_res', 'err', 'Поле "Комментарий" не может содержать более <?=opt_get('max_comment_len');?> символов');
        return;
    }

    hide_msg('comment_check_res');
  }

</script>
<div>
  <form action=".?action=create&page=<?=$page?>" method="POST" onsubmit="check(this); return false;">
      <table class="clear" width="100%">
        <tr>
            <td width="30%" style="padding: 0 2px;">
                Конкурс:
            </td>
            <td style="padding: 0 2px;">
                <select id="ContestGroup" name ="ContestGroup">
                    <?php
                        foreach ($reg_contests as $k)
                        {
                            $selected = ($k['id'] == $_POST['grade']) ? ('selected') : ('');
                            echo('<option value = "' . $k['id'] . '" '.$selected.' >' . $k['name'] . '</option>');
                        }
                    ?>
                </select>
            </td>
        </tr>
      </table>
      <div id="hr"></div>
      <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Дата платежа: <span class="error">*</span>
            </td>
            <td style="padding: 0 2px;">
                <?= calendar('date', htmlspecialchars(stripslashes($_POST['date']))) ?>
            </td>
        </tr>
    </table>
    <div id="date_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Номер чек-ордера: <span class="error">*</span>
            </td>
            <td style="padding: 0 2px;">
                <input type="text" id="cheque_number" name="cheque_number" onblur="check_frm_cheque ();" value="<?= htmlspecialchars(stripslashes($_POST['cheque_number'])); ?>" class="txt block">
            </td>
        </tr>
    </table>
    <div id="cheque_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Полное имя плательщика: <span style="color: red">*</span>
            </td>
            <td style="padding: 0 2px;">
                <input type="text" id="payer_full_name" name="payer_full_name" onblur="check_frm_payer ();" value="<?= htmlspecialchars(stripslashes($_POST['payer_full_name'])); ?>" class="txt block">
            </td>
        </tr>
    </table>
    <div id="payer_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Сумма платежа: <span style="color: red">*</span>
            </td>
            <td style="padding: 0 2px;">
                <input type="text" id="amount" name="amount" onblur="check_frm_amount ();" value="<?= htmlspecialchars(stripslashes($_POST['amount'])); ?>" class="txt block">
            </td>
        </tr>
    </table>
    <div id="amount_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Примечание:
            </td>
            <td style="padding: 0 2px;">
                <input type="text" id="comment" name="comment" onblur="check_frm_comment ();" value="<?= htmlspecialchars(stripslashes($_POST['comment'])); ?>" class="txt block"><div id="hr"></div>
            </td>
        </tr>
    </table>
    <div id="comment_check_res" style="display: none;"></div>
    <div class="formPast">
      <button class="submitBtn block" type="submit">Сохранить</button>
    </div>
  </form>
</div>
<?php
dd_formc ();
?>
