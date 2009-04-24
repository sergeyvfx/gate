<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Registration conformation page
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
<div id="navigator"><a href="<?=config_get ('document-root')?>/login">Вход в систему</a><a href="<?=config_get ('document-root')?>/login/registration">Регистрация</a>Активация пользователя</div>
${information}
<?php
  global $id, $hash;

  if (!isset ($id) || !isnumber ($id) || !isset ($hash)) {
    add_info ('Пропущен обязательный параметр.');
  } else {
    $r = db_row_value ('user', '`id`='.$id);
    if ($r['authorized']) {
      add_info ('Ошибка активации пользователя.');
    } else if (md5 ($r['login'].'##VERY_RANDOM_SEED##'.$r['email'].'##'.$r['id'])!=$hash) {
        add_info ('Ошибка активации пользователя.');
    } else {
      add_info ('Пользователь успешно активирован. Вход в систему с логином '.$r['login'].' разрешен.');
      db_update ('user', array ('authorized' => 1));
    }
  }
?>
