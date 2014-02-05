<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include '../../../../globals.php';
include $DOCUMENT_ROOT . '/inc/include.php';

$file_names = array();
if ($_POST['files'] != '')
    $file_names = explode('|',$_POST['files']);

$logfile = isset($_POST['logfile']) ? htmlspecialchars(stripslashes($_POST['logfile'])) : '';
$logfile = iconv("UTF-8","WINDOWS-1251",$logfile);
$folder = isset($_POST['folder']) ? htmlspecialchars(stripslashes($_POST['folder'])) : '';
$to = isset($_POST['address']) ? htmlspecialchars(stripslashes($_POST['address'])) : '';
$from = isset($_POST['mailsender']) ? htmlspecialchars(stripslashes($_POST['mailsender'])) : ''; 
$subject = isset($_POST['mailsubject']) ? htmlspecialchars(stripslashes($_POST['mailsubject'])) : ''; 
$message = isset($_POST['mailtext']) ? htmlspecialchars(stripslashes($_POST['mailtext'])) : ''; 
$headers = "Content-type: text/plain; charset=\"utf-8\"\r\n"; 
$headers .= "From: <". $from .">\r\n"; 
$headers .= "MIME-Version: 1.0\r\n"; 
$headers .= "Date: ". date('D, d M Y h:i:s O') ."\r\n"; 

// Отправляем почтовое сообщение 
if(count($file_names) == 0)
{
    if(!mail($to, $subject, $message, $headers)) 
    { 
        $msg = $to.". К сожалению, письмо не отправлено.\n";        
    }
    else
    {
        $msg = $to.". Письмо успешно отправлено.\n";
    }
    file_put_contents('log/'.$logfile, $msg, FILE_APPEND|LOCK_EX);
}
else 
{
    send_mail($to, $from, $subject, $message, $file_names, $folder, $logfile);
}

// Вспомогательная функция для отправки почтового сообщения с вложением 
function send_mail($to, $from, $subject, $message, $files, $folder, $logfile) 
{
    $boundary = "--".md5(uniqid(time())); // генерируем пароль!!!!! 
    
    $headers = "Content-type: text/plain; charset=\"utf-8\"\r\n"; 
    $headers .= "From: <". $from .">\r\n"; 
    $headers .= "MIME-Version: 1.0\r\n"; 
    $headers .= "Date: ". date('D, d M Y h:i:s O') ."\r\n"; 
    
    $headers .= "MIME-Version: 1.0\n"; 
    $headers .= "Date: ". date('D, d M Y h:i:s O') ."\r\n"; 
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\n"; 
    $headers .= "From: <". $from .">\r\n"; 
    
    $multipart .= "--$boundary\n"; 
    $multipart .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
    $multipart .= "Content-Transfer-Encoding: Quot-Printed\n\n"; 
    $multipart .= "$message\n\n";
    
    $message_part = "";
    $cnt = count($files);
    for($i = 0; $i < $cnt; ++$i) 
    {
        $fp = fopen($folder.$files[$i],"r"); 
        if (!$fp) 
        { 
            $msg = $to.". Файл ".$files[$i]." не может быть прочитан.\n";
            file_put_contents('log/'.$logfile, $msg, FILE_APPEND|LOCK_EX);
            exit(); 
        } 
        $file = fread($fp, filesize($folder.$files[$i])); 
        fclose($fp); 

        $message_part .= "--$boundary\n";
        $message_part .= "Content-Type: application/octet-stream\n"; 
        $message_part .= "Content-Transfer-Encoding: base64\n"; 
        $message_part .= "Content-Disposition: attachment; filename = \"".$files[$i]."\"\n\n"; 
        $message_part .= chunk_split(base64_encode($file))."\n"; 
    }    
    $multipart .= $message_part."--$boundary--\n";
    
    if(!mail($to, $subject, $multipart, $headers)) 
    { 
        $msg = $to.". К сожалению, письмо не отправлено.\n";
    }
    else
    {
        $msg = $to.". Письмо успешно отправлено.\n";
    }
    file_put_contents('log/'.$logfile, $msg, FILE_APPEND|LOCK_EX);
} 
?>
