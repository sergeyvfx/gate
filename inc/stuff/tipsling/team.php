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

if ($_team_included_ != '#team_Included#') {
  $_team_included_ = '#team_Included#';
  $user_infos = array();

  function team_list($responsible_id = -1, $sort = 1, $contest = -1, $filter=-1) {
    if ($responsible_id == '') {
      $responsible_id = -1;
    }

    if ($sort == '') {
      $sort = 1;
    }
    
    if ($contest == '') $contest = -1;
    
    if ($filter == '') $filter = -1;

    if ($sort == 1) {
      $sort = "ORDER BY team.grade, team.number";
    } elseif ($sort == 2) {
      $sort = "ORDER BY region.name, team.grade, team.number";
    } elseif ($sort == 3) {
      $sort = "ORDER BY region.name, city.name, school.name, team.grade, team.number";
    }

    if ($responsible_id < 0) {
      $where = '';
    } else {
      $where = " team.responsible_id=" . $responsible_id . " AND\n";
    }
    
    if ($contest != -1)
        $where .= "team.contest_id=".$contest." AND\n";
    
    if ($filter == 2)
        $where .= "team.place>0 AND team.place<4 AND\n";

    $sql = "SELECT\n"
            . " team.*\n"
            . "FROM\n"
            . " team, region, responsible, school, city\n"
            . "WHERE\n"
            . $where
            . " team.responsible_id=responsible.user_id AND\n"
            . " responsible.school_id = school.id AND\n"
            . " region.id = school.region_id AND\n"
            . " city.id = school.city_id\n"
            . $sort;

    return arr_from_query($sql);
  }

  /**
   * Проверка корректности заполнения полей
   */
  function team_check_fields($grade, $teacher_full_name, $pupil1_full_name, $comment, $update=false, $id=-1) {
    if ($update) {
      $team = team_get_by_id($id);
      $g = group_get_by_name("Администраторы");
      $has_access = is_user_bookkeeper(user_id(), $team['contest_id']) || is_user_in_group(user_id(), $g['id']) || user_access_root();
      
      $contest_stat = get_contest_status($team['contest_id']);
      $allow_edit = $contest_stat==1 || $contest_stat==2;
      if ($allow_edit && !$has_access) {
        add_info("Данная команда не доступна для редактирования");
        return false;
      }
      if ($team['responsible_id'] != user_id() && !$has_access) {
        add_info('Вы не можете редактировать эту команду');
        return false;
      }
    }
    if ($grade == '') {
      add_info('Поле "Класс" является обязательным для заполнения');
      return false;
    }

    if (!isIntNumber($grade)) {
      add_info('"Класс" должен быть целым положительным числом');
      return false;
    }

    if ($teacher_full_name == '') {
      add_info('Поле "Полное имя учителя" является обязательным для заполнения');
      return false;
    }

    if ($pupil1_full_name == '') {
      add_info('Поле "Полное имя 1-го участника" является обязательным для заполнения');
      return false;
    }

    $max_comment_len = opt_get('max_comment_len');
    if (strlen($comment) > $max_comment_len) {
      add_info("Поле \"Примечание\" не может содержать более " . $max_comment_len . " символов");
      return false;
    }
    return true;
  }

  /**
   * Создание новой команды
   * @param <type> $number - Номер команды
   * @param <type> $responsible_id - ID Ответственного
   * @param <type> $contest_id - ID конкурса
   * @param <type> $payment_id - ID платежа
   * @param <type> $grade - Класс участников
   * @param <type> $teacher_full_name - Полное имя учителя
   * @param <type> $pupil1_full_name - Полное имя первого ученика
   * @param <type> $pupil2_full_name - Полное имя второго ученика
   * @param <type> $pupil3_full_name - Полное имя третьего ученика
   * @param <type> $is_payment - Флаг оплаты участия
   * @param <type> $comment - Примечание
   * @return <type> Вернет true если команда успешно создана, в противном случае
   *                вернет false
   */
  function team_create($number, $responsible_id, $contest_id, $payment_id, $grade, $teacher_full_name, $pupil1_full_name, $pupil2_full_name, $pupil3_full_name, $is_payment, $smena, $comment) {
    if (!team_check_fields($grade, $teacher_full_name, $pupil1_full_name, $comment)) {
      return false;
    }

    // Checking has been passed
    $teacher_full_name = db_string($teacher_full_name);
    $pupil1_full_name = db_string($pupil1_full_name);
    $pupil2_full_name = db_string($pupil2_full_name);
    $pupil3_full_name = db_string($pupil3_full_name);
    $comment = db_string($comment);
    db_insert('team', array('number' => $number,
        'responsible_id' => $responsible_id,
        'contest_id' => $contest_id,
        'payment_id' => $payment_id,
        'grade' => $grade,
        'teacher_full_name' => $teacher_full_name,
        'pupil1_full_name' => $pupil1_full_name,
        'pupil2_full_name' => $pupil2_full_name,
        'pupil3_full_name' => $pupil3_full_name,
        'is_payment' => $is_payment,
        'smena' => $smena,
        'comment' => $comment));

    return true;
  }

  function team_create_received() {
    // Get post data
    global $current_contest;
    $grade = stripslashes(trim($_POST['grade']));
    $teacher_full_name = stripslashes(trim($_POST['teacher_full_name']));
    $pupil1_full_name = stripslashes(trim($_POST['pupil1_full_name']));
    $pupil2_full_name = stripslashes(trim($_POST['pupil2_full_name']));
    $pupil3_full_name = stripslashes(trim($_POST['pupil3_full_name']));
    $smena = stripslashes(trim($_POST['smena']));
    $payment_id = stripslashes(trim($_POST['payment_id']));
    
    if ($payment_id == '') {
      $payment_id = -1;
    }
    $comment = stripslashes(trim($_POST['comment']));
    $contest_id = stripslashes(trim($_POST['ContestGroup']));
    if ($contest_id == '')
        $contest_id = $current_contest;
    
    $number=db_max('team','number', "`grade`=$grade AND `contest_id`=$contest_id")+1;
    $responsible_id = user_id();
    $is_payment = 0;
    if (team_create($number, $responsible_id, $contest_id, $payment_id, $grade,
                    $teacher_full_name, $pupil1_full_name, $pupil2_full_name,
                    $pupil3_full_name, $is_payment, $smena, $comment)) {
      $_POST = array();
      return true;
    }

    return false;
  }
  
  function team_register_again_received() {
    // Get post data
    global $current_contest;
    $team_id = stripslashes(trim($_POST['Team']));
    $this_team = team_get_by_id($team_id);
    $grade = $this_team['grade']+1;
    $teacher_full_name = $this_team['teacher_full_name'];
    $pupil1_full_name = $this_team['pupil1_full_name'];
    $pupil2_full_name = $this_team['pupil2_full_name'];
    $pupil3_full_name = $this_team['pupil3_full_name'];
    $payment_id = -1;

    $comment = $this_team['comment'];
    $contest_id = $current_contest;
    
    $responsible_id = $this_team['responsible_id'];
    $is_payment = 0;
    $number=db_max('team','number', "`grade`=$grade AND `contest_id`=$contest_id")+1;
    
    if (team_create($number, $responsible_id, $contest_id, $payment_id, $grade,
                    $teacher_full_name, $pupil1_full_name, $pupil2_full_name,
                    $pupil3_full_name, $is_payment, $comment)) {
      $_POST = array();
      return true;
    }

    return false;
  }

  function team_update($id, $payment_id, $grade, $teacher_full_name, $pupil1_full_name, $pupil2_full_name, $pupil3_full_name, $is_payment, $number, $smena, $comment) {
    if (!team_check_fields($grade, $teacher_full_name, $pupil1_full_name, $comment, true, $id)) {
      return false;
    }

    $teacher_full_name = db_string($teacher_full_name);
    $pupil1_full_name = db_string($pupil1_full_name);
    $pupil2_full_name = db_string($pupil2_full_name);
    $pupil3_full_name = db_string($pupil3_full_name);
    $comment = db_string($comment);
    //$number = db_string($number);

    $update = array('payment_id' => $payment_id,
        'grade' => $grade,
        'teacher_full_name' => $teacher_full_name,
        'pupil1_full_name' => $pupil1_full_name,
        'pupil2_full_name' => $pupil2_full_name,
        'pupil3_full_name' => $pupil3_full_name,
        'is_payment' => $is_payment,
        'number' => $number,
        'smena' => $smena,
        'comment' => $comment);

    db_update('team', $update, "`id`=$id");

    return true;
  }

  function team_update_received($id) {
    // Get post data
    $grade = stripslashes(trim($_POST['grade']));
    $teacher_full_name = stripslashes(trim($_POST['teacher_full_name']));
    $pupil1_full_name = stripslashes(trim($_POST['pupil1_full_name']));
    $pupil2_full_name = stripslashes(trim($_POST['pupil2_full_name']));
    $pupil3_full_name = stripslashes(trim($_POST['pupil3_full_name']));
    $smena = stripslashes(trim($_POST['smena']));
    $comment = stripslashes(trim($_POST['comment']));
    $team = team_get_by_id($id);
    $is_payment = stripslashes(trim($_POST['is_payment_value']));
    if ($is_payment=='')
        $is_payment = $team['is_payment_value'];
    $payment_id = stripslashes(trim($_POST['payment_id']));
    if ($payment_id == '') {
      $payment_id = $team['payment_id'];
    }
    if ($team['grade'] != $grade) {
      $number = db_max('team','number',"`grade`=$grade AND `contest_id`=".$team['contest_id']) + 1;
    } else {
      $number = $team['number'];
    }

    if (team_update($id, $payment_id, $grade, $teacher_full_name,
                    $pupil1_full_name, $pupil2_full_name, $pupil3_full_name,
                    $is_payment, $number, $smena, $comment)) {
      $_POST = array();
    }
  }

  function team_update_is_payment($id, $payment_id, $is_payment) {
    $update = array('payment_id' => $payment_id, 'is_payment' => $is_payment);

    db_update('team', $update, "`id`=$id");
  }

  function team_can_delete($id) {
    $team = team_get_by_id($id);
    $g = group_get_by_name("Администраторы");
    $has_access = is_user_in_group(user_id(), $g['id']) || user_access_root();
    if ($team['is_payment'] > 0 && !$has_access) {
      add_info("Данную команду нельзя удалить");
      return false;
    }
    if ($team['responsible_id'] != user_id() && !$has_access) {
      add_info('Вы не можете удалять эту команду');
      return false;
    }
    return true;
  }

  function team_delete($id) {
    if (!team_can_delete($id)) {
      return false;
    }

    return db_delete('team', 'id=' . $id);
  }

  function team_get_by_id($id) {
    return db_row_value('team', "`id`=$id");
  }

  function teams_count_is_payment($id) {
    return db_count('team', '`payment_id`=' . $id . ' AND `is_payment`=1');
  }

}
?>
