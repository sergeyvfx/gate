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

if (!user_authorized ()) {
  header('Location: ' . config_get('document-root') . '/login/profile');
}

if (!is_responsible(user_id())) {
  print (content_error_page(403));
  return;
}

global $DOCUMENT_ROOT, $redirect, $action;
include $DOCUMENT_ROOT . '/login/profile/inc/menu.php';
include '../menu.php';
$profile_menu->SetActive('info');
$info_menu->SetActive('additional');

if ($action == 'save') {
  global $email, $phone, $comment;
  $email = stripslashes($email);
  $phone = stripslashes($phone);
  $comment = stripslashes($comment);

  $arr = array();

  $r = responsible_get_by_id(user_id());

  //TODO Add check of phone

  if ($r['email'] != '' && !check_email($email)) {
    add_info('Указанный E-Mail не выглядит корректным');
  } else if (user_registered_with_email($email, user_id())) {
    add_info('Такой E-Mail уже используется.');
  } else {
    $arr['email'] = db_string($email);
  }
  //TODO Add saving data
//  if (count($arr) > 0) {
//    db_update('user', $arr, '`id`=' . user_id ());
//  }
}

$r = responsible_get_by_id(user_id());

$f = new CVCForm ();
$f->Init('', 'action=.?action\=save' . (($redirect != '') ? ('&redirect=' . prepare_arg($redirect) . ';backlink=' . prepare_arg($redirect)) : ('')) . ';method=POST;add_check_func=check;');
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Дополнительный E-mail:</td><td><input id="email" name="email" onblur="check_frm_email ();" type="text" class="txt block" value="' . htmlspecialchars($u['email']) . '"></td></tr></table><div id="email_check_res" style="display: none;"></div>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Дополнительный телефон:</td><td><input id="phone" name="phone" onblur="check_frm_phone ();" type="text" class="txt block" value="' . htmlspecialchars($sc['phone']) . '"></td></tr></table><div id="phone_check_res" style="display: none;"></div>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Откуда Вы узнали о конкурсе?</td><td><input id="comment" name="comment" type="text" class="txt block" value="' . htmlspecialchars($sc['comment']) . '"></td></tr></table>'));
?>

<script language="JavaScript" type="text/JavaScript">
  function check_frm_phone() {
    var phone = getElementById('phone').value;

    if (phone == '') {
      set_display('phone_check_res', 'none');
      return true;
    }
    if (check_phone(phone)) {
      show_msg ('phone_check_res', 'ok', 'Данный телефон является корректным и Вы можете его использовать.');
    } else {
      show_msg ('phone_check_res', 'err', 'Указанный телефон не выглядит корректным. Его использование невозможно.');
    }
  }
  
  function update_email_check (http_request) {
    if (http_request.readyState == 4) {
      if (http_request.responseText == '+OK')
        show_msg ('email_check_res', 'ok', 'Данный адрес электронной почты пользователя является корректным и Вы можете его использовать.'); else
          show_msg ('email_check_res', 'err', 'Вы не можете использовать этот адрес электронной почты, так как он уже используется.');
    }
  }

  function check_frm_email () {
    var email = getElementById ('email').value;

    if (email == '') {
      set_display('email_check_res', 'none');
      return true;
    }

    if (!check_email (email)) {
      show_msg ('email_check_res', 'err', 'Указанный почтовый ящик не выглядит корректным. Его использование невозможно.');
      return false;
    }

    ipc_send_request ('/', 'ipc=check_email&skipId=<?= user_id (); ?>&email='+email, update_email_check);
  }
</script>

<div id="navigator">Мой профиль >> Дополнительная информация</div>
${information}
<div class="form">
  <div class="content">
    <?php
    $profile_menu->Draw();
    $info_menu->Draw();
    $f->Draw();
    ?>
  </div>
</div>
