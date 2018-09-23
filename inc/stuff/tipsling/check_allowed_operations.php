<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $current_contest;

function check_create_team_allow($contest_id='') {    
    if ($contest_id == '')
        $contest_id = $current_contest;
    $contest_status = get_contest_status($contest_id);
    return ($contest_status & 1) == 1 && ($contest_status & 2) == 0;
}

function check_edit_team_allow($contest_id='') {    
    if ($contest_id == '')
        $contest_id = $current_contest;
    $contest_status = get_contest_status($contest_id);
    return ($contest_status & 1) == 1 && ($contest_status & 8) == 0;;
}

function check_is_team_owner($team, $user_id='') {    
    if ($user_id == '')
        $user_id = user_id();
    return $user_id==$team['responsible_id'];
}

function check_contestadmin_rights() {    
    $g = group_get_by_name("Администраторы");
    return is_user_in_group(user_id(), $g['id']) || user_access_root();      
}

function check_contestbookkeeper_rights($contest_id='') {    
    if ($contest_id == '')
        $contest_id = $current_contest;
    return is_user_bookkeeper(user_id(), $contest_id) || check_contestadmin_rights($contest_id);      
}

function check_can_user_edit_team($team, $user_id='') {    
    return check_edit_team_allow($team['contest_id']) && 
          (check_is_team_owner($team, $user_id) || check_contestbookkeeper_rights($team['contest_id']));
}

function check_can_user_edit_teamgrade_field($team) {    
    return (check_create_team_allow($team['contest_id']) && !$team['is_payment'])
        || check_contestadmin_rights($team['contest_id']);
}

function check_can_user_edit_teamsmena_field($team) {    
    $contest_status = get_contest_status($team['contest_id']);
    return (($contest_status & 1) == 1 && ($contest_status & 4) == 0) || check_contestadmin_rights($team['contest_id']);
}
?>
