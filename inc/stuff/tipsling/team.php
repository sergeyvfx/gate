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
            . " team.*, "
            . " (SELECT pupil.FIO FROM pupil JOIN pupil_team on pupil_team.pupil_id=pupil.id WHERE pupil_team.team_id = team.id AND pupil_team.number=1) as pupil1_full_name, "
            . " (SELECT pupil.FIO FROM pupil JOIN pupil_team on pupil_team.pupil_id=pupil.id WHERE pupil_team.team_id = team.id AND pupil_team.number=2) as pupil2_full_name, "
            . " (SELECT pupil.FIO FROM pupil JOIN pupil_team on pupil_team.pupil_id=pupil.id WHERE pupil_team.team_id = team.id AND pupil_team.number=3) as pupil3_full_name, "
            . " GROUP_CONCAT(DISTINCT pupil.FIO ORDER BY pupil_team.number ASC SEPARATOR  ', ') as pupils, \n"
            . " GROUP_CONCAT(DISTINCT teacher.FIO ORDER BY teacher_team.number ASC SEPARATOR  ', ') as teacher_full_name \n"
            . "FROM\n"
            . " team, region, responsible, school, city, pupil, pupil_team, teacher, teacher_team \n"
            . "WHERE\n"
            . $where
            . " team.responsible_id=responsible.user_id AND\n"
            . " responsible.school_id = school.id AND\n"
            . " region.id = school.region_id AND\n"
            . " city.id = school.city_id AND\n"
            . " pupil_team.team_id = team.id AND\n"
            . " pupil.id = pupil_team.pupil_id AND\n"
            . " teacher_team.team_id = team.id AND\n"
            . " teacher.id = teacher_team.teacher_id \n"
            . " GROUP BY team.id \n"
            . $sort;
    return arr_from_query($sql);
  }

  /**
   * Проверка корректности заполнения полей
   */
  function team_check_fields($grade, $teachers, $pupils, $comment, $update=false, $id=-1) {
    if ($update) {
      $team = team_get_by_id($id);
      $has_access = check_contestbookkeeper_rights($team['contest_id']);
      if (!check_edit_team_allow($team['contest_id']) && !$has_access) {
        add_info("Данная команда не доступна для редактирования");
        return false;
      }
      if (!check_is_team_owner($team) && !$has_access) {
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

    $isTeachersEmpty=true;
    foreach($teachers as $teacher){
        if ($teacher['FIO'] != ''){
            $isTeachersEmpty=false;
            break;
        }
    }    
    if ($isTeachersEmpty) {
      add_info('Поле "Полное имя учителя" является обязательным для заполнения');
      return false;
    }

    if ($pupils[0]['FIO'] == '') {
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
  function team_create($number, $responsible_id, $contest_id, $payment_id, $grade, $teachers, $pupils, $is_payment, $smena, $comment) {
    if (!team_check_fields($grade, $teachers, $pupils, $comment)) {
      return false;
    }
    // Checking has been passed
    for ($i=0; $i<count($teachers); $i++){
        $teachers[$i]=db_string($teachers[$i]);
    }    
    for ($i=0; $i<count($pupils); $i++){
        $pupils[$i]=db_string($pupils[$i]);
    }
    $comment = db_string($comment);
    db_insert('team', array('reg_number' => $number,
        'number' => $number,
        'responsible_id' => $responsible_id,
        'contest_id' => $contest_id,
        'payment_id' => $payment_id,
        'grade' => $grade,
        'is_payment' => $is_payment,
        'smena' => $smena,
        'comment' => $comment));
    
    $team_id = db_last_insert_id();
    $pupils_count = count($pupils);
    $number=1;
    for ($i=0; $i<$pupils_count; $i++){
        if ($pupils[$i]!='""'){
            //добавление нового ученика
            db_insert('pupil', array('FIO'=>$pupils[$i]));
            $pupil_id = db_last_insert_id();
            db_insert('pupil_team', array('team_id'=>$team_id,'number'=>$number,'pupil_id'=>$pupil_id));
            $number+=1;                
        }
    }
    $teachers_count = count($teachers);
    $number=1;
    for ($i=0; $i<$teachers_count; $i++){
        if ($teachers[$i]!='""'){
            //добавление нового учителя
            db_insert('teacher', array('FIO'=>$teachers[$i]));
            $teacher_id = db_last_insert_id();
            db_insert('teacher_team', array('team_id'=>$team_id,'number'=>$number,'teacher_id'=>$teacher_id));
            $number+=1;                
        }
    }
    return true;
  }

  function team_create_received() {
    // Get post data
    global $current_contest;
    $grade = stripslashes(trim($_POST['grade']));
    $all_teachers = $_POST['teachers'];
    $all_pupils = $_POST['pupils'];
    $smena = stripslashes(trim($_POST['smena']));
    $payment_id = stripslashes(trim($_POST['payment_id']));
    
    if ($payment_id == '') {
      $payment_id = -1;
    }
    $comment = stripslashes(trim($_POST['comment']));
    $contest_id = stripslashes(trim($_POST['ContestGroup']));
    if ($contest_id == '')
        $contest_id = $current_contest;
    
    $number=db_max('team','reg_number', "`grade`=$grade AND `contest_id`=$contest_id")+1;
    $responsible_id = user_id();
    $is_payment = 0;
    
    $teachers=array();
    $pupils=array();
    
    foreach ($all_teachers as $key => $value){
        $teachers[]=stripslashes(trim($value));
    }    
    foreach ($all_pupils as $key => $value){
        $pupils[]=stripslashes(trim($value));
    }
    if (team_create($number, $responsible_id, $contest_id, $payment_id, $grade,
                    $teachers, $pupils, $is_payment, $smena, $comment)) {
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
    $teachers = gate_array_column($this_team['teachers'], 'FIO');
    $pupils = gate_array_column($this_team['pupils'], 'FIO');
    $smena = $this_team['smena'];
    
    $payment_id = -1;

    $comment = $this_team['comment'];
    $contest_id = $current_contest;
    
    $responsible_id = $this_team['responsible_id'];
    $is_payment = 0;
    $number=db_max('team','reg_number', "`grade`=$grade AND `contest_id`=$contest_id")+1;
    
    if (team_create($number, $responsible_id, $contest_id, $payment_id, $grade,
                    $teachers, $pupils, $is_payment, $smena, $comment)) {
      $_POST = array();
      return true;
    }

    return false;
  }

  function team_update($id, $payment_id, $grade, $all_teachers, $all_teachers_team, $all_pupils, $all_pupils_team, $is_payment, $reg_number, $number, $smena, $comment) {
      if (count($all_teachers)!=count($all_teachers_team)){
          add_info('Неверно заполнена информация об учителе, попробуйте еще раз.');
          return false;
      }
      if (count($all_pupils)!=count($all_pupils_team)){
          add_info('Неверно заполнена информация об учениках, попробуйте еще раз.');
          return false;
      }
    $teachers=array();
    $pupils=array();
    
    $i=0;
    foreach ($all_teachers as $key => $value){
        $teachers[$i]['FIO']=stripslashes(trim($value));
        $teachers[$i]['teacher_team_id']=$all_teachers_team[$i];
        $i+=1;
    }
    
    $i=0;
    foreach ($all_pupils as $key => $value){
        $pupils[$i]['FIO']=stripslashes(trim($value));
        $pupils[$i]['pupil_team_id']=$all_pupils_team[$i];
        $i+=1;
    }
    
    if (!team_check_fields($grade, $teachers, $pupils, $comment, true, $id)) {
      return false;
    }
    
    $comment = db_string($comment);
    for ($i=0; $i<count($teachers); $i++){
        $teachers[$i]['FIO']=db_string($teachers[$i]['FIO']);
    }    
    for ($i=0; $i<count($pupils); $i++){
        $pupils[$i]['FIO']=db_string($pupils[$i]['FIO']);
    }
    
    $update = array('payment_id' => $payment_id,
        'grade' => $grade,
        'is_payment' => $is_payment,
        'reg_number' => $reg_number,
        'number' => $number,
        'smena' => $smena,
        'comment' => $comment);
    db_update('team', $update, "`id`=$id").'; ';
    
    $exist_pupils = gate_array_column(pupil_list_by_team_id($id), 'FIO', 'idOfPupil_team');
    $pupils_count = count($pupils);
    $number=1;
    for ($i=0; $i<$pupils_count; $i++){
        if ($pupils[$i]['pupil_team_id']==''){
            if ($pupils[$i]['FIO']!='""'){
                //добавление нового ученика
                db_insert('pupil', array('FIO'=>$pupils[$i]['FIO']));
                $pupil_id = db_last_insert_id();
                db_insert('pupil_team', array('team_id'=>$id,'number'=>$number,'pupil_id'=>$pupil_id));
                $number+=1;                
            }
        }
        else {
            if ($pupils[$i]['FIO'] == '""'){
                //удаление ученика
                $pupil_team_id=$pupils[$i]['pupil_team_id'];
                pupil_team_delete($pupil_team_id);
                if (array_key_exists($pupil_team_id, $exist_pupils)){
                    unset($exist_pupils[$pupil_team_id]);
                }
            }
            else {
                //обновление ученика
                $pupil_team_id=$pupils[$i]['pupil_team_id'];
                $pupil_id = db_field_value('pupil_team', 'pupil_id', "`id`=$pupil_team_id");
                db_update('pupil', array('FIO'=>$pupils[$i]['FIO']),"`id`=$pupil_id");
                db_update('pupil_team', array('number'=>$number),"`id`=$pupil_team_id");
                if (array_key_exists($pupil_team_id, $exist_pupils)){
                    unset($exist_pupils[$pupil_team_id]);
                }
                $number+=1;
            }                
        }
    }
    if(count($exist_pupils)>0){
        foreach(array_keys($exist_pupils) as $key){
            //удаление ученика
            pupil_team_delete($key);
        }
    }
    
    $exist_teachers = gate_array_column(teacher_list_by_team_id($id), 'FIO', 'idOfTeacher_team');
    $teachers_count = count($teachers);
    $number=1;
    for ($i=0; $i<$teachers_count; $i++){
        if ($teachers[$i]['teacher_team_id']==''){
            if ($teachers[$i]['FIO']!='""'){
                //добавление нового учителя
                db_insert('teacher', array('FIO'=>$teachers[$i]['FIO']));
                $teacher_id = db_last_insert_id();
                db_insert('teacher_team', array('team_id'=>$id,'number'=>$number,'teacher_id'=>$teacher_id));
                $number+=1;                
            }
        }
        else {
            if ($teachers[$i]['FIO'] == '""'){
                //удаление учителя
                $teacher_team_id=$teachers[$i]['teacher_team_id'];
                teacher_team_delete($teacher_team_id);
                if (array_key_exists($teacher_team_id, $exist_teachers)){
                    unset($exist_teachers[$teacher_team_id]);
                }
            }
            else {
                //обновление учителя
                $teacher_team_id=$teachers[$i]['teacher_team_id'];
                $teacher_id = db_field_value('teacher_team', 'teacher_id', "`id`=$teacher_team_id");
                db_update('teacher', array('FIO'=>$teachers[$i]['FIO']),"`id`=$teacher_id");
                db_update('teacher_team', array('number'=>$number),"`id`=$teacher_team_id");
                if (array_key_exists($teacher_team_id, $exist_teachers)){
                    unset($exist_teachers[$teacher_team_id]);
                }
                $number+=1;
            }                
        }
    }
    if(count($exist_teachers)>0){
        foreach(array_keys($exist_teachers) as $key){
            //удаление учителя
            teacher_team_delete($key);
        }
    }
    return true;
  }

  function team_update_received($id) {
    // Get post data
    $teachers = $_POST['teachers'];
    $teachers_team = $_POST['teacher_team'];
    $pupils = $_POST['pupils'];
    $pupils_team = $_POST['pupil_team'];
    $grade = stripslashes(trim($_POST['grade']));
    $comment = stripslashes(trim($_POST['comment']));
    $team = team_get_by_id($id);
    $is_payment = $team['is_payment'];
    $payment_id = $team['payment_id'];
    $number = $team['number'];
    $smena = $team['smena'];
    
    if (check_contestbookkeeper_rights($team['contest_id'])) {
        if ($_POST['is_payment_value']!='')
            $is_payment = stripslashes(trim($_POST['is_payment_value']));
        if ($_POST['payment_id']!='')
            $payment_id = stripslashes(trim($_POST['payment_id']));
    }
    if (check_contestadmin_rights() && stripslashes(trim($_POST['number']))!=''){
        $number = stripslashes(trim($_POST['number']));
    }    
    if (check_can_user_edit_teamsmena_field($team)){
        $smena = stripslashes(trim($_POST['smena']));
    }    
    if ($team['grade'] != $grade && check_can_user_edit_teamgrade_field($team)) {
      $number = db_max('team','number',"`grade`=$grade AND `contest_id`=".$team['contest_id']) + 1;
      $reg_number = $number;
    } else {
      $reg_number = $team['reg_number'];
    }

    if (team_update($id, $payment_id, $grade, $teachers, $teachers_team, $pupils, $pupils_team,
                    $is_payment, $reg_number, $number, $smena, $comment)) {
      $_POST = array();
    }
  }

  function team_update_is_payment($id, $payment_id, $is_payment) {
    $update = array('payment_id' => $payment_id, 'is_payment' => $is_payment);

    db_update('team', $update, "`id`=$id");
  }

  function team_can_delete($id) {
    $team = team_get_by_id($id);
    $has_access = check_contestadmin_rights();
    if ($team['is_payment'] > 0 && !$has_access) {
      add_info("Данную команду нельзя удалить");
      return false;
    }
    if (!check_is_team_owner($team) && !$has_access) {
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
    $row = db_row_value('team', "`id`=$id");
    $row['teachers'] = teacher_list_by_team_id($id);
    $row['pupils'] = pupil_list_by_team_id($id);
    return $row;
  }

  function teams_count_is_payment($id) {
    return db_count('team', '`payment_id`=' . $id . ' AND `is_payment`=1');
  }
  
  function team_update_results_received($list, $contest_id='') {
      global $current_contest;
      if ($contest_id==''){
          $contest_id = $current_contest;
      }
      $list = team_list('','',$current_contest);
      foreach ($list as &$team) {
          $team_id = $team['id'];
          $mark = stripslashes(trim($_POST["mark"]["$team_id"]));
          $place = stripslashes(trim($_POST["place"]["$team_id"]));
          $common_place = stripslashes(trim($_POST["common_place"]["$team_id"]));
          if ($mark == '') $mark = '0';
          if ($place == '') $place = '0';
          if ($common_place == '') $common_place = '0';
          team_update_results($team_id, $mark, $place, $common_place);          
      }
  }
  
  function team_update_results($id, $mark, $place, $common_place){
      $update = array('mark' => $mark, 'place' => $place, 'common_place'=>$common_place);
      db_update('team', $update, "`id`=$id");
  }
}  
?>
