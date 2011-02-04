<?php

/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Users manipulating stuff
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */
global $IFACE;

if ($IFACE != "SPAWNING NEW IFACE" || $_GET['IFACE'] != '') {
  print ('HACKERS?');
  die;
}

if ($_sec_user_included_ != '#sec_user_Included#') {
  $_sec_user_included_ = '#sec_user_Included#';
  $user_infos = array();

  function user_delete_unwanted() {
    db_delete('user', '(`authorized`=0) AND (`timestamp`<=' .
            time () - config_get('confirm_authorize_timeout') . ')');

    $q = db_select('user', array('id'), '(`last_act`>0) AND (`last_act`<' .
                    (time () - config_get('user-lifetime')) . ') AND (`id`!=1)');

    while ($r = db_row($q)) {
      user_delete($r['id']);
    }
  }

  function user_logout() {
    global $user_login, $user_password;
    $user_login = $user_password = '';
    session_unregister('user_id');
    session_unregister('user_login');
    session_unregister('user_password');
    session_unregister('user_access');

    // But how we can optimize this?
    session_unregister('WT_contest_id');
    hook_call('CORE.Security.OnUserLogout', $id);
  }

  function user_password_hash($login, $password) {
    return strtolower($login) . '#RANDOM_SEED#' . $password;
  }

  function user_authorized() {
    global $user_login, $user_password, $user_authorized;

    if (isset($user_authorized)) {
      return $user_authorized;
    }

    db_query('SELECT * FROM `user` WHERE (`authorized`=1) AND (`login`="' .
            $user_login . '") AND (`password`=MD5("' .
            addslashes(user_password_hash($user_login, $user_password)) .
            '"))');

    $user_authorized = db_affected () == 1;

    return $user_authorized;
  }

  function user_authorize($login, $password) {
    global $user_id, $user_login, $user_password, $user_access;
    $r = db_row(db_query('SELECT * FROM `user` WHERE (`authorized`=1) ' .
                            'AND (`login`="' . addslashes($login) . '") AND ' .
                            '(`password`=MD5("' .
                            addslashes(user_password_hash($login, $password)) .
                            '"))'));

    $user_id = $r['id'];
    session_unregister('user_id');
    session_unregister('user_login');
    session_unregister('user_password');
    session_unregister('user_access');

    // But how we can optimize this?
    session_unregister('WT_contest_id');

    if ($user_id != '') {
      $user_login = $login;
      $user_password = $password;
      $user_access = $r['access'];
      session_register('user_id');
      session_register('user_login');
      session_register('user_password');
      session_register('user_access');

      db_update('user', array('last_act' => time ()), '`id`=' . $user_id);
    }
    return $user_id != '';
  }

  function user_registered_with_field($field, $value, $skipId = -1) {
    return db_count('user', "`$field`=\"" . addslashes($value) .
            '" AND `id`<>' . $skipId);
  }

  function user_registered_with_login($login, $skipId = -1) {
    return user_registered_with_field('login', $login, $skipId);
  }

  function user_registered_with_email($email, $skipId = -1) {
    if ($email == config_get('null-email')) {
      return false;
    }

    return user_registered_with_field('email', $email, $skipId);
  }

  /**
   * Проверка корректности заполнения полей
   * @param <type> $surname
   * @param <type> $name
   * @param <type> $patronymic
   * @param <type> $login
   * @param <type> $email
   * @param <type> $phone
   * @param <type> $passwd
   * @param <type> $check_login
   * @param <type> $skipId
   * @return <type>
   */
  function user_check_fields($surname, $name, $patronymic, $login, $email, $phone, $passwd, $check_login = true, $skipId = -1) {
    // Get settings
    $max_login_len = opt_get('max_login_len');
    $max_surname_len = opt_get('max_surname_len');
    $max_name_len = opt_get('max_name_len');
    $max_patronymic_len = opt_get('max_patronymic_len');
    $max_passwd_len = opt_get('max_passwd_len');

    if ($check_login && !isalphanum($login)) {
      add_info('Логин пользователя может состоять лишь из латинских букв и цифр.');
      return false;
    }

    if (mb_strlen($login) > $max_login_len) {
      add_info('Логин пользователя может содержать не более ' .
              $max_login_len . ' символов.');
      return false;
    }

    if (mb_strlen($surname) > $max_surname_len) {
      add_info('Фамилия пользователя может содержать не более ' .
              $max_surname_len . ' символов.');
      return false;
    }

    if (mb_strlen($name) > $max_name_len) {
      add_info('Имя пользователя может содержать не более ' .
              $max_name_len . ' символов.');
      return false;
    }

    if (mb_strlen($patronymic) > $max_patronymic_len) {
      add_info('Отчество пользователя может содержать не более ' .
              $max_patronymic_len . ' символов.');
      return false;
    }

    if (mb_strlen($passwd) > $max_passwd_len) {
      add_info('Пароль пользователя может содержать не более ' .
              $max_passwd_len . ' символов.');
      return false;
    }

    if (!check_email($email)) {
      add_info('Адрес электронной почты не выглядит корректным.');
      return false;
    }

    if (user_registered_with_email($email, $skipId)) {
      add_info('Этот адрес электронной почты уже используется. ' .
              'Пожалуйста, укажите другой.');
      return false;
    }

    if ($check_login && user_registered_with_login($login, $skipId)) {
      add_info('Этот логин уже используется. Пожалуйста, укажите другой.');
      return false;
    }

    return true;
  }

  /**
   * Создание нового пользователя
   * @param <type> $surname - Фамилия пользователя
   * @param <type> $name - Имя пользователя
   * @param <type> $patronymic - Отчество пользователя
   * @param <type> $login - Логин пользователя
   * @param <type> $email - Адрес электронной почты
   * @param <type> $phone - Телефон
   * @param <type> $passwd - Пароль
   * @param <type> $authorized
   * @param <type> $access - Уровень доступа
   * @param <type> $groups - Группы
   * @return <type> Возвращает true если пользователь успешно добавлен; false в противном случае.
   */
  function user_create($surname, $name, $patronymic, $login, $email, $phone, $passwd, $authorized = false, $access = 1, $groups = array()) {
    // Check da values
    if (!user_check_fields($surname, $name, $patronymic, $login, $email, $phone, $passwd)) {
      return false;
    }

    // Checking has been passed
    db_insert('user', array('surname' => '"' . htmlspecialchars(addslashes($surname)) . '"',
        'name' => '"' . htmlspecialchars(addslashes($name)) . '"',
        'patronymic' => '"' . htmlspecialchars(addslashes($patronymic)) . '"',
        'login' => '"' . addslashes($login) . '"',
        'email' => '"' . addslashes($email) . '"',
        'phone' => '"' . addslashes($phone) . '"',
        'password' => 'MD5("' . addslashes(user_password_hash($login, $passwd)) . '")',
        'access' => $access,
        'authorized' => (($authorized) ? ('1') : ('0')),
        'settings' => '""',
        'timestamp' => time ()));

    $uid = db_last_insert ();
    user_add_to_default_groups($uid);
    user_add_to_groups($uid, $groups);
    return true;
  }

  function user_create_received($authorized = true) {
    // Get post data
    $surname = stripslashes(trim($_POST['surname']));
    $name = stripslashes(trim($_POST['name']));
    $patronymic = stripslashes(trim($_POST['patronymic']));
    $login = stripslashes(trim($_POST['login']));
    $email = stripslashes($_POST['email']);
    $phone = stripslashes($_POST['phone']);
    $passwd = stripslashes($_POST['passwd']);
    $passwd_confirm = stripslashes($_POST['passwd_confirm']);

    if ($passwd != $passwd_confirm) {
      add_info('Ошибка подтверждения пароля.');
      return false;
    }

    $groups = new CVCAppendingList ();
    $groups->Init('groups', '');
    $groups->ReceiveItemsUsed();

    $acc = $_POST['acgroup'];
    if ($acc == '') {
      $acc = 0;
    }

    if (user_create($surname, $name, $patronymic, $login, $email, $phone,
                    $passwd, $authorized, $acc, $groups->GetItemsUsed())) {
      $_POST = array();
      return true;
    }

    return false;
  }

  function user_update($id, $surname, $name, $patronymic, $email, $phone, $access, $groups = array(), $passwd = '') {
     if (!user_check_fields($surname, $name, $patronymic, CORRECT_LOGIN, $email, $phone, $passwd, false, $id)) {
      return false;
    }

    $info = user_get_by_id($id);
    $name = htmlspecialchars(addslashes($name));
    $email = addslashes($email);

    $update = array('surname' => '"' . htmlspecialchars(addslashes($surname)) . '"',
        'name' => '"' . htmlspecialchars(addslashes($name)) . '"',
        'patronymic' => '"' . htmlspecialchars(addslashes($patronymic)) . '"',
        'email' => '"' . addslashes($email) . '"',
        'phone' => '"' . addslashes($phone) . '"',
        'access' => '"' . addslashes($access) . '"');

    if ($passwd != '') {
      $update['password'] = 'MD5("' .
              addslashes(user_password_hash($info['login'], $passwd)) . '")';
    }

    db_update('user', $update, "`id`=$id");
    user_delete_from_unset_groups($id, $groups);
    user_add_to_groups($id, $groups);

    return true;
  }

  function user_update_received($id) {
    $surname = stripslashes(trim($_POST['surname']));
    $name = stripslashes(trim($_POST['name']));
    $patronymic = stripslashes(trim($_POST['patronymic']));
    $passwd = stripslashes($_POST['passwd']);
    $passwd_confirm = stripslashes($_POST['passwd_confirm']);
    $email = stripslashes($_POST['email']);
    $phone = stripslashes($_POST['phone']);
    $acgroup = stripslashes($_POST['acgroup']);

    if ($passwd != '' && $passwd != $passwd_confirm) {
      add_info('Ошибка подтверждения пароля.');
      return false;
    }

    $groups = new CVCAppendingList ();
    $groups->Init('groups');
    $groups->ReceiveItemsUsed();

    if (user_update($id, $surname, $name, $patronymic, $email, $phone,
            $acgroup, $groups->GetItemsUsed(), $passwd)) {
      $_POST = array();
    }
  }

  function user_is_system($id) {
    return $id == 1;
  }

  function user_authorized_list($gid = -1) {
    if ($gid == '') {
      $gid = -1;
    }

    if ($gid < 0) {
      return arr_from_query('SELECT * FROM `user` ORDER BY  `login`');
    }

    return arr_from_query('SELECT `user`.* FROM `user`, ' .
            '`usergroup` WHERE `user`.`id`=`usergroup`.`user_id` ' .
            ' AND `usergroup`.`group_id`=' . $gid . ' ORDER BY  `login`');
  }

  function user_add_to_groups($uid, $arr) {
    for ($i = 0; $i < count($arr); $i++) {
      if (trim($arr[$i]) != '') {
        user_add_to_group($uid, $arr[$i]);
      }
    }
  }

  function user_add_to_default_groups($uid) {
    $arr = group_default_list ();
    for ($i = 0; $i < count($arr); $i++) {
      user_add_to_group($uid, $arr[$i]['id']);
    }
  }

  function user_add_to_group($uid, $gid) {
    if (db_count('usergroup', "`user_id`=$uid AND `group_id`=$gid") > 0) {
      return false;
    }

    db_insert('usergroup', array('user_id' => $uid, 'group_id' => $gid));
    db_update('group', array('refcount' => '`refcount`+1'), '`id`=' . $gid);

    return true;
  }

  function user_delete_from_group($uid, $gid) {
    if (db_count('usergroup', "`user_id`=$uid AND `group_id`=$gid") == 0) {
      return false;
    }

    db_delete('usergroup', "`user_id`=$uid AND `group_id`=$gid");
    db_update('group', array('refcount' => '`refcount`-1'), '`id`=' . $gid);
  }

  function user_delete_from_groups($uid) {
    $q = db_select('usergroup', array('group_id'), '`user_id`=' . $uid);
    while ($r = db_row($q)) {
      user_delete_from_group($uid, $r['group_id']);
    }
  }

  function user_delete_from_unset_groups($uid, $groups) {
    $inGroups = user_get_groups($uid);
    $assoc = array();

    for ($i = 0; $i < count($groups); $i++) {
      $assoc[$groups[$i]] = true;
    }

    for ($i = 0; $i < count($inGroups); $i++) {
      if (!$assoc[$inGroups[$i]]) {
        user_delete_from_group($uid, $inGroups[$i]);
      }
    }
  }

  function user_delete($id) {
    if (user_is_system($id)) {
      add_info('Невозможно удалить этого пользователя, ' .
              'так как он является системным.');
      return false;
    }

    user_delete_from_groups($id);

    hook_call('CORE.Security.OnUserDelete', $id);

    return db_delete('user', 'id=' . $id);
  }

  function user_get_groups($id) {
    return arr_from_query('SELECT `group_id` FROM `usergroup` ' .
            'WHERE `user_id`=' . $id . '  GROUP BY `group_id`',
            'group_id');
  }

  function user_get_by_id($id) {
    return db_row_value('user', "`id`=$id");
  }

  function user_access() {
    global $user_access;

    if ($user_access == '') {
      return 0;
    }

    return $user_access;
  }

  function user_access_root() {
    global $user_access;
    return $user_access >= ACCESS_ROOT;
  }

  function user_id() {
    global $user_id;

    if ($user_id == '') {
      return -1;
    }

    return $user_id;
  }

  function user_login() {
    global $user_login;
    return $user_login;
  }

  function user_id_by_login($login) {
    return db_field_value('user', 'id', '`login`="' . addslashes($login) . '"');
  }

  function user_info_by_id($id, $cacheable = true) {
    global $user_infos;

    if ($cacheable && isset($user_infos[$id])) {
      return $user_infos[$id];
    }

    $user_infos[$id] = db_row_value('user', "`id`=$id");
    $user_infos[$id]['settings'] = unserialize($user_infos[$id]['settings']);
    return $user_infos[$id];
  }

  function user_generate_info_string($id, $cacheable=true) {
    $info = user_info_by_id($id, $cacheable);

    if ($info['id'] == '') {
      return 'anonumoys (Гость)';
    }

    return '<a href="' . config_get('document-root') .
    '/login/viewuser/?id=' . $info['id'] . '&redirect=' .
    get_redirection () . '">' . $info['login'] . '</a> (' . $info['name'] . ') ' .
    '&nbsp;(' . security_access_title($info['access']) . ')' .
    '&nbsp;&nbsp;&nbsp;E-Mail: <a href="mailto:' . $info['email'] . '">' .
    $info['email'] . '</a>';
  }

  function user_generate_short_info_string($id, $cacheable = true) {
    $info = user_info_by_id($id, $cacheable);
    if ($info['id'] == '') {
      return 'anonumoys (Гость)';
    }

    return '<a href="' . config_get('document-root') . '/login/viewuser/?id=' .
    $info['id'] . '&redirect=' . get_redirection () . '">' .
    $info['name'] . ' (<i>' . $info['login'] . '</i>)</a>';
  }

  function user_generate_viewlink($id) {
    return config_get('document-root') . '/login/viewuser/?id=' .
    $id . '&redirect=' . get_redirection ();
  }

}
?>
