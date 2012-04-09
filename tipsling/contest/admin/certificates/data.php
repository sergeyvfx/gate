<?php

/**
 * Gate - Wiki engine and web-interface for WebTester Server
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

global $current_contest;

if (!user_authorized ()) {
  header('Location: ../../../../login');
} else {
  $it = contest_get_by_id($current_contest);
  $query = arr_from_query ("select * from Admin_FamilyContest ".
                     "where family_contest_id=".$it['family_id']." and ".
                     "user_id=".user_id());
  if (count($query) <= 0)
  {
    print (content_error_page(403));
    return;
  }
  else
      header('Location: templates');
}
?>