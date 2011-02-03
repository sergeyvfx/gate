<?php
/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Registration form
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
<div id="navigator"><a href="<?= config_get('document-root') ?>/login">Вход в систему</a>Регистрация</div>
${information}
<script language="JavaScript" type="text/JavaScript">
  function check_passwd () {
    var passwd  = getElementById ('passwd').value;
    var confirm = getElementById ('passwd_confirm').value;
    var widget   = getElementById ('passwd_msg');

    if (passwd == '' && confirm == '') {
      widget.innerHTML='<span style="color: #600000">Пароль не может быть пустым</span>';
      return;
    }

    if (passwd == confirm && !passwd == '')
      widget.innerHTML='<span style="color: #006000">Успешное подтверждение пароля</span>'; else
        widget.innerHTML='<span style="color: #600000">Ошибка подтверждения пароля</span>';
  }

  function check () {
    var login = getElementById ('login').value;
    var surname = getElementById('surname').value;
    var name = getElementById ('name').value;
    var patronymic = getElementById('patronymic').value;
    var passwd = getElementById ('passwd').value;
    var passwd_confirm = getElementById ('passwd_confirm').value;
    var agree  = getElementById ('agree');

    if (qtrim (surname) == '') {
      alert ('Фамилия создаваемого пользователя не может быть пустой.');
      return false;
    }

    if (qtrim (name)=='') {
      alert ('Имя создаваемого пользователя не может быть пустым.');
      return false;
    }

    if (qtrim (patronymic)=='') {
      alert ('Отчество создаваемого пользователя не может быть пустым.');
      return false;
    }

    if (qtrim (surname).length > <?= opt_get('max_user_surname_len'); ?>) {
      alert ('Фамилия создаваемого пользователя может содержать не более <?= opt_get('max_user_surname_len'); ?> символов.');
      return false;
    }

    if (qtrim (name).length > <?= opt_get('max_user_name_len'); ?>) {
      alert ('Имя создаваемого пользователя может содержать не более <?= opt_get('max_user_name_len'); ?> символов.');
      return false;
    }

    if (qtrim (patronymic).length > <?= opt_get('max_user_patronymic_len'); ?>) {
      alert ('Отчество создаваемого пользователя может содержать не более <?= opt_get('max_user_patronymic_len'); ?> символов.');
      return false;
    }

    if (!isalphanum (login) || qtrim (login) == '') {
      alert ('Логин создаваемого пользователя может состоять лишь из букв латинского алфавита и цифр и не может быть пустым.');
      return false;
    }

    if (qtrim (login).length > <?= opt_get('max_user_login_len'); ?>) {
      alert ('Логин создаваемого пользователя может содержать не более <?= opt_get('max_user_login_len'); ?> символов.');
      return false;
    }

    if (!check_email (getElementById ('email').value)) {
      alert ('Адрес электронной почты не является корректным.');
      return false;
    }

    if (!check_phone (getElementById('phone').value)) {
      alert ('Указанный телефон не является корректным.');
      return false;
    }

    if (passwd != passwd_confirm) {
      alert ('Ошибка подтверждения пароля.');
      return false;
    }

    if (passwd.length > <?= opt_get('max_user_passwd_len'); ?>) {
      alert ('Пароль создаваемого пользователя может содержать не более <?= opt_get('max_user_passwd_len'); ?> символов.');
      return false;
    }

    if (!agree.checked) {
      alert ('Вы не согласны с правилами этого ресурса, так как же мы Вас здесь зарегестрируем?');
      return false;
    }

    return true;
  }

  function update_login_check (http_request) {
    if (http_request.readyState == 4) {
      if (http_request.responseText == '+OK')
        show_msg ('login_check_res', 'ok', 'Данный логин пользователя является корректным и Вы можете его использовать.'); else
          show_msg ('login_check_res', 'err', 'Данный логин уже используется и Вы не можете его использовать.');
    }
  }

  function update_email_check (http_request) {
    if (http_request.readyState == 4) {
      if (http_request.responseText == '+OK')
        show_msg ('email_check_res', 'ok', 'Данный адрес электронной почты пользователя является корректным и Вы можете его использовать.'); else
          show_msg ('email_check_res', 'err', 'Вы не можете использовать этот адрес электронной почты, так как он уже используется.');
    }
  }

  function check_login () {
    var login = getElementById ('login').value;
    
    if (!isalphanum (login) || qtrim (login) == '') {
      show_msg ('login_check_res', 'err', 'Логин создаваемого пользователя может состоять лишь из букв латинского алфавита и цифр и не может быть пустым.');
      return false;
    }

    if (qtrim (login).length > <?= opt_get('max_user_login_len'); ?>) {
      show_msg ('login_check_res', 'err', 'Логин пользователя может содержать не более 14 символов.');
      return false;
    }

    ipc_send_request ('/', 'ipc=check_login&login='+login, update_login_check);
  }

  function check_frm_email () {
    var email = getElementById ('email').value;

    if (!check_email (email)) {
      show_msg ('email_check_res', 'err', 'Указанный почтовый ящик не выглядит корректным. Его использование невозможно.');
      return false;
    }

    ipc_send_request ('/', 'ipc=check_email&email='+email, update_email_check);
  }
