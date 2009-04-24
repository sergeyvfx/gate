<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Password restoration form
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
?>
<div id="navigator"><a href="<?=config_get ('document-root');?>/login">Вход в систему</a>Восстановление пароля</div>
${information}
<script language="JavaScript" type="text/JavaScript">
function check_passwd () {
  var passwd  = getElementById ('passwd').value;
  var confirm = getElementById ('passwd_confirm').value;
  var widget  = getElementById ('passwd_msg');

  if (passwd == '' && confirm == '') {
    widget.innerHTML = '';
    return;
  }

  if (passwd == confirm)
    widget.innerHTML='<span style="color: #006000">Успешное подтверждение пароля</span>'; else
    widget.innerHTML='<span style="color: #600000">Ошибка подтверждения пароля</span>';
}

function check () {
  var login = getElementById ('login').value;

  if (!isalphanum (login) || qtrim (login) == '') {
    alert ('Логин пользователя может состоять лишь из букв латинского алфавита и цифр и не может быть пустым.');
    return false;
  }

  if (qtrim (login).length><?=opt_get ('max_user_login_len');?>) {
    alert ('Логин пользователя может содержать не более 14 символов.');
    return false;
  }

  if (!check_email (getElementById ('email').value)) {
    alert ('Адрес электронной не является корректным.');
    return false;
  }

  return true;
}
</script>
<?php
  global $redirect, $action, $login, $email;

  function send () {
    global $keystring, $login, $email;
    $hash = md5 ('#RANDOM_PREFIX#'.mtime ().'#RANDOM_SEPARATOR#'.$login.'#WITH#'.$email.'#RANDOM_SUFFIX#');

    if ($_SESSION['CAPTCHA_Keystring'] == '' || strtolower ($keystring) != $_SESSION['CAPTCHA_Keystring']) {
      add_info ('Вы не прошли тест Тьюринга на подтверждение того, что вы не бот.');
      return false;
    }

    $r = db_row_value ('user', "(`login` =\"$login\") AND (`email`=\"$email\") AND (`authorized`=1)");
    if ($r['id'] == '') {
      add_info ('Неверное сочетание login <-> email');
      return false;
    }
    $s = unserialize ($r['settings']);

    if ($s['restore_timestamp'] && time () - $s['restore_timestamp'] < config_get ('restore-timeout')) {
      add_info ('Вы не можете просить восстановку пароля так часто');
      return false;
    }

    $s['restore_hash'] = $hash;
    $s['restore_timestamp'] = time ();

    db_update ('user', array ('settings'=>db_string (serialize ($s))), '`id`='.$r['id']);

    $link = config_get ('http-document-root').'/login/restore/confirm/?id='.$r['id'].'&hash='.$hash;
    sendmail_tpl (stripslashes ($email), 'Восстановление пароля в системе '.config_get ('site-name'), 'restore', array ('login'=>stripslashes ($login), 'email'=>stripslashes ($email), 'link'=>$link));

    return true;
  }

  $f = new CVCForm (); 
  $f->Init ('', 'action=.?action\=send'.(($redirect!='')?('&redirect\='.prepare_arg ($redirect)):('')).';method=POST;add_check_func=check;caption=Послать заявку на восстановление;backlink='.prepare_arg ($redirect));

  $rn = new CVCCaptcha ();
  $rn->Init ();

  $f->AppendCustomField    (array ('src'=>'<table class="clear" width="100%"><tr><td width="30%">Код с картинки</td><td style="padding: 0 2px;"><div>'.$rn->OuterHTML ().'</div><input type="text" class="txt block" name="keystring" value=""></td></tr></table>'));
  $f->AppendCustomField    (array ('src'=>'<table class="clear" width="100%"><tr><td width="30%">Логин</td><td style="padding: 0 2px;"><input type="text" class="txt block" id="login" name="login" value="'.htmlspecialchars (stripslashes ($login)).'"></td></tr></table>'));
  $f->AppendCustomField    (array ('src'=>'<table class="clear" width="100%"><tr><td width="30%">E-Mail</td><td style="padding: 0 2px;"><input type="text" class="txt block" id="email" name="email" value="'.htmlspecialchars (stripslashes ($email)).'"></td></tr></table>'));

  if ($action == 'send') {
    if (!send ()) {
      formo ('title=Форма восстановления пароля');
      $f->Draw ();
      formc ();
    } else {
      add_info ('Письмо с подробной информации о дальнейших действиях для смены пароля было выслано по электронному адресу '.$email.
        (($redirect!='')?('<br><br><a href="'.htmlspecialchars ($redirect).'">Вернуться в предыдущий раздел</a>'):('')));
    }
  } else {
    formo ('title=Форма восстановления пароля');
    $f->Draw ();
    formc ();
  }
?>
