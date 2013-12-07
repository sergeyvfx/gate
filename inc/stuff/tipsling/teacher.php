<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

  function teacher_list_by_team_id($team_id = -1) {
    $sql = "SELECT\n"
            . " teacher.*, "
            . " teacher_team.id as idOfTeacher_team \n"
            . "FROM\n"
            . " teacher, teacher_team \n"
            . "WHERE\n"
            . " teacher_team.teacher_id=teacher.id AND\n"
            . " teacher_team.team_id = " . $team_id ."\n"
            . " ORDER BY\n"
            . " teacher_team.number ASC";

    return arr_from_query($sql);
  }
  
  function teacher_list_by_responsible_id($user_id = -1) {
    $sql = "SELECT\n"
            . " teacher.*, "
            . "FROM\n"
            . " teacher, teacher_team, team \n"
            . "WHERE\n"
            . " teacher_team.teacher_id=teacher.id AND\n"
            . " teacher_team.team_id = team.id AND\n"
            . " team.responsible_id = ".$user_id."\n"
            . " ORDER BY\n"
            . " teacher_team.number ASC";

    return arr_from_query($sql);
  }
  
  function teacher_team_list($teacher_id = -1) {
    $sql = "SELECT * \n"
            . "FROM\n"
            . " teacher_team \n"
            . "WHERE\n"
            . " teacher_team.teacher_id=".$teacher_id;

    return arr_from_query($sql);
  }
  
  function teacher_team_delete($teacher_team_id){
      $teacher_id = db_field_value('teacher_team', 'teacher_id', "`id`=$teacher_team_id");
      //удаление учителя из данной команды
      db_delete('teacher_team', "`id`=$teacher_team_id");
      //если учителя больше нет в других командах, то удаляем его из базы
      $team_list = teacher_team_list($teacher_id);
      if (count($team_list)==0){
          db_delete('teacher', "`id`=$teacher_id");
      }
  }
?>
