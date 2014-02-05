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

global $current_contest, $document_root;

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
<script src="<?= config_get('document-root') . "/scripts/jquery.MultiFile.js"?>" type="text/javascript" language="javascript"></script>

<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest" ?>"><?=$it['name']?></a><a>Администрирование</a>Рассылка писем</div>
${information}

<script language="JavaScript" type="text/JavaScript">  
    function LoadAddressFromDatabase(param)
    {
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {        
            if (xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                document.getElementById("mailaddress").innerHTML+=xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","LoadAddressFromDatabase.php?param="+param,true);
        xmlhttp.send();
    }
    
    function GetDateString(date)
    {
        var day = ''+date.getDate();
        var month = ''+(date.getMonth()+1);
        var year = ''+date.getFullYear();
        var hours = ''+date.getHours();
        var minutes = ''+date.getMinutes();
        
        if (day.length==1){
            day = '0'+day;
        }
        if (month.length==1){
            month = '0'+month;
        }
        if (hours.length==1){
            hours = '0'+hours;
        }
        if (minutes.length==1){
            minutes = '0'+minutes;
        }
        
        return day+'-'+month+'-'+year+' '+hours+'-'+minutes;
    }
  
    function StartSending()
    {      
        var intervalID,
            files = $('#filenames').val(),
            folder = $('#folder').val(),
            subject = $("#mailsubject").val(),
            message = $("#mailtext").val(),
            sender = $("#mailsender").val(),
            addr = $("#mailaddress").val(),
            logfile = '['+GetDateString(new Date())+'] '+subject+'.txt',
            addresses = addr.split(/[\s,]+/),
            n = addresses.length,
            i=0,
            sendOneLetter = function(){
                if (addresses[i]!=""){
                    $.post(
                        "SendLetter.php",
                        {
                            address:addresses[i],
                            mailsubject:subject,
                            mailtext:message,
                            mailsender:sender,
                            files:files,
                            folder:folder,
                            logfile:logfile
                        }
                    );
                }
                i++;          
                $("#progressbar").progressbar({ value: i/n*100 });
                if (i>=n){
                      clearInterval(intervalID);
                      alert('Отправка писем завершена');
                      location="./";
                }
            };
        
        $('#progressbar').progressbar({ value: 0 });
        intervalID = setInterval(sendOneLetter, 1000);
    }
    
    $(function(){
        if ($('#progressbar').size()){
            StartSending();
        }
    });
</script>

<div class="form">
  <div class="content">
    <?php
    global $action, $current_contest;
    
    include '../menu.php';
    $admin_menu->SetActive('MailSender');
    
    $admin_menu->Draw();
    
    if ($action=='send')
    {
        //include 'SendLetter.php';
        include 'UploadingFiles.php';
    }
    formo('title=Отправка письма');
    
    ?>
    <form id="myform" method="POST" enctype="multipart/form-data" action=".?action=send">
      <table class="clear" width="100%">
        <tr>
            <td width="100%">
                <table width="100%">
                    <tr width="100%">
                    <td width="35%" style="padding: 0 7px;">
                        <table width="100%">
                            <tr width="100%">
                                <td width="105px">E-mail отправителя:</td>
                                <td> <input type="text" id="mailsender" name="mailsender" value="<?= $_POST['mailsender']; ?>" class="txt block"/> </td>
                            </tr>
                        </table>
                    </td>
                    <td width="65%" style="padding: 0 7px;">
                        <table width="100%">
                            <tr width="100%">
                                <td width="70px">Тема письма:</td>
                                <td> <input type="text" id="mailsubject" name="mailsubject" value="<?= $_POST['mailsubject']; ?>" class="txt block"/> </td>
                            </tr>
                        </table>
                    </td>
                    </tr>
                    <tr  width="100%">
                        <td  width="35%" style="padding: 0 7px;">
                            <table width="100%" height="100%">
                                <tr width="100%" style="vertical-align: top;"> <td width="100%">Список рассылки:<br/>(Адреса должны быть разделены пробелом или переносом строки)</td> </tr>
                                <tr width="100%" style="vertical-align: top;"> <td width="100%"> <textarea width="100%" rows="29" id="mailaddress" name="mailaddress" class="txt block" style="resize:none;"><?= $_POST['mailaddress']; ?></textarea></td></tr>
                                <tr width="100%" style="vertical-align: top;"> <td width="100%"> 
                                    <input type="button" value="импорт всех пользователей" onclick="LoadAddressFromDatabase ('all')"/>
                                    <input type="button" value="импорт ответственных текущего конкурса" onclick="LoadAddressFromDatabase ('contest')"/>
                                </td></tr>
                                <tr><td><br/></td></tr>
                                <tr width="100%" style="vertical-align: top;"> <td width="100%">Файлы:</td> </tr>
                                <tr width="100%" style="vertical-align: bottom;"> <td width="100%">
                                   <input type="file" name="mail_file[]" class="multi"/>
                                </td></tr>
                            </table>
                        </td>
                        <td  width="65%" style="padding: 0 7px;">
                            <table width="100%">
                                <tr width="100%"> <td width="100%">Текст письма:</td> </tr>
                                <tr width="100%"> <td width="100%"> <textarea width="100%" rows="40" id="mailtext" name="mailtext" class="txt block" style="resize:none;"><?= $_POST['mailtext']; ?></textarea></td></tr>
                            </table>
                        </td>
                    </tr>                        
                </table>
            </td>
        </tr>
    </table>
    <div id="hr"></div>
    
    <div class="formPast">
      <button class="submitBtn block" type="submit">Отправить</button>
      <?php  
        if ($action=='send') {
            echo '<div id="progressbar"></div>';
            echo '<input type="hidden" id="filenames" value="'.$file_names.'"></div>';
            echo '<input type="hidden" id="folder" value="'.$folder.'"></div>';
        }
      ?>
    </div>    
    </form>
<?php
  formc ();
?>
  </div>
</div>