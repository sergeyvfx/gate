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
}

$it = contest_get_by_id($current_contest);
$query = arr_from_query("select * from Admin_FamilyContest ".
                   "where family_contest_id=".$it['family_id']." and ".
                   "user_id=".user_id());
if (count ($query) <= 0)
{
  print (content_error_page(403));
  return;
}

?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest" ?>"><?=$it['name']?></a><a>Администрирование</a>Результаты</div>
${information}
<div class="form">
  <div class="content">
    <?php
    include '../menu.php';
    $admin_menu->SetActive('Results');
    
    global $DOCUMENT_ROOT, $action;
       
    $admin_menu->Draw();

    if ($action == 'save') {
        team_update_results_received($list);
    }
    $list = team_list('','',$current_contest);
    include 'list.php';
    ?>
  </div>
</div>
