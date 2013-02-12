<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
global $DOCUMENT_ROOT;
   // Проверяем загружен ли файл
   if(is_uploaded_file($_FILES["filename"]["tmp_name"]))
   {
     move_uploaded_file($_FILES["filename"]["tmp_name"], $DOCUMENT_ROOT."/uploaded_files/certificate/".$_FILES["filename"]["name"]);
   }
?>
