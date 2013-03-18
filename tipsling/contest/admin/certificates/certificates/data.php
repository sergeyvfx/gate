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
  header('Location: ../../../../../login');
}

if ($current_contest=='' || $current_contest==-1)
    header('Location: ../../choose');

$contest = contest_get_by_id($current_contest);


?>
<div id="snavigator"><a href="<?= config_get('document-root') . "/tipsling/contest/" ?>"><?=$contest['name']?></a><a href="<?= config_get('document-root') . "/tipsling/contest/admin" ?>">Администрирование</a><a href="<?= config_get('document-root') . "/tipsling/contest/admin/certificates" ?>">Сертификаты</a>Генерировать</div>
${information}

<div class="form">
  <div class="content">    
    <?php
    global $DOCUMENT_ROOT, $action, $certificate, $team, $id, $filter;
    include '../../menu.php';
    include '../menu.php';
    
    $admin_menu->SetActive('Certificates');
    $admin_menu->Draw();
    
    $certificate_menu->SetActive('Certificates');
    $certificate_menu->Draw();
    ?>
    <div class="f" style="margin: 6px -6px 6px;">
        <form action="." method="POST" onsubmit="update (); return false;" onkeypress="if (event.keyCode==13) update ();">
            <b>Фильтр: &nbsp;</b>
            <select id="filterGroup" onchange="update()">
                <option value="1" <?=($filter == 1) ? ('selected') : ('')?>>Все команды</option>
                <option value="2" <?=($filter == 2) ? ('selected') : ('')?>>Победители</option>
            </select>
        </form>
    </div>

    <?php
    formo('title=Генерация сертификатов;');
    ?>
    <form action=".?action=save&id=<?= $id; ?>&<?= (($page != '') ? ('&page=' . $page) : ('')); ?>" method="POST" onsubmit="check (this); return false;">  
        <table id="list_table" class="list" width="100%" style="text-align:center;">
            <?php
                echo('<tr class="h"><th width="8%" style="text-align:center;">Номер команды</th>');
                $c = contest_get_by_id($current_contest);
                $sql = "select * from certificate where actual=1 and family_id=".$c['family_id']." order by id";
                $q = db_query($sql);
                $percent = 80/count($q)+1;
                $certificate_teams_array = array();
                while ($r = mysql_fetch_array($q)){
                    echo('<th width="'.$percent.'" style="text-align:center;">'.$r['name'].'</th>');
                    
                    $certificate_sql = certificate_get_sql($r['id'], $current_contest, '', false);
                    $result_sql = db_query($certificate_sql);
                    $array = array();
                    while ($row = db_row($result_sql)) $array[] = $row;
                    $certificate_teams_array[count($certificate_teams_array)] = array('teams'=>$array,
                                                                                      'cert_id'=>$r['id']);
                }
                echo('<th width="12%"></th></tr>');
                
                echo('<tr><td/>');
                $q = db_query($sql);
                while ($r = mysql_fetch_array($q))
                    echo('<td style="padding:5px 5px 5px 5px;"><a style="cursor:pointer;" onclick="check_all_in_column('.$r["id"].');">Выбрать&nbspвсе</a> / <a style="cursor:pointer;" onclick="uncheck_all_in_column('.$r["id"].');">Снять&nbspвсе</a></td>');
                echo('</tr>');
            
                $teams = team_list("", "", $current_contest, $filter);
                $n = count($teams);
                $i=0;
                while ($i<$n)
                {
                    $team = $teams[$i];
                    echo('<tr name="'.$i.'"><td>'.$team["grade"].'.'.$team["number"].'</td>');
                    
                    foreach ($certificate_teams_array as $certificate_teams) {
                        $is_show = false;
                        foreach ($certificate_teams['teams'] as $value) {
                            if ($value['id_команды']==$team['id']){
                                $is_show = true;
                                break;
                            }                                
                        }
                        $input = $is_show?'<input type="checkbox" name="'.$team["id"].'.'.$certificate_teams['cert_id'].'" id="'.$team["id"].'.'.$certificate_teams['cert_id'].'"></input>':'';
                        echo('<td name="'.$certificate_teams['cert_id'].'">'.$input.'</td>');
                    }
                    
                    echo('<td><a style="cursor:pointer;" onclick="check_all_in_row('.$i.');">Выбрать все</a><br/><a style="cursor:pointer;" onclick="uncheck_all_in_row('.$i.');">Снять все</a></td> </tr>');
                    $i++;
                }                
            ?>            
        </table>
        <div width = "100%" class="formPast">
            <button width = "100%" class="submitBtn block" type="button" onclick="cert_generate();">Генерировать</button>
        </div>
    </form>
  </div>
</div>

<script type="text/JavaScript"  language="JavaScript">

  function update()
  {
      var filter=getElementById('filterGroup').value;
      nav ('.?filter='+filter);
  }

  function check_all_in_column(name)
  {
      $('#list_table tr td[name='+name+'] input').each(function(){
         $(this)[0].checked="true";
      });
  }
  
  function uncheck_all_in_column(name)
  {
      $('#list_table tr td[name='+name+'] input').each(function(){
         $(this)[0].checked="";
      });
  }
  
  function check_all_in_row(name)
  {
      $('#list_table tr[name='+name+'] input').each(function(){
         $(this)[0].checked="true";
      });
  }
  
  function uncheck_all_in_row(name)
  {
      $('#list_table tr[name='+name+'] input').each(function(){
         $(this)[0].checked="";
      });
  }

  function cert_generate()
  {    
      var str = '';
      $('#list_table tr td input:checked').each(function(){
          str += $(this)[0].id + ";";
      });
      
      if (str!="")
      {
        var url = './download/?params='+str;
        nav(url);        
      }
  }
</script>