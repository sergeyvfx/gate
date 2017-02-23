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

  $max_login_len = opt_get('max_login_len');
  $max_surname_len = opt_get('max_surname_len');
  $max_name_len = opt_get('max_name_len');
  $max_patronymic_len = opt_get('max_patronymic_len');
  $max_passwd_len = opt_get('max_passwd_len');
?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<div id="snavigator"><a href="<?=config_get ('document-root')?>/login">Вход в систему</a>Регистрация</div>
${information}
<script language="JavaScript" type="text/JavaScript">
  function check_passwd () {
    var passwd  = getElementById ('passwd').value;
    var confirm = getElementById ('passwd_confirm').value;
    var widget   = getElementById ('passwd_msg');

    if (passwd == '' && confirm == '') {
    widget.innerHTML = '';
      return;
    }

  if (passwd != confirm)
      widget.innerHTML='<span style="color: #600000">Ошибка подтверждения пароля</span>';
      else widget.innerHTML='';
          //widget.innerHTML='<span style="color: #006000">Успешное подтверждение пароля</span>';
        
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

    if (qtrim (surname).length > <?=$max_surname_len;?>) {
      alert ('Фамилия создаваемого пользователя может содержать не более <?=$max_surname_len;?> символов.');
      return false;
    }

    if (qtrim (name).length > <?=$max_name_len; ?>) {
      alert ('Имя создаваемого пользователя может содержать не более <?=$max_name_len;?> символов.');
      return false;
    }

    if (qtrim (patronymic).length > <?=$max_patronymic_len;?>) {
      alert ('Отчество создаваемого пользователя может содержать не более <?=$max_patronymic_len;?> символов.');
      return false;
    }

    if (!isalphanum (login) || qtrim (login) == '') {
      alert ('Логин создаваемого пользователя может состоять лишь из букв латинского алфавита и цифр и не может быть пустым.');
      return false;
    }

    if (qtrim (login).length > <?=$max_login_len;?>) {
      alert ('Логин создаваемого пользователя может содержать не более <?=$max_login_len;?> символов.');
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

    if (qtrim(passwd)==''){
        alert ('Пароль не может быть пустым');
        return false;
    }

    if (passwd != passwd_confirm) {
      alert ('Ошибка подтверждения пароля.');
      return false;
    }

  if (passwd.length > <?=$max_passwd_len;?>) {
    alert ('Пароль создаваемого пользователя может содержать не более <?=$max_passwd_len;?> символов.');
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
          hide_msg('login_check_res');
      else
          show_msg ('login_check_res', 'err', 'Данный логин уже используется и Вы не можете его использовать.');
    }
  }

  function update_email_check (http_request) {
    if (http_request.readyState == 4) {
      if (http_request.responseText == '+OK')
          hide_msg('email_check_res');
      else
        show_msg ('email_check_res', 'err', 'Вы не можете использовать этот адрес электронной почты, так как он уже используется.');
    }
  }

  function check_login () {
    var login = getElementById ('login').value;
    if (!isalphanum (login) || qtrim (login) == '') {
      show_msg ('login_check_res', 'err', 'Логин создаваемого пользователя может состоять лишь из букв латинского алфавита и цифр и не может быть пустым.');
      return false;
    }

  if (qtrim (login).length > <?=$max_login_len;?>) {
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

  function check_frm_phone () {
    var phone = getElementById ('phone').value;

    if (!check_phone (phone)) {
      show_msg ('phone_check_res', 'err', 'Указанный телефон не выглядит корректным.');
      return false;
    }

    hide_msg('phone_check_res');
  }

  function check_frm_surname () {
    var surname = getElementById ('surname').value;

    if (qtrim(surname) == '') {
      show_msg ('surname_check_res', 'err', 'Это поле обязательно для заполнения');
      return false;
    }

    hide_msg('surname_check_res');
  }

  function check_frm_name () {
    var name = getElementById ('name').value;

    if (qtrim(name)=='') {
      show_msg ('name_check_res', 'err', 'Это поле обязательно для заполнения');
      return false;
    }

    hide_msg('name_check_res');
  }
</script>

<?php
  global $redirect, $action, $surname, $name, $patronymic, $login, $email, $phone, $passwd;

  function register () {
    global $agree, $email, $keystring;

    $privatekey = '6LfamhYUAAAAAJTgOpDhdkP4a6FD939yiQbFswPJ'; //config_get('recaptcha-private-key');
    $response = $_POST["g-recaptcha-response"];
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => $privatekey,
        'response' => $response
    );
    $options = array(
        'http' => array (
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captcha_success=json_decode($verify);
    
    if ($captcha_success->success == false) {
        add_info('Вы не прошли тест Тьюринга на подтверждение того, что вы не бот.');
        return false;
    } else if ($captcha_success->success == true) {

        if ($email == config_get('null-email')) {
          add_info('Недопустимый адрес электронной почты.');
          return false;
        }

        if (!$agree) {
          add_info('Вы не согласны с правилами этого ресурса, так как же мы Вас здесь зарегестрируем?');
          return false;
        }

        //return user_create_received(false);
        return user_create_received(true);
    }
}

$f = new CVCForm ();
$f->Init('', 'action=.?action\=register' . (($redirect != '') ? ('&redirect\=' . prepare_arg($redirect)) : ('')) . ';method=POST;add_check_func=check;caption=Зарегистрироваться;backlink=' . prepare_arg($redirect));

// Fields
// FIXME Почему не используются поля специализироваанного типа?
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Фамилия: <span class="error">*</div></td><td style="padding: 0 2px;"><input type="text" class="txt block" id="surname" name="surname" onBlur="check_frm_surname ();" value="' . htmlspecialchars(stripslashes($surname)) . '"></td></tr>'.
    '</table><div id="surname_check_res" style="display: none;"></div>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Имя: <span class="error">*</div></td><td style="padding: 0 2px;"><input type="text" class="txt block" id="name" name="name" onBlur="check_frm_name();" value="' . htmlspecialchars(stripslashes($name)) . '"></td></tr>'.
    '</table><div id="name_check_res" style="display: none;"></div>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Отчество: </td><td style="padding: 0 2px;"><input type="text" class="txt block" id="patronymic" name="patronymic" value="' . htmlspecialchars(stripslashes($patronymic)) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Логин: <span class="error">*</div></td><td style="padding: 0 2px;"><input type="text" class="txt block" id="login" onBlur="check_login ();" name="login" value="' . htmlspecialchars(stripslashes($login)) . '"></td></tr>' .
    '</table>' . '<div id="login_check_res" style="display: none;"></div>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">E-Mail: <span class="error">*</span></td><td style="padding: 0 2px;"><input type="text" class="txt block" id="email" onBlur="check_frm_email ();" name="email" value="' . htmlspecialchars(stripslashes($email)) . '"></td></tr></table>' .
    '<div id="email_check_res" style="display: none;"></div>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Телефон:</td><td style="padding: 0 2px;"><input type="text" class="txt block" id="phone" name="phone" value="' . htmlspecialchars(stripslashes($phone)) . '"></td></tr>'.
    '<tr><td><i>Например: +79091234567</i></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Пароль: <span class="error">*</span></td><td style="padding: 2px;"><input type="password" class="txt block" id="passwd" name="passwd"></td></tr>' .
    '<tr><td>Подтверждение пароля: <span class="error">*</span></td><td style="padding: 2px;"><input type="password" class="txt block" id="passwd_confirm" name="passwd_confirm"  onBlur="check_passwd ();"><div id="passwd_msg"></div></td></tr>' .
    '</table>'));
$f->AppendCustomField(array ('src'=>'<table class="clear" width="100%"><tr><td align="center" style="padding: 0 2px;"><div class="g-recaptcha" data-sitekey="6LfamhYUAAAAAK4OC9pKnyrj3y5at5dnx7_aoMO3"></div></td></tr></table>'));
$f->AppendCUstomField(array('src' => '<center><input type="checkbox" class="cb" value="1" name="agree" id="agree">Я согласен с <a href="' . config_get('document-root') . '/articles/rules" target="blank">правилами</a> этого ресурса <span class="error">*</span></center>'));

if ($action == 'register') {
  if (!register ()) {
    formo('title=Форма регистрации пользователя');
    $f->Draw();
    formc ();
  } else {
    redirect(config_get('document-root') . '/login?firstlogin=1&username=' . $login);
    /*$id = user_id_by_login(stripslashes($login));
    $reglink = config_get('http-document-root') . '/login/registration/confirm/?id=' . $id . '&hash=' . md5(stripslashes($login) . '##VERY_RANDOM_SEED##' . stripslashes($email) . '##' . $id);
    $restorelink = config_get('http-document-root') . '/login/restore';
    $contestname = 'Тризформашка';
    sendmail_tpl(stripslashes($email), 'Регистрация в конкурсе Тризформашка', 'registration', array('login' => stripslashes($login),
        'passwd' => stripslashes($passwd), 'reglink' => $reglink, 'restorelink' => $restorelink, 'contestname' => $contestname));
    add_info('Новый пользователь был успешно добавлен в базу, но в данный момент он неактивирован и вход в систему от его имени пока невозможен. ' .
            'Письмо с подробной информации об активации пользователя было выслано по электронному адресу ' . $email .
            (($redirect != '') ? ('<br><br><a href="' . htmlspecialchars($redirect) . '">Вернуться в предыдущий раздел</a>') : ('')));
     */
  }
} else {
  formo('title=Форма регистрации пользователя');
  $f->Draw();
  formc ();
}
?>
