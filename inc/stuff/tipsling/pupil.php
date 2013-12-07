<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

  function pupil_list_by_team_id($team_id = -1) {
    $sql = "SELECT\n"
            . " pupil.*, "
            . " pupil_team.id as idOfPupil_team "
            . "FROM\n"
            . " pupil, pupil_team \n"
            . "WHERE\n"
            . " pupil_team.pupil_id=pupil.id AND\n"
            . " pupil_team.team_id = " . $team_id ."\n"
            . " ORDER BY\n"
            . " pupil_team.number ASC";

    return arr_from_query($sql);
  }
  
  
  function pupil_list_by_responsible_id($user_id = -1) {
    $sql = "SELECT DISTINCT\n"
            . " pupil.*"
            . "FROM\n"
            . " pupil, pupil_team, team \n"
            . "WHERE\n"
            . " pupil_team.pupil_id=pupil.id AND\n"
            . " pupil_team.team_id = team.id AND\n"
            . " team.responsible_id = ".$user_id."\n"
            . " ORDER BY\n"
            . " pupil_team.number ASC";

    return arr_from_query($sql);
  }
  
  function pupil_team_list($pupil_id = -1) {
    $sql = "SELECT * \n"
            . "FROM\n"
            . " pupil_team \n"
            . "WHERE\n"
            . " pupil_team.pupil_id=".$pupil_id;

    return arr_from_query($sql);
  }
  
  function pupil_team_delete($pupil_team_id){
      $pupil_id = db_field_value('pupil_team', 'pupil_id', "`id`=$pupil_team_id");
      //удаление ученика из данной команды
      db_delete('pupil_team', "`id`=$pupil_team_id");
      //если ученика больше нет в других командах, то удаляем его из базы
      $team_list = pupil_team_list($pupil_id);
      if (count($team_list)==0){
          db_delete('pupil', "`id`=$pupil_id");
      }
  }
  
?>
