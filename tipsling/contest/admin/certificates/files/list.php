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
  $i = 0;
  $n = count($file_list);

  
  
  $pageSrc = '<table class="list">' . "\n";
  $pageSrc .= '<tr class="h"><th width="30%" style="text-align: center;">Имя файла</th>
        <th width="70%" style="text-align: center;">Файл</th>';

  $folder = opendir($DOCUMENT_ROOT."/uploaded_files/certificate");
  while($file = readdir($folder)) 
  {
    if ($file[0] != "." && $file[0] != ".." ) 
    {
      $substr = substr($file,1 + strrpos($file, "."));
              
      if ($substr == "jpg" OR $substr == "gif" OR $substr == "png" OR $substr == "bmp")
      {
          $pageSrc .= '<tr' . (($i == $n - 1) ? (' class="last"') : ('')) . '>' .
                      '<td align="center">' . $file . '</td>' .
                      '<td align="center">'.
                      '<img src="'.config_get('document-root').'/uploaded_files/certificate/'.$file.'" alt="">';
      }  
    }
  }
  $pageSrc .= '</table>' . "\n";
  $pages->AppendPage($pageSrc);
  
  $pages->Draw();


formc ();
?>
