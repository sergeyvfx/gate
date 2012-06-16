<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$cnt = count($_FILES['mail_file']['name']);
$picture = array(); 
if($cnt > 0) 
{
    for($i = 0; $i < $cnt; ++$i) 
    {
        // Если поле выбора вложения не пустое - закачиваем его на сервер 
        if (!empty($_FILES['mail_file']['tmp_name'][$i])) 
        { 
            // Закачиваем файл 
            $path = 'files/'.$_FILES['mail_file']['name'][$i];
            //$path = dirname($_SERVER['PHP_SELF']).'/files/'.$_FILES['mail_file']['name'][$i];
            
            if (move_uploaded_file($_FILES['mail_file']['tmp_name'][$i], $path)) 
            {
                $picture[$i] = $_FILES['mail_file']['name'][$i]; 
            }
        }
    }
}


$addresses = preg_split("/[\s,]+/", $_POST['mailaddress']);
foreach ($addresses as $value) 
{
    if (trim($value)!='')
    {
        $to = htmlspecialchars(stripslashes($value));
        $from = isset($_POST['mailsender']) ? htmlspecialchars(stripslashes($_POST['mailsender'])) : ''; 
        $subject = isset($_POST['mailsubject']) ? htmlspecialchars(stripslashes($_POST['mailsubject'])) : ''; 
        $message = isset($_POST['mailtext']) ? htmlspecialchars(stripslashes($_POST['mailtext'])) : ''; 

        // Отправляем почтовое сообщение 
        if(count($picture) == 0)
            mail ($to, $subject, $message, "Content-type: text/plain\nFrom:".$from);
        else send_mail($to, $from, $subject, $message, $picture); 
    }
}

// Вспомогательная функция для отправки почтового сообщения с вложением 
function send_mail($to, $from, $subject, $message, $files) 
{
    $boundary = "--".md5(uniqid(time())); // генерируем пароль!!!!! 
    // echo "$boundary"; 
    $headers .= "MIME-Version: 1.0\n"; 
    $headers .="Content-Type: multipart/mixed; boundary=\"$boundary\"\n"; 
    $headers .="From:".$from."\r\n";
    
    $multipart .= "--$boundary\n"; 
    $multipart .= "Content-Type: text/plain\n"; 
    $multipart .= "Content-Transfer-Encoding: Quot-Printed\n\n"; 
    $multipart .= "$message\n\n";
    
    $cnt = count($files);
    for($i = 0; $i < $cnt; ++$i) 
    {
        echo "<center><br>".$files[$i]."<br></center>";
        $fp = fopen('files/'.$files[$i],"r"); 
        if (!$fp) 
        { 
            print "Файл $files[$i] не может быть прочитан"; 
            exit(); 
        } 
        $file = fread($fp, filesize('files/'.$files[$i])); 
        fclose($fp); 

        $message_part = "--$boundary\n";
        $message_part .= "Content-Type: application/octet-stream\n"; 
        $message_part .= "Content-Transfer-Encoding: base64\n"; 
        $message_part .= "Content-Disposition: attachment; filename = \"".$files[$i]."\"\n\n"; 
        $message_part .= chunk_split(base64_encode($file))."\n"; 
    }    
    $multipart .= $message_part."--$boundary--\n";
    
    if(!mail($to, $subject, $multipart, $headers)) 
    { 
        echo "К сожалению, письмо не отправлено"; 
        exit();
    } 
} 
?>
