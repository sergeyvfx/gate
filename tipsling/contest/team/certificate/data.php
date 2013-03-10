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

global $current_contest;

if (!user_authorized ()) {
  header('Location: ../../../login');
}

if (!is_responsible(user_id())) {
  print (content_error_page(403));
  return;
}

if (!is_responsible_has_school(user_id())) {
  redirect(config_get('document-root') . '/login/profile/info/school/?noschool=1');
}

if ($current_contest=='' || $current_contest==-1)
    header('Location: ../../choose');


$contest = contest_get_by_id($current_contest);
?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$contest['name']?></a><a href="<?= config_get('document-root') . "/tipsling/contest/team" ?>">Команды</a>Сертификаты</div>
${information}

<div class="form">
  <div class="content">    
    <?php
    global $DOCUMENT_ROOT, $action, $certificate, $team, $id;
    include '../menu.php';
    
    $team_menu->SetActive('certificate');
    $team_menu->Draw();
    formo('title=Генерация сертификатов;');
    ?>
    <form action=".?action=save&id=<?= $id; ?>&<?= (($page != '') ? ('&page=' . $page) : ('')); ?>" method="POST" onsubmit="check (this); return false;">  
        <table class="clear" width="100%">
            <tr>
                <td width="120px">Тип сертификата: </td>
                <td>
                    <select id="select_type" name="select_type" class="block" onchange="select_type_changed();">
                        <?php
                            $sql = 'select * from certificate where actual = 1 order by id';
                            $result = db_query($sql);
                            while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
                                echo('<option value="'.$rows["id"].'">'.$rows['name'].'</option>');
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <table class="clear" width="100%">
            <tr>
                <td width="120px">Сертификат для:</td>
                <td>
                    <select id="select_value" name="select_value" class="block"></select>
                </td>
            </tr>
        </table>
        <div width = "100%" class="formPast">
            <button width = "100%" class="submitBtn block" type="button" onclick="cert_generate();">Генерировать</button>
        </div>
    </form>
  </div>
</div>


<script type="text/JavaScript"  language="JavaScript">
    $(document).ready(function(){
        ipc_send_request ('/', 'ipc=find_values&type='+$('#select_type').val(), update_value_select);
    })

function cert_generate()
  {    
    var certificate = $('#select_type').val();
    var param = $('#select_value').val();
    if (param!="undefined")
    {
        var url = './download/?certificate='+certificate+'&param='+param;
        nav(url);        
    }
  }

  function select_type_changed()
  {    
    ipc_send_request ('/', 'ipc=find_values&type='+$('#select_type').val(), update_value_select);
  }
    
  function update_value_select(http_request)
  {
      if (http_request.readyState == 4) {
          $('#select_value').html(http_request.responseText);
    }
  }  
</script>