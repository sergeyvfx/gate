<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF!='') {
    print ('HACKERS?');
    die;
  }

  global $id, $page;
  formo ('title=Редактирование пользователя;');

  $glist = security_groups ();
  $max_surname_len = opt_get ('max_surname_len');
  $max_name_len   = opt_get ('max_name_len');
  $max_patronymic_len = opt_get ('max_patronymic_len');
  $max_passwd_len = opt_get ('max_passwd_len');
  $user = user_get_by_id ($id);
?>
<script language="JavaScript" type="text/javascript">
  var gDesc = new Array ();
<?php
  foreach ($glist as $k=>$g) {
?>
  gDesc[<?=$g['access']?>]='<?=addslashes ($g['desc'])?>';
<?php
  }
?>
  function check_passwd () {
    var passwd  = getElementById ('passwd').value;
    var confirm = getElementById ('passwd_confirm').value;
    var widget   = getElementById ('passwd_msg');

    if (passwd == '' && confirm == '') {
      widget.innerHTML = '';
      return;
    }

    if (passwd == confirm)
      widget.innerHTML='<span style="color: #006000">Успешное подтверждение пароля</span>'; else
        widget.innerHTML='<span style="color: #600000">Ошибка подтверждения пароля</span>';
  }

  function check (frm) {
    var surname = getElementById('surname').value;
    var name   = getElementById ('name').value;
    var patronymic = getElementById('patronymic').value;
    var passwd = getElementById ('passwd').value;
    var passwd_confirm = getElementById ('passwd_confirm').value;

    if (qtrim (surname) == '') {
      alert ('Фамилия создаваемого пользователя не может быть пустой.');
      return false;
    }

    if (qtrim (surname).length > <?=$max_surname_len;?>) {
      alert ('Фамилия создаваемого пользователя может содержать не более <?=$max_surname_len;?> символов.');
      return false;
    }

    if (qtrim (name) == '') {
      alert ('Имя пользователя не может быть пустым.');
      return false;
    }

    if (qtrim (name).length > <?=$max_name_len;?>) {
      alert ('Имя пользователя может содержать не более <?=$max_name_len;?> символов.');
      return false;
    }

    if (qtrim (patronymic)=='') {
      alert ('Отчество создаваемого пользователя не может быть пустым.');
      return false;
    }

    if (qtrim (patronymic).length > <?=$max_patronymic_len;?>) {
      alert ('Отчество создаваемого пользователя может содержать не более <?=$max_patronymic_len;?> символов.');
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

    if (passwd.length > <?=$max_passwd_len;?>) {
      alert ('Пароль пользователя может содержать не более <?=$max_passwd_len;?> символов.');
      return false;
    }

    alist_prepare_post_data ('groups');
    frm.submit ();
  }

  function updateGDesc (sender) {
    getElementById ('gdesc').innerHTML=gDesc[sender.value];
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
    if (!check_email (email)) {
      show_msg ('email_check_res', 'err', 'Указанный почтовый ящик не выглядит корректным. Его использование невозможно.'); return false;}
    ipc_send_request ('/', 'ipc=check_email&email='+email+'&skipId=<?=$id;?>', update_email_check);
  }
</script>

<form action=".?action=save&id=<?=$id;?>&<?=get_filters ();?><?=(($page!='')?('&page='.$page):(''));?>" method="POST" onsubmit="check (this); return false;">
  Логин:
  <div><?=$user['login'];?></div>
  <div id="hr"></div>
  Фамилия:
  <input type="text" id="surname" name="surname" value="<?=htmlspecialchars ($user['surname']);?>" class="txt block"><div id="hr"></div>
  Имя:
  <input type="text" id="name" name="name" value="<?=htmlspecialchars ($user['name']);?>" class="txt block"><div id="hr"></div>
  Отчество:
  <input type="text" id="patronymic" name="patronymic" value="<?=htmlspecialchars ($user['patronymic']);?>" class="txt block"><div id="hr"></div>
  Пароль:
  <input type="password" id="passwd" name="passwd" value="" class="passwd block"><div id="hr"></div>
  Подтверждение пароля:
  <input type="password" onblur="check_passwd ();" id="passwd_confirm" name="passwd_confirm" value="" class="passwd block"><div id="passwd_msg"></div><div id="hr"></div>
  Адрес электронной почты<span> :: <a href="JavaScript:dn();" onclick="getElementById ('email').value='<?=config_get ('null-email');?>';" title="Пользователь, не имеющий собственной почты"><?=config_get ('null-email');?></a></span>
  <input type="text" onblur="check_frm_email ();" id="email" name="email" value="<?=htmlspecialchars ($user['email']);?>" class="txt block">
  <div id="email_check_res" style="display: none;"></div><div id="hr"></div>
  Телефон:
  <input type="text" id="phone" name="phone" value="<?=htmlspecialchars ($user['phone']);?>" class="txt block"><div id="hr"></div>
  Уровень доступа:
  <select class="block" onchange="updateGDesc (this);" name="acgroup">
<?php
  foreach ($glist as $k => $g) { ?>
    <option value="<?=$g['access']?>"<?=(($user['access']==$g['access'])?(' selected'):(''))?>><?=$g['title'];?></option>
<?php
  }
?>
  </select>
  <div id="gdesc" style="padding: 4px 10px;"><?=security_group_desc_by_access ($user['access']);?></div><div id="hr"></div>
  Является членом групп:
<?php
  $groups = new CVCAppendingList ();
  $groups->Init ('groups', 'height=48px;');

  $glist = group_list ();
  for ($i = 0; $i < count ($glist); $i++) {
    $g = $glist[$i];
    $groups->AppendItem ($g['name'], $g['id']);
  }

  $groups->SetItemsUsed (user_get_groups ($id));

  $groups->Draw ();
?>
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.?<?=get_filters ();?><?=(($page!='')?('&page='.$page):(''));?>');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<?php
  formc ();
?>
