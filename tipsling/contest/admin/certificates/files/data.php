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
  header('Location: ../../../../../login');
}

if ($current_contest=='' || $current_contest==-1)
    header('Location: ../../choose');

$contest = contest_get_by_id($current_contest);


?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$contest['name']?></a><a href="<?= config_get('document-root') . "/tipsling/contest/admin" ?>">Администрирование</a><a href="<?= config_get('document-root') . "/tipsling/contest/admin/certificates" ?>">Сертификаты</a>Файлы</div>
${information}

<div class="form">
  <div class="content">    
    <?php
      global $DOCUMENT_ROOT, $action;
      include '../../menu.php';
      include '../menu.php';
    
      $admin_menu->SetActive('Certificates');
      $admin_menu->Draw();
    
      $certificate_menu->SetActive('Files');
      $certificate_menu->Draw();
    ?>
    <form action=".?action=upload" method="post" enctype="multipart/form-data">
      <input type="file" name="filename"/> 
      <input type="submit" value="Загрузить"/>
    </form>
    <?php
      if ($action=='upload')
      {
        include 'upload.php';
      }
      include 'list.php';
    ?>
  </div>
</div>
