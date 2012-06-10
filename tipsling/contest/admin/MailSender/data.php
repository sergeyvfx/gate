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

<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest" ?>"><?=$it['name']?></a><a>Администрирование</a>Рассылка писем</div>
${information}

<script language="JavaScript" type="text/JavaScript">
  function check () {
    return true;
  }

  function check_frm_email () {
    
  }
  
  function LoadAddressFromDatabase()
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
          document.getElementById("mailaddress").innerHTML=xmlhttp.responseText;
      }
    }
    xmlhttp.open("GET","LoadAddressFromDatabase.php",true);
    xmlhttp.send();
  }
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
        $addresses = preg_split("/[\s,]+/", $_POST['mailaddress']);
        foreach ($addresses as $value) 
        {
            if (trim($value)!='')
            {
                $to = $value;
                $subject = $_POST['mailsubject'];
                $message = $_POST['mailmessage'];
                $additional_headers = 'FROM: '.$_POST['mailsender'];
                mail($to, $subject, $message, $additional_headers);
            }
        }
    }
    //formo('title=Отправка письма');
    ?>
  <form action=".?action=send" method="POST" onsubmit="check (this);">
    <table class="clear" width="100%">
        <tr>
            <td width="100%">
                <table width="100%">
                    <tr width="100%">
                    <td width="35%" style="padding: 0 7px;">
                        <table width="100%">
                            <tr width="100%">
                                <td width="105px">E-mail отправителя:</td>
                                <td> <input type="text" id="mailsender" name="mailsender" onblur="check_frm_email ();" value="<?= $_POST['mailsender']; ?>" class="txt block"/> </td>
                            </tr>
                        </table>
                    </td>
                    <td width="65%" style="padding: 0 7px;">
                        <table width="100%">
                            <tr width="100%">
                                <td width="70px">Тема письма:</td>
                                <td> <input type="text" id="mailsubject" name="mailsubject" onblur="check_frm_email ();" value="<?= $_POST['mailsubject']; ?>" class="txt block"/> </td>
                            </tr>
                        </table>
                    </td>
                    </tr>
                    <tr  width="100%">
                        <td  width="35%" style="padding: 0 7px;">
                            <table width="100%">
                                <tr width="100%"> <td width="100%">Список рассылки:</td> </tr>
                                <tr width="100%"> <td width="100%"> <textarea width="100%" rows="29" id="mailaddress" name="mailaddress" onblur="check_frm_email ();" class="txt block" style="resize:none;"><?= $_POST['mailaddress']; ?></textarea></td></tr>
                                <tr width="100%"> <td width="100%"> 
                                    <!--<input type="button" value="импорт из файла" onclick="LoadAddressFromFile ()"/>-->
                                    <input type="button" value="импорт из базы" onclick="LoadAddressFromDatabase ()"/></td></tr>
                                <tr width="100%"> <td width="100%">Файлы:</td> </tr>
                                <tr width="100%"> <td width="100%"> <select multiple size="5" id="mailfile" name="mailfile" style="width: 100%;"></select> </td></tr>
                                <tr width="100%"> <td width="100%"> <input type="button" width="100%" value="Загрузить файл" onclick="LoadFile ()"/></td></tr>
                            </table>
                        </td>
                        <td  width="65%" style="padding: 0 7px;">
                            <table width="100%">
                                <tr width="100%"> <td width="100%">Текст письма:</td> </tr>
                                <tr width="100%"> <td width="100%"> <textarea width="100%" rows="40" id="mailtext" name="mailtext" onblur="check_frm_email ();" class="txt block" style="resize:none;"><?= $_POST['mailtext']; ?></textarea></td></tr>
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
    </div>
  </form>      
<?php
  //formc ();
?>
  </div>
</div>