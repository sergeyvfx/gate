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


function bookkeepers_initialize() 
{
  if (config_get('check-database')) 
  {
    if (!db_table_exists('bookkeepers')) 
    {
      db_create_table('bookkeepers', array(
        'user_id' => 'INT NOT NULL',
        'contestFamily_id' => 'INT NOT NULL'));
    }
  }
}

function is_user_bookkeeper($user_id, $contest_id) {
  $contest = contest_get_by_id($contest_id);
  $family_id = $contest['family_id'];
  
  if (db_count('bookkeepers', "`user_id`=$user_id AND `contestFamily_id`=$family_id") > 0)
    return true;

  return false;
}

?>
