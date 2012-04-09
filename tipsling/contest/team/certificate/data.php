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
                            $sql = 'select * from certificate order by id';
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
                    <select id="select_value" name="select_value" class="block">
                        <?php
                            $sql = 'SELECT DISTINCT
                                        `team`.`pupil1_full_name` as pupil1,
                                        `team`.`pupil2_full_name` as pupil2,
                                        `team`.`pupil3_full_name` as pupil3,
                                        `team`.`grade`,
                                        `team`.`number`,
                                        `team`.`id` as team
                                    FROM
                                        `team`,
                                        `contest`,
                                        `user`,
                                        `responsible`
                                    WHERE
                                        `team`.`responsible_id`=`user`.`id` AND
                                        `responsible`.`user_id`=`user`.`id` AND
                                        `team`.`contest_id`=`contest`.`id` AND
                                        `user`.`id`='.user_id().' AND 
                                        `contest`.`id`='.$current_contest.'
                                    ORDER BY `team`.`grade` ASC, `team`.`number` ASC';
                            $result = db_query($sql);
                            while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
                            {
                                $values .= ';'.$rows['pupil1'].'#'.$rows['team'].'.1';
                                echo('<option value="'.$rows['team'].'.1">'.$rows['pupil1'].'</option>');
                                if (trim($rows['pupil2'])!="")
                                    echo('<option value="'.$rows['team'].'.2">'.$rows['pupil2'].'</option>');
                                if (trim($rows['pupil3'])!="")
                                    echo('<option value="'.$rows['team'].'.3">'.$rows['pupil3'].'</option>');
                            }
                        ?>
                    </select>
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

function cert_generate()
  {    
    var type_select = getElementById("select_type");
    var value_select = getElementById("select_value");
    
    var certificate = type_select.value;
    var param = value_select.value;
    
    if (param!="undefined")
    {
        var url = './download/?certificate='+certificate+'&param='+param;
        nav(url);        
    }
  }

function select_type_changed()
  {    
    var type_select = getElementById("select_type");
    
    ipc_send_request ('/', 'ipc=find_values&type='+type_select.value, update_value_select);
  }
    
  function update_value_select(http_request)
  {
      if (http_request.readyState == 4) {
        var value_select = getElementById("select_value");

        value_select.length=0;
        var values = http_request.responseText.split(";");
        
        for (i = 0; i < values.length; i++){
            if (document.createElement){
                var newListOption = document.createElement("OPTION");
                newListOption.text = values[i].split("#")[0];
                newListOption.value = values[i].split("#")[1];
                // тут мы используем для добавления элемента либо метод IE, либо DOM, которые, alas, не совпадают по параметрам…
                (value_select.options.add) ? value_select.options.add(newListOption) : value_select.add(newListOption, null);
            }else{
                // для NN3.x-4.x
                value_select.options[i] = new Option(values[i].split("#")[0], values[i].split("#")[1], false, false);
            }
        }
    }
  }  
</script>