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
                $sql = "select * from certificate where family_id=".$c['family_id']." order by id";
                $q = db_query($sql);
                $percent = 80/count($q)+1;
                while ($r = mysql_fetch_array($q))
                    echo('<th width="'.$percent.'" style="text-align:center;">'.$r['name'].'</th>');
                echo('<th width="12%"></th></tr>');
            
                $teams = team_list("", "", $current_contest, $filter);
                $n = count($teams);
                $i=0;
                while ($i<$n)
                {
                    $team = $teams[$i];
                    echo('<tr name="'.$i.'"><td>'.$team["grade"].'.'.$team["number"].'</td>');
                    $q = db_query($sql);
                    while ($r = mysql_fetch_array($q))
                    {
                        $is_show=($r['id']==1 || $r['id']==2 || (($r['id']==3 || $r['id']==4) && $team['grade']>1 && $team['grade']<12 && $team['place']>0 && $team['place']<4) || ($r['id']==5 && $team['grade']<12) || ($r['id']==6 && $team['grade']<12 && $team['place']>0 && $team['place']<4));
                        $input = $is_show?'<input type="checkbox" name="'.$team["id"].'.'.$r["id"].'" id="'.$team["id"].'.'.$r["id"].'"></input>':'';
                        echo('<td name="'.$r['id'].'">'.$input.'</td>');
                    }
                    echo('<td><a style="cursor:pointer;" onclick="check_all_in_row('.$i.');">Выбрать все</a><br/><a style="cursor:pointer;" onclick="uncheck_all_in_row('.$i.');">Снять все</a></td> </tr>');
                    $i++;
                }
                echo('<tr><td/>');
                $q = db_query($sql);
                while ($r = mysql_fetch_array($q))
                    echo('<td style="padding:5px 5px 5px 5px;"><a style="cursor:pointer;" onclick="check_all_in_column('.$r["id"].');">Выбрать&nbspвсе</a> / <a style="cursor:pointer;" onclick="uncheck_all_in_column('.$r["id"].');">Снять&nbspвсе</a></td>');
                echo('</tr>');
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