</script>

<?php
global $redirect, $action, $surname, $name, $patronymic, $login, $email, $phone, $passwd;

function register() {
  global $agree, $email, $keystring;

  // FIXME  проверка капчи не работает. Разобраться почему
//  if ($_SESSION['CAPTCHA_Keystring'] == '' || strtolower($keystring) != $_SESSION['CAPTCHA_Keystring']) {
//    add_info('Вы не прошли тест Тьюринга на подтверждение того, что вы не бот.');
//    return false;
//  }

  if ($email == config_get('null-email')) {
    add_info('Недопустимый адрес электронной почты.');
    return false;
  }

  if (!$agree) {
    add_info('Вы не согласны с правилами этого ресурса, так как же мы Вас здесь зарегестрируем?');
    return false;
  }

// FIXME Раскомментировать когда поправим багу с отправкой e-mail.
//  return user_create_received(false);
  return user_create_received(true);
}

$f = new CVCForm ();
$f->Init('', 'action=.?action\=register' . (($redirect != '') ? ('&redirect\=' . prepare_arg($redirect)) : ('')) . ';method=POST;add_check_func=check;caption=Зарегистрироваться;backlink=' . prepare_arg($redirect));

$rn = new CVCCaptcha ();
$rn->Init();

// Fields
// FIXME Почему не используются поля специализироваанного типа?
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Фамилия</td><td style="padding: 0 2px;"><input type="text" class="txt block" id="surname" name="surname" value="' . htmlspecialchars(stripslashes($surname)) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Имя</td><td style="padding: 0 2px;"><input type="text" class="txt block" id="name" name="name" value="' . htmlspecialchars(stripslashes($name)) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Отчество</td><td style="padding: 0 2px;"><input type="text" class="txt block" id="patronymic" name="patronymic" value="' . htmlspecialchars(stripslashes($patronymic)) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Логин</td><td style="padding: 0 2px;"><input type="text" class="txt block" id="login" onBlur="check_login ();" name="login" value="' . htmlspecialchars(stripslashes($login)) . '"></td></tr>' .
    '</table>' . '<div id="login_check_res" style="display: none;"></div>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">E-Mail</td><td style="padding: 0 2px;"><input type="text" class="txt block" id="email" onBlur="check_frm_email ();" name="email" value="' . htmlspecialchars(stripslashes($email)) . '"></td></tr></table>' .
    '<div id="email_check_res" style="display: none;"></div>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Телефон</td><td style="padding: 0 2px;"><input type="text" class="txt block" id="phone" name="phone" value="' . htmlspecialchars(stripslashes($phone)) . '"></td></tr>'.
    '<tr><td><i>Например: +79091234567</i></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Пароль</td><td style="padding: 2px;"><input type="password" class="txt block" id="passwd" name="passwd"></td></tr>' .
    '<tr><td>Подтверждение</td><td style="padding: 2px;"><input type="password" class="txt block" id="passwd_confirm" name="passwd_confirm"  onBlur="check_passwd ();"><div id="passwd_msg"></div></td></tr>' .
    '</table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Код с картинки</td><td style="padding: 0 2px;"><div>' . $rn->OuterHTML() . '</div><input type="text" class="txt block" name="keystring" value=""></td></tr></table>'));
$f->AppendCUstomField(array('src' => '<center><input type="checkbox" class="cb" value="1" name="agree" id="agree">Я согласен с <a href="' . config_get('document-root') . '/articles/rules" target="blank">правилами</a> этого ресурса</center>'));

if ($action == 'register') {
  if (!register ()) {
    formo('title=Форма регистрации пользователя');
    $f->Draw();
    formc ();
  } else {
    $id = user_id_by_login(stripslashes($login));
    $reglink = config_get('http-document-root') . '/login/registration/confirm/?id=' . $id . '&hash=' . md5(stripslashes($login) . '##VERY_RANDOM_SEED##' . stripslashes($email) . '##' . $id);
    //FIXME Посмотреть что можно сделать чтобы отправка email была с внешнего сервера
    sendmail_tpl(stripslashes($email), 'Регистрация в системе ' . config_get('site-name'), 'registration', array('login' => stripslashes($login),
        'passwd' => stripslashes($passwd), 'reglink' => $reglink));
    add_info('Новый пользователь был успешно добавлен в базу, но в данный момент он неактивирован и вход в систему от его имени пока невозможен. ' .
            'Письмо с подробной информации об активации пользователя было выслано по электронному адресу ' . $email .
            (($redirect != '') ? ('<br><br><a href="' . htmlspecialchars($redirect) . '">Вернуться в предыдущий раздел</a>') : ('')));
  }
} else {
  formo('title=Форма регистрации пользователя');
  $f->Draw();
  formc ();
}
?>
