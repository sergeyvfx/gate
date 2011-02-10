<?php

/**
 * Gate - Wiki engine and web-interface for WebTester Server
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

if ($_payment_included_ != '#payment_Included#') {
  $_payment_included_ = '#payment_Included#';

  function payment_list($responsible_id = -1) {
    if ($responsible_id == '') {
      $responsible_id = -1;
    }

    if ($responsible_id < 0) {
      return arr_from_query('SELECT * FROM `payment` ORDER BY `date`');
    }

    return arr_from_query('SELECT `payment`.* FROM `payment` ' .
            'WHERE `payment`.`responsible_id`=' . $responsible_id .
            ' ORDER BY `date`');
  }

  /**
   * Проверка корректности заполнения полей
   */
  function payment_check_fields($date, $cheque_number, $payer_full_name, $amount, $comment, $update = false, $id = -1) {
    if ($update) {
      $tc = teams_count_is_payment($id);
      $b = group_get_by_name("Бухгалтеры");
      if ($tc > 0 && !is_user_in_group(user_id(), $b['id'])) {
        add_info("Данный платеж не доступен для редактирования");
        return false;
      }
    }
    if ($date == '') {
      add_info("Поле \"Дата\" обязательно для заполнения");
      return false;
    }

    if ($cheque_number == '') {
      add_info("Поле \"Номер чека-ордера\" обязательно для заполнения");
      return false;
    }

    if ($payer_full_name == '') {
      add_info("Поле \"Полное имя плательщика\" обязательно для заполнения");
      return false;
    }

    if ($amount == '') {
      add_info("Поле \"Сумма платежа\" обязательно для заполнения");
      return false;
    }

    if (!isRealNumber($amount)) {
      add_info("В поле \"Сумма платежа\" должно быть число с двумя знаками после запятой");
      return false;
    }

    $max_comment_len = opt_get('max_comment_len');
    if (strlen($comment) > $max_comment_len) {
      add_info("Поле \"Комментарий\" не может содержать более " . $max_comment_len . " символов");
      return false;
    }

    return true;
  }

  /**
   * Создание нового платежа
   * @param <type> $responsible_id - ID Ответственного
   * @param <type> $date - Дата совершения платежа
   * @param <type> $cheque_number - Номер чека-ордера
   * @param <type> $payer_full_name - Полное имя плательщика
   * @param <type> $amount - Сумма платежа
   * @param <type> $comment - Комментарий
   * @return <type>
   */
  function payment_create($responsible_id, $date, $cheque_number, $payer_full_name, $amount, $comment) {
    if (!payment_check_fields($date, $cheque_number, $payer_full_name, $amount, $comment)) {
      return false;
    }

    // Checking has been passed
    $cheque_number = db_string($cheque_number);
    $payer_full_name = db_string($payer_full_name);
    $date = db_string(date('Y-m-d H:i:s', strtotime($date)));
    $amount = str_replace(',', '.', $amount);
    $comment = db_string($comment);
    db_insert('payment', array('responsible_id' => $responsible_id,
        'date' => $date,
        'cheque_number' => $cheque_number,
        'payer_full_name' => $payer_full_name,
        'amount' => $amount,
        'comment' => $comment));

    return true;
  }

  function payment_create_received() {
    // Get post data
    $date = stripslashes(trim($_POST['date']));
    $cheque_number = stripslashes(trim($_POST['cheque_number']));
    $payer_full_name = stripslashes(trim($_POST['payer_full_name']));
    $amount = stripslashes(trim($_POST['amount']));
    $comment = stripslashes(trim($_POST['comment']));
    $responsible_id = user_id();

    if (payment_create($responsible_id, $date, $cheque_number, $payer_full_name, $amount, $comment)) {
      $_POST = array();
      return true;
    }

    return false;
  }

  function payment_update($id, $date, $cheque_number, $payer_full_name, $amount, $comment) {

    if (!payment_check_fields($date, $cheque_number, $payer_full_name, $amount, $comment, true, $id)) {
      return false;
    }

    $cheque_number = db_string($cheque_number);
    $payer_full_name = db_string($payer_full_name);
    $date = db_string(date('Y-m-d H:i:s', strtotime($date)));
    $amount = str_replace(',', '.', $amount);
    $comment = db_string($comment);

    $update = array('date' => $date,
        'cheque_number' => $cheque_number,
        'payer_full_name' => $payer_full_name,
        'amount' => $amount,
        'comment' => $comment);

    db_update('payment', $update, "`id`=$id");

    return true;
  }

  function payment_update_received($id) {
    // Get post data
    $date = stripslashes(trim($_POST['date']));
    $cheque_number = stripslashes(trim($_POST['cheque_number']));
    $payer_full_name = stripslashes(trim($_POST['payer_full_name']));
    $amount = stripslashes(trim($_POST['amount']));
    $comment = stripslashes(trim($_POST['comment']));
    $responsible_id = user_id();

    if (payment_update($id, $date, $cheque_number, $payer_full_name, $amount, $comment)) {
      $_POST = array();
    }
  }

  function payment_can_delete($id) {
    $p = payment_get_by_id($id);
    $tc = teams_count_is_payment($id);
    $b = group_get_by_name("Бухгалтеры");
    if ($p['responsible_id'] == user_id()) {
      //Its our payment, all is good
      if (tc > 0) {
        add_info("Данный платеж не доступен для удаления");
        return false;
      }
      return true;
    }
    if (!is_user_in_group(user_id(), $b['id'])) {
      add_info("Вы не имеете прав для удаления данного платежа");
      return false;
    }
    return true;
  }

  function payment_delete($id) {
    if (!payment_can_delete($id)) {
      return false;
    }

    return db_delete('payment', 'id=' . $id);
  }

  function payment_get_by_id($id) {
    return db_row_value('payment', "`id`=$id");
  }

}
?>
