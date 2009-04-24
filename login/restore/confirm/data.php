<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Confirm password restoration form
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
<div id="navigator"><a href="<?=config_get ('document-root')?>/login">Вход в систему</a>Восстановление пароля</div>
${information}
<?php
  global $id, $hash;

  if (!isset ($id) || !isnumber ($id) || !isset ($hash)) {
    add_info ('Пропущен обязательный параметр.');
  } else {
    $r = db_row_value ('user', "(`id`=\"$id\") AND (`authorized`=1)");
    if ($r['id'] == '') {
      add_info ('Ошибка восстановления пароля.');
    } else {
      global $hash;
      $s = unserialize ($r['settings']);
      if ($s['restore_hash'] != $hash) {
        add_info ('Ошибка восстановления пароля.');
      } else {
?>
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
  var passwd = getElementById ('passwd').value;
  var passwd_confirm = getElementById ('passwd_confirm').value;

  if (passwd != passwd_confirm) {
    alert ('Ошибка подтверждения пароля.');
    return false;
  }

  if (passwd.length > <?=opt_get ('max_user_passwd_len');?>) {
    alert ('Пароль создаваемого пользователя может содержать не более <?=opt_get ('max_user_passwd_len');?> символов.');
    return false;
  }

  return true;
}
</script>
<?php
        global $action;
        $f = new CVCForm (); 
        $f->Init ('', 'action=.?id\='.$id.'&hash\='.$hash.'&action\=save;method=POST;add_check_func=check;caption=Сменить пароль;');
        $f->AppendCustomField    (array ('src'=>'<table class="clear" width="100%"><tr><td width="30%">Пароль</td><td style="padding: 2px;"><input type="password" class="txt block" id="passwd" name="passwd" onkeyup="check_passwd ();" onchange="check_passwd ();"></td></tr>'.
         '<tr><td>Подтверждение</td><td style="padding: 2px;"><input type="password" class="txt block" id="passwd_confirm" name="passwd_confirm"  onkeyup="check_passwd ();" onchange="check_passwd ();"><div id="passwd_msg"></div></td></tr>'.
         '</table>'));

        $draw = true;
        if ($action == 'save') {
          global $passwd, $passwd_confirm;
          $passwd = stripslashes ($passwd);
          $passwd_confirm = stripslashes ($passwd_confirm);
          if ($passwd != $passwd_confirm) {
            add_info ('Ошибка подьверждения пароля.');
          } else {
            $draw = false;
            unset ($s['restore_hash']);
            unset ($s['restore_timestamp']);
            db_update ('user', array ('password'=>'MD5('.db_string (user_password_hash ($r['login'], $passwd)).')', 'settings'=>db_string (serialize ($s))), '`id`='.$r['id']);
            add_info ('Ваш пароль был успешно поменян.');
          }
        }

        if ($draw) {
          formo ('title=Форма смены пароля');
          $f->Draw ();
          formc ();
        }
      }
    }
  }
?>
