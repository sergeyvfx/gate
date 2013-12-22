<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

if ($PHP_SELF != '') {
  print ('HACKERS?');
  die;
}

formo('title=Список файлов');
    
  $pages = new CVCPagintation ();
  $pages->Init('', 'bottomPages=false;skiponcepage=true;');
    
  $pageSrc = '<table class="list">' . "\n";
  $pageSrc .= '<tr class="h">
                <th width="30%" style="text-align: center;">Имя файла</th>
                <th width="70%" style="text-align: center;">Файл</th>
                <th width="48" class="last">&nbsp;</th>
              </tr>';

  $folder = opendir($directory);
  while($file = readdir($folder)) 
  {
    if ($file != "." && $file != ".." ) 
    {
      $substr = strtolower(substr($file,1 + strrpos($file, ".")));
      if ($substr == "jpg" OR $substr == "gif" OR $substr == "png" OR $substr == "bmp")
      {
          $pageSrc .= '<tr>' .
                      '<td align="center">' . $file . '</td>' .
                      '<td align="center">'.
                        '<img src="'.config_get('document-root').'/uploaded_files/certificate/'.$file.'" alt="">'.
                      '</td>'.
                      '<td align="right">' .
                        stencil_ibtnav('cross.gif', '?action=delete&filename='.$file, 'Удалить файл', 'Удалить файл '.$file.'?') .
                      '</td></tr>';
      }  
    }
  }
  $pageSrc .= '</table>' . "\n";
  $pages->AppendPage($pageSrc);
  
  $pages->Draw();


formc ();
?>
