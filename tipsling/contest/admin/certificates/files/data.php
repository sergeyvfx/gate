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
      global $DOCUMENT_ROOT, $action, $filename;
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
      $directory = $DOCUMENT_ROOT."/uploaded_files/certificate";
      if ($action=='upload')
      {
          // Проверяем загружен ли файл
          if(is_uploaded_file($_FILES["filename"]["tmp_name"]))
          {
              move_uploaded_file($_FILES["filename"]["tmp_name"], $directory."/".$_FILES["filename"]["name"]);
          }
          //include 'upload.php';
      }
      if ($action=='delete')
      {
          $dir = opendir($directory); 
          // считываем содержание директории 
          while(($file = readdir($dir))) 
          { 
              // Если это файл и он равен удаляемому ... 
              if((is_file("$directory/$file")) && ("$directory/$file" == "$directory/$filename")) 
              {
                  // ...удаляем его. 
                  unlink("$directory/$file");                  
              } 
          } 
          // Закрываем дескриптор директории. 
          closedir($dir);
      }
      include 'list.php';
    ?>
  </div>
</div>
