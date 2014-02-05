<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $file_names, $folder;

$folder = $DOCUMENT_ROOT.'/uploaded_files/email_attachment/';
$cnt = count($_FILES['mail_file']['name']);
$file_names = ''; 
if($cnt > 0) 
{
    for($i = 0; $i < $cnt; ++$i) 
    {        
        // Если поле выбора вложения не пустое - закачиваем его на сервер 
        if (!empty($_FILES['mail_file']['tmp_name'][$i])) 
        { 
            // Закачиваем файл 
            $path = $folder.$_FILES['mail_file']['name'][$i];
            if (move_uploaded_file($_FILES['mail_file']['tmp_name'][$i], $path)) 
            {
                $file_names .= $_FILES['mail_file']['name'][$i].'|'; 
            }
        }            
    }
    $file_names = substr($file_names, 0, strlen($file_names)-1);
}
?>
