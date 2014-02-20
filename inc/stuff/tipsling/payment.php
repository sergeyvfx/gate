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

  function payment_list($responsible_id = -1, $contest_id = -1) {
    if ($responsible_id == '') {
      $responsible_id = -1;
    }
    
    if ($contest_id == '') {
      $contest_id = -1;
    }
    
    $sort="";
    $where = "";
    if ($contest_id != -1)
        $where .= "where `payment`.`contest_id`=".$contest_id;
     
    if ($responsible_id != -1)
    {
        $sort = " ORDER BY `date`";
        if ($where != "")
            $where .= " AND\n`payment`.`responsible_id`=" . $responsible_id;
        else
            $where = " where `payment`.`responsible_id`=" . $responsible_id;
    }
    else
        $sort = " ORDER BY `date`";

    $sql='SELECT * FROM `payment` '.$where.$sort;
    return arr_from_query($sql);
  }

  /**
   * Проверка корректности заполнения полей
   */
  function payment_check_fields($payment_option, $date, $amount, $team_numbers, $payer_full_name, $cheque_number, $comment, $update = false, $id = -1) {
    if ($update) {
      $p = payment_get_by_id($id);
      $b = group_get_by_name("Бухгалтеры");
      $contest = contest_get_by_id($p['contest_id']);
      $contest_status = get_contest_status($contest['id']);
      if ($contest_status>2 && $p['date_arrival'] != null && !is_user_in_group(user_id(), $b['id'])) {
        add_info("Данный платеж не доступен для редактирования");
        return false;
      }
    }
    
    if ($payment_option == '') {
      add_info("Поле \"Вариант оплаты\" обязательно для заполнения");
      return false;
    }
    
    if ($date == '') {
      add_info("Поле \"Дата\" обязательно для заполнения");
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

    if ($team_numbers == '') {
      alert("Поле \"Номера команд, за которых оплачено\" обязательно для заполнения");
      return;
    }
            
    if ($payer_full_name == '') {
      add_info("Поле \"Полное имя плательщика\" обязательно для заполнения");
      return false;
    }

    if ($payment_option == 1 && $cheque_number == '') {
      add_info("Поле \"Номер чека-ордера\" обязательно для заполнения");
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
  function payment_create($responsible_id, $contest_id, $payment_option, $date, $amount, $team_numbers, $payer_full_name, $cheque_number, $reposts, $comment) {
    if (!payment_check_fields($payment_option, $date, $amount, $team_numbers, $payer_full_name, $cheque_number, $comment)) {
      return false;
    }
    // Checking has been passed
    $payment_option = db_string($payment_option);
    $date = db_string(date('Y-m-d H:i:s', strtotime($date)));
    $amount = str_replace(',', '.', $amount);
    $team_numbers = db_string($team_numbers);
    $payer_full_name = db_string($payer_full_name);
    $cheque_number = db_string($cheque_number);
    $reposts = db_string($reposts);
    $comment = db_string($comment);
    db_insert('payment', array('responsible_id' => $responsible_id,
        'contest_id' => $contest_id,
        'payment_option' => $payment_option,
        'date' => $date,
        'amount' => $amount,
        'team_numbers' => $team_numbers,
        'payer_full_name' => $payer_full_name,
        'cheque_number' => $cheque_number,
        'reposts' => $reposts,
        'comment' => $comment));

    return true;
  }

  function payment_create_received() {
    global $current_contest;
    
    // Get post data
    $payment_option = $_POST['paymentOption'];
    $date = stripslashes(trim($_POST['date']));
    $amount = stripslashes(trim($_POST['amount']));
    $team_numbers = stripslashes(trim($_POST['teamNumbers']));
    $payer_full_name = stripslashes(trim($_POST['payer_full_name']));
    $cheque_number = stripslashes(trim($_POST['cheque_number']));
    $reposts = join(';', $_POST['repost']);
    $comment = stripslashes(trim($_POST['comment']));
    //$file = stripslashes(trim($_POST['file']));
    
    $responsible_id = user_id();
    
    if (payment_create($responsible_id, $current_contest, $payment_option, $date, $amount, $team_numbers, $payer_full_name, $cheque_number, $reposts, $comment)) {
    
      $_POST = array();
      return true;
    }
    
    return false;
  }

  function payment_update($id, $payment_option, $date, $amount, $team_numbers, $payer_full_name, $cheque_number, $reposts, $comment) {
    if (!payment_check_fields($payment_option, $date, $amount, $team_numbers, $payer_full_name, $cheque_number, $comment)) {
      return false;
    }
    
    $payment_option = db_string($payment_option);
    $date = db_string(date('Y-m-d H:i:s', strtotime($date)));
    $amount = str_replace(',', '.', $amount);
    $team_numbers = db_string($team_numbers);
    $payer_full_name = db_string($payer_full_name);
    $cheque_number = db_string($cheque_number);
    $reposts = db_string($reposts);
    $comment = db_string($comment);

    $update = array('payment_option' => $payment_option,
        'date' => $date,
        'amount' => $amount,
        'team_numbers' => $team_numbers,
        'payer_full_name' => $payer_full_name,
        'cheque_number' => $cheque_number,
        'reposts' => $reposts,
        'comment' => $comment);
    
    db_update('payment', $update, "`id`=$id");
    return true;
  }

  function payment_update_received($id) {
    // Get post data
    $payment_option = $_POST['paymentOption'];
    $date = stripslashes(trim($_POST['date']));
    $amount = stripslashes(trim($_POST['amount']));
    $team_numbers = stripslashes(trim($_POST['teamNumbers']));
    $payer_full_name = stripslashes(trim($_POST['payer_full_name']));
    $cheque_number = stripslashes(trim($_POST['cheque_number']));
    $reposts = join(';', $_POST['repost']);
    $comment = stripslashes(trim($_POST['comment']));
    //$file = stripslashes(trim($_POST['file']));
    
    if (payment_update($id, $payment_option, $date, $amount, $team_numbers, $payer_full_name, $cheque_number, $reposts, $comment)) {
        $_POST = array();
    }  
  }

  function payment_apply($id) {
    global $current_contest;
    $date_arrival = stripslashes(trim($_POST['date_arrival']));
    $payment = payment_get_by_id($id);
    db_query("UPDATE `team` SET `team`.`is_payment`=0, `team`.`payment_id`=null WHERE (`team`.`is_payment`=0 OR `team`.`payment_id`=".$id.") AND `team`.`responsible_id`=" . $payment['responsible_id'] . " AND `team`.`contest_id`=".$current_contest." ORDER BY `team`.`grade`, `team`.`number`");
    foreach ($_POST as $p => $value) {
      if (preg_match("/^team_(?P<id>\d+)$/", $p, $t) > 0) {
        team_update_is_payment($t['id'], $id, 1);
      }
    }
    $date_arrival = db_string(date('Y-m-d H:i:s', strtotime($date_arrival)));
    $update = array('date_arrival' => $date_arrival);
    db_update('payment', $update, "`id`=$id");
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
