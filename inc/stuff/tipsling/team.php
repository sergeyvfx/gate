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
      $sort = "ORDER BY team.reg_grade, team.number";
    } elseif ($sort == 2) {
      $sort = "ORDER BY region.name, team.reg_grade, team.number";
    } elseif ($sort == 3) {
      $sort = "ORDER BY region.name, city.name, school.name, team.reg_grade, team.number";
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
            . " team_type.id as team_type_id, "
            . " team_type.name as team_type, "
            . " region.name as region, "
            . " city.name as city, "
            . " school.name as school, "
            . " country.id as country_id, "
            . " country.name as country, "
            . " (SELECT pupil.FIO FROM pupil JOIN pupil_team on pupil_team.pupil_id=pupil.id WHERE pupil_team.team_id = team.id AND pupil_team.number=1) as pupil1_full_name, "
            . " (SELECT pupil.FIO FROM pupil JOIN pupil_team on pupil_team.pupil_id=pupil.id WHERE pupil_team.team_id = team.id AND pupil_team.number=2) as pupil2_full_name, "
            . " (SELECT pupil.FIO FROM pupil JOIN pupil_team on pupil_team.pupil_id=pupil.id WHERE pupil_team.team_id = team.id AND pupil_team.number=3) as pupil3_full_name, "
            . " GROUP_CONCAT(DISTINCT pupil.FIO ORDER BY pupil_team.number ASC SEPARATOR  ', ') as pupils, \n"
            . " GROUP_CONCAT(DISTINCT teacher.FIO ORDER BY teacher_team.number ASC SEPARATOR  ', ') as teacher_full_name \n"
            . "FROM\n"
            . " team, team_type, country, region, responsible, school, city, pupil, pupil_team, teacher, teacher_team \n"
            . "WHERE\n"
            . $where
            . " team.team_type_id=team_type.id AND\n"
            . " team.responsible_id=responsible.user_id AND\n"
            . " responsible.school_id = school.id AND\n"
            . " city.id = school.city_id AND\n"
            . " region.id = city.region_id AND\n"
            . " country.id = region.country_id AND\n"
            . " pupil_team.team_id = team.id AND\n"
            . " pupil.id = pupil_team.pupil_id AND\n"
            . " teacher_team.team_id = team.id AND\n"
            . " teacher.id = teacher_team.teacher_id \n"
            . " GROUP BY team.id \n"
            . $sort;
    return arr_from_query($sql);
  }
  
  function team_list_sort_by_mark($contest_id)
  {
      if ($contest_id == '') $contest_id = $current_contest;
      
      $sql = "SELECT\n"
            . " team.*, "
            . "FROM \n"
            . " team \n"
            . "WHERE \n"
            . " team.contest_id=$contest_id AND"
            . " team.mark is not null \n"
            . "SORT BY team.mark";
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
   * @param <type> $team_type_id - Тип команды
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
  function team_create($team_type_id, $number, $responsible_id, $contest_id, $payment_id, 
                       $grade, $reg_grade, $teachers, $pupils, $is_payment, $contest_day, 
                       $smena, $date, $reposts, $payment_sum, $comment) {
    if (!team_check_fields($grade, $teachers, $pupils, $comment)) {
      return false;
    }
    
    // Checking has been passed
    for ($i=0; $i<count($teachers); $i++){
        $teachers[$i]['FIO']=db_string($teachers[$i]['FIO']);
        $teachers[$i]['contests']=db_string($teachers[$i]['contests']);
        $teachers[$i]['winners']=db_string($teachers[$i]['winners']);
        $teachers[$i]['other_contest']=db_string($teachers[$i]['other_contest']);
    }    
    
    for ($i=0; $i<count($pupils); $i++){
        $pupils[$i]['FIO']=db_string($pupils[$i]['FIO']);
        $pupils[$i]['contests']=db_string($pupils[$i]['contests']);
        $pupils[$i]['winners']=db_string($pupils[$i]['winners']);
        $pupils[$i]['other_contest']=db_string($pupils[$i]['other_contest']);
    }
    $contest_day = db_string($contest_day);
    $date = $date !=null ? db_string(date('Y-m-d H:i:s', strtotime($date))) : 'null';
    $reposts = db_string($reposts);
    $comment = db_string($comment);
    db_insert('team', array('team_type_id' => $team_type_id,
        'reg_number' => $number,
        'number' => $number,
        'responsible_id' => $responsible_id,
        'contest_id' => $contest_id,
        'payment_id' => $payment_id,
        'grade' => $grade,
        'reg_grade' => $reg_grade,
        'is_payment' => $is_payment,
        'contest_day' => $contest_day,
        'smena' => $smena,
        'payment_date' => $date,
        'reposts' => $reposts,
        'comment' => $comment,
        'payment_sum' => $payment_sum));
    
    $team_id = db_last_insert_id();
    $pupils_count = count($pupils);
    $number=1;
    for ($i=0; $i<$pupils_count; $i++){
        if ($pupils[$i]['FIO']!='""'){
            //добавление нового ученика
            db_insert('pupil', array('FIO' => $pupils[$i]['FIO'], 
                                     'contests' => $pupils[$i]['contests'], 
                                     'winners' => $pupils[$i]['winners'],
                                     'other_contest' => $pupils[$i]['other_contest']));
            $pupil_id = db_last_insert_id();
            db_insert('pupil_team', array('team_id'=>$team_id,'number'=>$number,'pupil_id'=>$pupil_id));
            $number+=1;                
        }
    }
    $teachers_count = count($teachers);
    $number=1;
    for ($i=0; $i<$teachers_count; $i++){
        if ($teachers[$i]['FIO']!='""'){
            //добавление нового учителя
            db_insert('teacher', array('FIO' => $teachers[$i]['FIO'], 
                                        'contests' => $teachers[$i]['contests'], 
                                        'winners' => $teachers[$i]['winners'],
                                        'other_contest' => $teachers[$i]['other_contest']));
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
    $team_type = stripslashes(trim($_POST['team_type']));
    $grade = stripslashes(trim($_POST['grade']));
    $all_teachers = $_POST['teachers'];
    $all_pupils = $_POST['pupils'];
    $contest_day = stripslashes(trim($_POST['contest_day']));
    $smena = stripslashes(trim($_POST['smena']));
    $date = stripslashes(trim($_POST['date']));
    $reposts = join(';', $_POST['repost']);
    $pupil_contest_count = $_POST['pupil_contest_count'];
    $pupil_winner_count = $_POST['pupil_winner_count'];
    $pupil_contest = $_POST['pupil_contest_value'];
    $pupil_winner = $_POST['pupil_winner_value'];
    $pupil_other_contest = $_POST['pupil_other_contest'];
    $teacher_contest_count = $_POST['teacher_contest_count'];
    $teacher_winner_count = $_POST['teacher_winner_count'];
    $teacher_contest = $_POST['teacher_contest_value'];
    $teacher_winner = $_POST['teacher_winner_value'];
    $teacher_other_contest = $_POST['teacher_other_contest'];
    $payment_sum = stripslashes(trim($_POST['payment_sum']));
    $payment_id = stripslashes(trim($_POST['payment_id']));
    
    if ($payment_id == '') {
      $payment_id = -1;
    }
    $comment = stripslashes(trim($_POST['comment']));
    $contest_id = stripslashes(trim($_POST['ContestGroup']));
    if ($contest_id == '')
        $contest_id = $current_contest;
    
    $team_type_object = teamType_get_by_id($team_type);
    $reg_grade = $grade + $team_type_object['grade_offset_number'];
    $number=db_max('team','reg_number', "`reg_grade`=$reg_grade AND `contest_id`=$contest_id")+1;
    $responsible_id = user_id();
    $is_payment = 0;
    
    $teachers=array();
    $pupils=array();
    $i=0;
    $contest_index=0;
    $winner_index=0;
    foreach ($all_teachers as $key => $value){
        $tmp_teacher = array();
        $tmp_teacher['FIO']=stripslashes(trim($value));
        
        $count = $teacher_contest_count[$i];
        $contests = array();
        for ($j=0;$j<$count;$j++){
            $in_array = array_search($teacher_contest[$contest_index], $contests);
            if (trim($teacher_contest[$contest_index])!='-1' && !$in_array && gettype($in_array)=='boolean'){
                $contests[count($contests)] = $teacher_contest[$contest_index];
            }            
            $contest_index += 1;
        }
        
        $count = $teacher_winner_count[$i];
        $winners = array();
        for ($j=0;$j<$count;$j++){
            $in_array = array_search($teacher_winner[$winner_index], $winners);
            if (trim($teacher_winner[$winner_index])!='-1' && !$in_array && gettype($in_array)=='boolean'){
                $winners[count($winners)] = $teacher_winner[$winner_index];
            }
            $winner_index += 1;
        }
        
        $tmp_teacher['contests']=stripslashes(implode(';',$contests));
        $tmp_teacher['winners']=stripslashes(implode(';',$winners));
        $tmp_teacher['other_contest']=stripslashes($teacher_other_contest[$key]);
        
        $teachers[]=$tmp_teacher;
        $i++;
    }
    
    $i=0;
    $contest_index=0;
    $winner_index=0;
    foreach ($all_pupils as $key => $value){
        $tmp_pupil = array();
        $tmp_pupil['FIO']=stripslashes(trim($value));
        
        $count = $pupil_contest_count[$i];
        $contests = array();
        for ($j=0;$j<$count;$j++){
            $in_array = array_search($pupil_contest[$contest_index], $contests);
            if (trim($pupil_contest[$contest_index])!='-1' && !$in_array && gettype($in_array)=='boolean'){
                $contests[count($contests)] = $pupil_contest[$contest_index];
            }            
            $contest_index += 1;
        }
        
        $count = $pupil_winner_count[$i];
        $winners = array();
        for ($j=0;$j<$count;$j++){
            $in_array = array_search($pupil_winner[$winner_index], $winners);
            if (trim($pupil_winner[$winner_index])!='-1' && !$in_array && gettype($in_array)=='boolean'){
                $winners[count($winners)] = $pupil_winner[$winner_index];
            }
            $winner_index += 1;
        }
        
        $tmp_pupil['contests']=stripslashes(implode(';',$contests));
        $tmp_pupil['winners']=stripslashes(implode(';',$winners));
        $tmp_pupil['other_contest']=stripslashes($pupil_other_contest[$key]);
        
        $pupils[]=$tmp_pupil;
        $i++;
    }
    
    if (team_create($team_type, $number, $responsible_id, $contest_id, $payment_id, 
                    $grade, $reg_grade, $teachers, $pupils, $is_payment, $contest_day, 
                    $smena, $date, $reposts, $payment_sum, $comment)) {
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
    $this_team_type = teamType_get_by_id($this_team['team_type_id']);
    $grade = $this_team['grade']+1;
    $reg_grade = $this_team['reg_grade']+1;
    if ($grade > $this_team_type['grade_max_number']){
        $this_team_type = teamType_get_next($this_team['team_type_id']);
        $grade = $this_team_type['grade_start_number'];
        $reg_grade = $grade + $this_team_type['grade_offset_number'];
    }
    $teachers_fio = gate_array_column($this_team['teachers'], 'FIO');
    $pupils_fio = gate_array_column($this_team['pupils'], 'FIO');
    $contest_day = $this_team['contest_day'] == '' ? 'сб' : $this_team['contest_day'];
    $smena = $this_team['smena'];
    
    $payment_id = -1;

    $comment = $this_team['comment'];
    $contest_id = $current_contest;
    
    $responsible_id = $this_team['responsible_id'];
    $is_payment = 0;
    $number=db_max('team','reg_number', "`grade`=$grade AND `contest_id`=$contest_id")+1;
    
    foreach ($teachers_fio as $fio) {
        $teacher_tmp = array();
        $teacher_tmp['FIO']=$fio;
        $teacher_tmp['contests']=$this_team['contest_id'];
        $teacher_tmp['winners']='';
        
        $teachers[]=$teacher_tmp;
    }
    
    foreach ($pupils_fio as $fio) {
        $pupil_tmp = array();
        $pupil_tmp['FIO']=$fio;
        $pupil_tmp['contests']=$this_team['contest_id'];
        $pupil_tmp['winners']='';
        
        $pupils[]=$pupil_tmp;
    }
        
    if (team_create($this_team_type['id'], $number, $responsible_id, $contest_id, $payment_id, 
                    $grade, $reg_grade, $teachers, $pupils, $is_payment, $contest_day, $smena, 
                    null, null, "1300", $comment)) {
      $_POST = array();
      return true;
    }

    return false;
  }

  function team_update($id, $team_type_id, $payment_id, $grade, $reg_grade, $all_teachers, $all_teachers_team, 
                       $all_pupils, $all_pupils_team, $is_payment, $reg_number, $number, 
                       $contest_day, $smena, $date, $reposts, $pupil_contest_count, 
                       $pupil_winner_count, $pupil_contest, $pupil_winner, $pupil_other_contest,
                       $teacher_contest_count, $teacher_winner_count, $teacher_contest, 
                       $teacher_winner, $teacher_other_contest, $payment_sum, $comment) {
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
    $contest_index=0;
    $winner_index=0;
    foreach ($all_teachers as $key => $value){
        $teachers[$i]['FIO']=stripslashes(trim($value));
        $teachers[$i]['teacher_team_id']=$all_teachers_team[$i];
        $teachers[$i]['other_contest']=$teacher_other_contest[$i];
        
        $count = $teacher_contest_count[$i];
        $contests = array();
        for ($j=0;$j<$count;$j++){
            $in_array = array_search($teacher_contest[$contest_index], $contests);
            if (trim($teacher_contest[$contest_index])!='-1' && !$in_array && gettype($in_array)=='boolean'){
                $contests[count($contests)] = $teacher_contest[$contest_index];
            }            
            $contest_index += 1;
        }
        
        $count = $teacher_winner_count[$i];
        $winners = array();
        for ($j=0;$j<$count;$j++){
            $in_array = array_search($teacher_winner[$winner_index], $winners);
            if (trim($teacher_winner[$winner_index])!='-1' && !$in_array && gettype($in_array)=='boolean'){
                $winners[count($winners)] = $teacher_winner[$winner_index];
            }
            $winner_index += 1;
        }
        
        $teachers[$i]['contests']=stripslashes(implode(';',$contests));
        $teachers[$i]['winners']=stripslashes(implode(';',$winners));
        $i+=1;
    }
    
    $contest_index=0;
    $winner_index=0;
    $i=0;
    foreach ($all_pupils as $key => $value){
        $pupils[$i]['FIO']=stripslashes(trim($value));
        $pupils[$i]['pupil_team_id']=$all_pupils_team[$i];
        $pupils[$i]['other_contest']=$pupil_other_contest[$i];
        
        $count = $pupil_contest_count[$i];
        $contests = array();
        for ($j=0;$j<$count;$j++){
            $in_array = array_search($pupil_contest[$contest_index], $contests);
            if (trim($pupil_contest[$contest_index])!='-1' && !$in_array && gettype($in_array)=='boolean'){
                $contests[count($contests)] = $pupil_contest[$contest_index];
            }            
            $contest_index += 1;
        }
        
        $count = $pupil_winner_count[$i];
        $winners = array();
        for ($j=0;$j<$count;$j++){
            $in_array = array_search($pupil_winner[$winner_index], $winners);
            if (trim($pupil_winner[$winner_index])!='-1' && !$in_array && gettype($in_array)=='boolean'){
                $winners[count($winners)] = $pupil_winner[$winner_index];
            }
            $winner_index += 1;
        }
        
        $pupils[$i]['contests']=stripslashes(implode(';',$contests));
        $pupils[$i]['winners']=stripslashes(implode(';',$winners));
        $i+=1;
    }
    
    if (!team_check_fields($grade, $teachers, $pupils, $comment, true, $id)) {
      return false;
    }
    
    $contest_day = db_string($contest_day);
    $date = db_string(date('Y-m-d H:i:s', strtotime($date)));
    $reposts = db_string($reposts);
    $comment = db_string($comment);
    for ($i=0; $i<count($teachers); $i++){
        $teachers[$i]['FIO']=db_string($teachers[$i]['FIO']);
        $teachers[$i]['contests']=db_string($teachers[$i]['contests']);
        $teachers[$i]['winners']=db_string($teachers[$i]['winners']);
        $teachers[$i]['other_contest']=db_string($teachers[$i]['other_contest']);
    }    
    for ($i=0; $i<count($pupils); $i++){
        $pupils[$i]['FIO']=db_string($pupils[$i]['FIO']);
        $pupils[$i]['contests']=db_string($pupils[$i]['contests']);
        $pupils[$i]['winners']=db_string($pupils[$i]['winners']);
        $pupils[$i]['other_contest']=db_string($pupils[$i]['other_contest']);
    }
    
    $update = array('team_type_id' => $team_type_id,
        'payment_id' => $payment_id,
        'grade' => $grade,
        'reg_grade' => $reg_grade,
        'is_payment' => $is_payment,
        'reg_number' => $reg_number,
        'number' => $number,
        'contest_day' => $contest_day,
        'smena' => $smena,
        'payment_date' => $date,
        'reposts' => $reposts,
        'comment' => $comment,
        'payment_sum' => $payment_sum);
    db_update('team', $update, "`id`=$id").'; ';
    
    $exist_pupils = gate_array_column(pupil_list_by_team_id($id), 'FIO', 'idOfPupil_team');
    $pupils_count = count($pupils);
    $number=1;
    for ($i=0; $i<$pupils_count; $i++){
        if ($pupils[$i]['pupil_team_id']==''){
            if ($pupils[$i]['FIO']!='""'){
                //добавление нового ученика
                db_insert('pupil', array('FIO'=>$pupils[$i]['FIO'], 
                                         'contests'=>$pupils[$i]['contests'], 
                                         'winners'=>$pupils[$i]['winners'],
                                         'other_contest'=>$pupils[$i]['other_contest']));
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
                db_update('pupil', 
                          array('FIO'=>$pupils[$i]['FIO'], 
                                'contests'=>$pupils[$i]['contests'], 
                                'winners'=>$pupils[$i]['winners'],
                                'other_contest'=>$pupils[$i]['other_contest']),
                          "`id`=$pupil_id");
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
                db_insert('teacher', array('FIO'=>$teachers[$i]['FIO'], 
                                           'contests'=>$teachers[$i]['contests'], 
                                           'winners'=>$teachers[$i]['winners'],
                                           'other_contest'=>$teachers[$i]['other_contest']));
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
                db_update('teacher', 
                          array('FIO'=>$teachers[$i]['FIO'], 
                                'contests'=>$teachers[$i]['contests'], 
                                'winners'=>$teachers[$i]['winners'],
                                'other_contest'=>$teachers[$i]['other_contest']),
                          "`id`=$teacher_id");
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
    $team_type = stripslashes(trim($_POST['team_type']));
    $teachers = $_POST['teachers'];
    $teachers_team = $_POST['teacher_team'];
    $pupils = $_POST['pupils'];
    $pupils_team = $_POST['pupil_team'];
    $grade = stripslashes(trim($_POST['grade']));
    $comment = stripslashes(trim($_POST['comment']));
    $team = team_get_by_id($id);
    $is_payment = $team['is_payment'];
    $payment_id = stripslashes(trim($team['payment_id']));
    $number = $team['number'];
    $contest_day = $team['contest_day'];
    $smena = $team['smena'];
    $date = stripslashes(trim($_POST['date']));
    $reposts = join(';', $_POST['repost']);
    $pupil_contest_count = $_POST['pupil_contest_count'];
    $pupil_winner_count = $_POST['pupil_winner_count'];
    $pupil_contest = $_POST['pupil_contest_value'];
    $pupil_winner = $_POST['pupil_winner_value'];
    $pupil_other_contest = $_POST['pupil_other_contest'];
    $teacher_contest_count = $_POST['teacher_contest_count'];
    $teacher_winner_count = $_POST['teacher_winner_count'];
    $teacher_contest = $_POST['teacher_contest_value'];
    $teacher_winner = $_POST['teacher_winner_value'];
    $teacher_other_contest = $_POST['teacher_other_contest'];
    $payment_sum = stripslashes(trim($_POST['payment_sum']));
    
    if (check_contestbookkeeper_rights($team['contest_id'])) {
        if ($_POST['is_payment_value']!='')
            $is_payment = stripslashes(trim($_POST['is_payment_value']));
        if ($is_payment == '1' && $_POST['payment_id']!='')
            $payment_id = stripslashes(trim($_POST['payment_id']));
        else {
            $payment_id = '-1';
        }
    }
    if (check_contestadmin_rights() && stripslashes(trim($_POST['number']))!=''){
        $number = stripslashes(trim($_POST['number']));
    }    
    if (check_can_user_edit_teamsmena_field($team)){
        $contest_day = stripslashes(trim($_POST['contest_day']));
        $smena = stripslashes(trim($_POST['smena']));
    }    
    
    $team_type_object = teamType_get_by_id($team_type);
    $reg_grade = $grade + $team_type_object['grade_offset_number'];
    if (($team['reg_grade'] != $reg_grade) && check_can_user_edit_teamgrade_field($team)) {
      $number = db_max('team','number',"`reg_grade`=$reg_grade AND `contest_id`=".$team['contest_id']) + 1;
      $reg_number = $number;
    } else {
      $reg_number = $team['reg_number'];
    }

    if (team_update($id, $team_type, $payment_id, $grade, $reg_grade, $teachers, $teachers_team, 
                    $pupils, $pupils_team, $is_payment, $reg_number, $number, 
                    $contest_day, $smena, $date, $reposts, $pupil_contest_count, 
                    $pupil_winner_count, $pupil_contest, $pupil_winner, 
                    $pupil_other_contest, $teacher_contest_count, $teacher_winner_count, 
                    $teacher_contest, $teacher_winner, $teacher_other_contest, 
                    $payment_sum, $comment)) {
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

    db_delete('team', 'id=' . $id);
    $pupils = pupil_list_by_team_id($id);
    foreach ($pupils as $pupil) {
        pupil_team_delete($pupil['idOfPupil_team']);
    }
    $teachers = teacher_list_by_team_id($id);
    foreach ($teachers as $teacher) {
        teacher_team_delete($teacher['idOfTeacher_team']);
    }
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
      $team_list=array();
      $team_list_by_grade=array();
      $list = team_list('','',$current_contest);
      
      //get array of teams with marks
      foreach ($list as &$team) {
          $team_id = $team['id'];
          $team['mark'] = stripslashes(trim($_POST["mark"]["$team_id"]));
          if ($team['mark'] != '') {
            $team['mark'] = (float)$team['mark'];
            $team_list[] = $team;
          }
          else {
              $sql = "UPDATE `team` SET `mark`=null, `place`=null, `common_place`=null WHERE `id`=$team_id";
              db_query ($sql);
          }          
      }
      
      //sort array of teams
      $tmp = Array(); 
      foreach($team_list as &$tmp_team)
        $tmp[] = &$tmp_team['mark'];
      array_multisort($tmp, SORT_DESC, SORT_NUMERIC, $team_list); 
      
      $team_list[0]['common_place']=1;
      $team_list[0]['place']=1;
      $team_list_by_grade[$team_list[0]['grade']][]=$team_list[0];
      $n = count($team_list);
      for ($i=1; $i<$n; $i++){
          //set common place
          if ($team_list[$i]['mark']<$team_list[$i-1]['mark']){
              $team_list[$i]['common_place']=$i+1;
          }
          else {
              $team_list[$i]['common_place']=$team_list[$i-1]['common_place'];
          }
          
          //set place in grade
          $team_grade = $team_list[$i]['grade'];
          $team_list_by_grade[$team_grade][]=$team_list[$i];
          $team_grade_count = count($team_list_by_grade[$team_grade]);
          if ($team_grade_count == 1){
              $team_list[$i]['place']=1;
              $team_list_by_grade[$team_grade][$team_grade_count - 1]['place']=1;
          }
          else if ($team_list_by_grade[$team_grade][$team_grade_count - 1]['mark']<$team_list_by_grade[$team_grade][$team_grade_count-2]['mark']){
              $team_list[$i]['place']=$team_grade_count;
              $team_list_by_grade[$team_grade][$team_grade_count - 1]['place']=$team_grade_count;
          }
          else {
              $team_list[$i]['place']=$team_list_by_grade[$team_grade][$team_grade_count-2]['place'];
          }              
      }      
      
      foreach($team_list as $team) {
          team_update_results($team['id'], $team['mark'], $team['place'], $team['common_place']);
      }
  }
  
  function team_update_results($id, $mark, $place, $common_place){
      $update = array('mark' => $mark, 'place' => $place, 'common_place'=>$common_place);
      db_update('team', $update, "`id`=$id");
  }
  
  function team_update_service_received($contest_id='') {
      global $current_contest;
      if ($contest_id==''){
          $contest_id = $current_contest;
      }
      $list = team_list('','',$current_contest);
      
      //get array of teams with marks
      foreach ($list as $team) {
          $team_id = $team['id'];
          $service = stripslashes(trim($_POST["service"]["$team_id"]));
          $update = array('service' => db_string($service));
          db_update('team', $update, "`id`=$team_id");
      }
  }
}  
?>
