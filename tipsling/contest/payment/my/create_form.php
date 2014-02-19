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
$(function(){
    var $paymentOption = $('#paymentOption'),
        $date = $('#date'),
        $amount = $('#amount'),
        $team_numbers = $('#teamNumbers'),
        $payer_full_name = $('#payer_full_name'),
        $cheque_number = $('#cheque_number'),
        $comment = $('#comment'),
        
        check_frm = function() {
            var paymentOption = $paymentOption.val(),
            date = qtrim($date.val()),
            amount = qtrim($amount.val()),
            team_numbers = qtrim($team_numbers.val()),
            payer_full_name   = qtrim($payer_full_name.val()),
            cheque_number = qtrim($cheque_number.val()),
            comment = qtrim($comment.val());

            if (paymentOption == '') {
              alert("Поле \"Вариант оплаты\" обязательно для заполнения");
              return;
            }
            
            if (date == '') {
              alert("Поле \"Дата\" обязательно для заполнения");
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
            
            if (team_numbers == '') {
              alert("Поле \"Номера команд, за которых оплачено\" обязательно для заполнения");
              return;
            }

            if (payer_full_name == '') {
              alert("Поле \"Полное имя плательщика\" обязательно для заполнения");
              return;
            }
            
            if (paymentOption == 1 && cheque_number == '') {
              alert("Поле \"Номер чека-ордера\" обязательно для заполнения");
              return;
            }            

            if (comment.length > <?=opt_get('max_comment_len');?>) {
              alert("Поле \"Комментарий\" не может содержать более <?=opt_get('max_comment_len');?> символов");
              return;
            }

            $('form').submit();
        },
        check_frm_date = function(){
            if (qtrim($date.val())=='') {
                show_msg ('date_check_res', 'err', 'Это поле обязательно для заполнения');
                return;
            }
            hide_msg('date_check_res');
        },
        check_frm_amount = function(){
            if (qtrim($amount.val())=='') {
                show_msg ('amount_check_res', 'err', 'Это поле обязательно для заполнения');
                return;
            }
            if (!isRealNumber($amount.val())) {
                show_msg ('amount_check_res', 'err', 'Поле заполненно некорректно');
                return;
            }
            hide_msg('amount_check_res');
        },
        check_frm_teamNumbers = function(){
            if (qtrim($team_numbers.val())=='') {
                show_msg ('teamNumbers_check_res', 'err', 'Это поле обязательно для заполнения');
                return;
            }
            hide_msg('teamNumbers_check_res');
        },
        check_frm_payer = function(){
            if (qtrim($payer_full_name.val())=='') {
                show_msg ('payer_check_res', 'err', 'Это поле обязательно для заполнения');
                return;
            }
            hide_msg('payer_check_res');
        },
        check_frm_cheque = function(){
            if (qtrim($cheque_number.val())=='') {
                show_msg ('cheque_check_res', 'err', 'Это поле обязательно для заполнения');
                return;
            }
            hide_msg('cheque_check_res');
        },
        check_frm_comment = function(){
            if ($comment.val().length > <?=opt_get('max_comment_len');?>) {
                show_msg ('comment_check_res', 'err', 'Поле "Комментарий" не может содержать более <?=opt_get('max_comment_len');?> символов');
                return;
            }
            hide_msg('comment_check_res');
        },
        AddRepostField = function(){
            $('#reposts').find('tr:last').after("<tr><td><input type='text' class='txt block' name='repost[]' value=''/></td><td width='24' style='text-align:right;'><img class='btn' src='<?=config_get('document-root')?>/pics/cross.gif'/></td></tr>");
        },
        RemoveRepostField = function(){
            var $rows = $(this).closest('table').find('tr');
            if ($rows.length>1){
                $(this).closest('tr').remove();
            }
        };
        
    $('#addRepost').on('click', AddRepostField);
    $('#reposts').on('click', 'img',  RemoveRepostField);
    
    $paymentOption.on('change', function(){
        var $table = $cheque_number.closest('table');
        if ($(this).val() == 1){
            $table.show();
            $table.nextAll('div:first').show();
            $table.nextAll('div:eq(1)').show();
        }
        else {
            $table.hide();
            $table.nextAll('div:first').hide();
            $table.nextAll('div:eq(1)').hide();
        }
    });
    $('form').on('submit', check_frm);
    $date.on('blur', check_frm_date);
    $amount.on('blur', check_frm_amount);
    $team_numbers.on('blur', check_frm_teamNumbers);
    $payer_full_name.on('blur', check_frm_payer);
    $cheque_number.on('blur', check_frm_cheque);
    $comment.on('blur', check_frm_comment);
});

</script>
<div>
    <form action=".?action=create&page=<?=$page?>" method="POST" onsubmit="check(this); return false;">
        <table class="clear" width="100%">
            <tr><td width="30%" style="padding: 0 2px;">
                    Вариант оплаты: <span class="error">*</span>
                </td>
                <td style="padding: 0 2px;">
                    <select id="paymentOption" name="paymentOption">
                        <option value="1">Учебный центр "Информатика" (банковский перевод)</option>
                        <option value="2">Учебный центр "Информатика" (безналичный расчет)</option>
                        <option value="3">Учебный центр "Информатика" (в кассе)</option>
                        <option value="4">Яндекс.Деньги</option>
                        <option value="5">Перевод на карту Сбербанка (по номеру карты)</option>
                        <option value="6">Перевод на карту Сбербанка (по реквизитам)</option>
                        <option value="-1">Другое (указать в примечании)</option>
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
                    <?= calendar('date') ?>
                </td>
            </tr>
        </table>
        <div id="date_check_res" style="display: none;"></div>
        <div id="hr"></div>
        <table class="clear" width="100%">
            <tr><td width="30%" style="padding: 0 2px;">
                    Сумма платежа: <span style="color: red">*</span>
                </td>
                <td style="padding: 0 2px;">
                    <input type="text" id="amount" name="amount" class="txt block">
                </td>
            </tr>
        </table>
        <div id="amount_check_res" style="display: none;"></div>
        <div id="hr"></div>
        <table class="clear" width="100%">
            <tr><td width="30%" style="padding: 0 2px;">
                    Номера команд, за которых оплачено: <span style="color: red">*</span>
                </td>
                <td style="padding: 0 2px;">
                    <input type="text" id="teamNumbers" name="teamNumbers" class="txt block">
                </td>
            </tr>
        </table>
        <div id="teamNumbers_check_res" style="display: none;"></div>
        <div id="hr"></div>
        <table class="clear" width="100%">
            <tr><td width="30%" style="padding: 0 2px;">
                    Полное имя плательщика: <span style="color: red">*</span>
                </td>
                <td style="padding: 0 2px;">
                    <input type="text" id="payer_full_name" name="payer_full_name" class="txt block"/>
                </td>
            </tr>
        </table>
        <div id="payer_check_res" style="display: none;"></div>
        <div id="hr"></div>
        <table class="clear" width="100%">
            <tr><td width="30%" style="padding: 0 2px;">
                    Номер чек-ордера: <span class="error">*</span>
                </td>
                <td style="padding: 0 2px;">
                    <input type="text" id="cheque_number" name="cheque_number" class="txt block"/>
                </td>
            </tr>
        </table>
        <div id="cheque_check_res" style="display: none;"></div>
        <div id="hr"></div>
        <table class="clear" width="100%">
            <tr><td width="30%" style="padding: 0 2px;">
                    Адреса репостов:
                </td>
                <td style="padding: 0 2px;">
                    <table width="100%" id="reposts">
                        <tr>
                            <td>
                                <input type='text' class='txt block' name='repost[]'/>
                            </td>
                            <td width='24' style='text-align:right;'>
                                <img class='btn' src='<?=config_get('document-root')?>/pics/cross.gif'/>
                            </td>
                        </tr>
                    </table>
                    <button id="addRepost" type="button" class="submitBtn">Добавить</button>
                </td>
            </tr>
        </table>
        <div id="hr"></div>
        <table class="clear" width="100%">
            <tr><td width="30%" style="padding: 0 2px;">
                    Комментарий:
                </td>
                <td style="padding: 0 2px;">
                    <input type="text" id="comment" name="comment" class="txt block"/>
                </td>
            </tr>
        </table>
        <div id="comment_check_res" style="display: none;"></div>
        <div id="hr"></div>
        <!--<table class="clear" width="100%">
            <tr><td width="30%" style="padding: 0 2px;">
                    Файл:
                </td>
                <td style="padding: 0 2px;">
                    <input type="file" id="file" name="file"/>
                </td>
            </tr>
        </table>
        <div id="file_check_res" style="display: none;"></div>
        <div id="hr"></div>-->
        <div class="formPast">
          <button class="submitBtn block" type="submit">Сохранить</button>
        </div>
    </form>
</div>
<?php
dd_formc ();
?>
