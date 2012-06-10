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
  print 'HACKERS?';
  die;
}

global $current_contest, $document_root;

if (!user_authorized ()) {
  header('Location: ../../../../login');
}

$it = contest_get_by_id($current_contest);
$query = arr_from_query("select * from Admin_FamilyContest ".
                   "where family_contest_id=".$it['family_id']." and ".
                   "user_id=".user_id());
if (count ($query) <= 0)
{
  print (content_error_page(403));
  return;
}
?>

<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest" ?>"><?=$it['name']?></a><a>Администрирование</a>Рассылка писем</div>
${information}
<div class="form">
  <div class="content">
    <?php
    global $action;
    
    include '../menu.php';
    $admin_menu->SetActive('MailSender');
    
    $admin_menu->Draw();
    
    $to = 'keeperami@gmail.com';
    $subject = 'Test info letter';
    $message = 'Content\n';
    $additional_headers = '';
    mail($to, $subject, $message, $additional_headers);
    
    //sendmail_tpl(stripslashes("keeperami@gmail.com"), 'Тестовое информационное письмо', 'mail', array());
    
    /*if (mail("keeperami@gmail.com", "test", "text"))
    {
        echo("<div>письмо ушло</div>");
    }
    else
        echo("<div>че-то не так</div>");
    */
    ?>
  <form action=".?action=save&id=<?= $id; ?><?= (($page != '') ? ('&page=' . $page) : ('')); ?>" method="POST" onsubmit="check (this); return false;">
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Получатель:
            </td>
            <td style="padding: 0 2px;">
                <input type="text" id="address" name="address" onblur="check_frm_address ();" value="<?= $POST['address']; ?>" class="txt block"/>
            </td>
        </tr>
    </table>
    <div id="name_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Начало регистрации:
            </td>
            <td style="padding: 0 2px;">
                <?= calendar('registration_start', htmlspecialchars($contest['registration_start'])) ?>
            </td>
        </tr>
    </table>
    <div id="r_s_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Конец регистрации:
            </td>
            <td style="padding: 0 2px;">
                <?= calendar('registration_finish', htmlspecialchars($contest['registration_finish'])) ?>
            </td>
        </tr>
    </table>
    <div id="r_f_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Начало конкурса:
            </td>
            <td style="padding: 0 2px;">
                <?= calendar('contest_start', htmlspecialchars($contest['contest_start'])) ?>
            </td>
        </tr>
    </table>
    <div id="c_s_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Конец конкурса:
            </td>
            <td style="padding: 0 2px;">
                <?= calendar('contest_finish', htmlspecialchars($contest['contest_finish'])) ?>
            </td>
        </tr>
    </table>
    <div id="r_s_check_res" style="display: none;"></div>
    <div id="hr"></div>
    <table class="clear" width="100%">
        <tr><td width="30%" style="padding: 0 2px;">
                Дата добавления в архив:
            </td>
            <td style="padding: 0 2px;">
                <?= calendar('send_to_archive', htmlspecialchars($contest['send_to_archive'])) ?>
            </td>
        </tr>
    </table>
    <div id="s_to_a_check_res" style="display: none;"></div>
    <div id="hr"></div>
    
    <div class="formPast">
      <button class="submitBtn block" type="submit">Сохранить</button>
    </div>
  </form>      

  </div>
</div>