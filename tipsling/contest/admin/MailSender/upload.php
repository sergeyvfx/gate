<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

 $target = "files/"; 
 $target = $target . basename( $_FILES['uploaded']['name']) ; 
 if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) 
 {
 echo "Файл ". basename( $_FILES['uploadedfile']['name']). " успешно загружен";
 } 
 else {
 echo "Извините, произошла ошибка при загрузке вашего файла.";
 }
?>
