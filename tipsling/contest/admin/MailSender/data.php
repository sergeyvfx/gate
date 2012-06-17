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
<script src="<?= config_get('document-root') . "/scripts/jquery/jquery-latest.js"?>" type="text/javascript" language="javascript"></script>
<script src="<?= config_get('document-root') . "/scripts/jquery/jquery.MultiFile.js"?>" type="text/javascript" language="javascript"></script>

<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest" ?>"><?=$it['name']?></a><a>Администрирование</a>Рассылка писем</div>
${information}

<script language="JavaScript" type="text/JavaScript">
  
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
  
  function StartSending()
  {
      var subject = getElementById('mailsubject').value;
      var message = getElementById('mailtext').value;
      var sender = getElementById('mailsender').value;
      var addr = getElementById('mailaddress').value;
      var addresses = addr.split(/[\s,]+/);
      var n = addresses.count;
      var i=0;
      for (var key in addresses) 
      {
          $("#progressbar").style.display = 'block';
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
                alert("письмо "+i+" отправлено");
            }
          }
          xmlhttp.open("GET","SendLetter.php?address="+key+"&subject="+subject+"&text="+message+"&sender="+sender,true);
          xmlhttp.send();
          $("#progressbar").progressbar({ value: i/n*100 });
          i++;
      }
      $("#progressbar").progressbar({ value: 100 });
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
        include 'SendLetter.php';
    }
    formo('title=Отправка письма');
    ?>
    <form action=".?action=send" method="POST" onsubmit="check (this); return false;" enctype="multipart/form-data">
      <table class="clear" width="100%">
        <tr>
            <td width="100%">
                <table width="100%">
                    <tr width="100%">
                    <td width="35%" style="padding: 0 7px;">
                        <table width="100%">
                            <tr width="100%">
                                <td width="105px">E-mail отправителя:\n(Адреса должны быть разделены пробелом или переносом строки)</td>
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
                            <table width="100%" height="100%">
                                <tr width="100%" style="vertical-align: top;"> <td width="100%">Список рассылки:</td> </tr>
                                <tr width="100%" style="vertical-align: top;"> <td width="100%"> <textarea width="100%" rows="29" id="mailaddress" name="mailaddress" onblur="check_frm_email ();" class="txt block" style="resize:none;"><?= $_POST['mailaddress']; ?></textarea></td></tr>
                                <tr width="100%" style="vertical-align: top;"> <td width="100%"> 
                                    <input type="button" value="импорт из базы" onclick="LoadAddressFromDatabase ()"/>
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
      <!--<button class="submitBtn block" type="button" onclick="StartSending ()">Отправить</button>-->
      <button class="submitBtn block" type="submit">Отправить</button>
      <div id="progressbar" style="display: none;"></div>
    </div>    
    </form>
<?php
  formc ();
?>
  </div>
</div>