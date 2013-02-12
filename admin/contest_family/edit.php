<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Service edtit form generator
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  if ($PHP_SELF != '') {
    print ('HACKERS?');
    die;
  }

  global $id;
  $d = contestFamily_get_by_id($id);
  formo ('title=Информация о конкурсе;');
?>
<script language="JavaScript" type="text/javascript">
  $(document).ready(function(){
    lim_array = new Array();
    var result = getElementById('result_admin').value;
    var matches = result.split(',');
    for (var key in matches)
    {
        var res = matches[key];
        if (res!='')
            lim_array[lim_array.length]=res;
    }    
  });
  
  function check (frm) {
    var name = getElementById ('name').value;

    if (qtrim (name) == '') {
      alert ('Нельзя сменить имя конкурса на пустое.');
      return false;
    }

    frm.submit ();
  }
  
  function addAdmin()
  {
      var select_user = getElementById ('user_name');
      
      var user = qtrim(select_user.value);
      var user_login = qtrim(select_user.options[select_user.selectedIndex].text);
      
      if (typeof lim_array == 'undefined')
          lim_array = new Array();
      
      if (jQuery.inArray(user, lim_array)==-1)      
        lim_array[lim_array.length]=user;
      else
      {
        alert('Этот пользователь уже добавлен в администраторы конкурса');
        return;
      }
      
      getElementById('list').innerHTML += 
          '<tr style="text-align: center;">'+
            '<td>'+user_login+'</td>'+
            '<td align="right"><img class="btn" src="'+getElementById('root').value+'/pics/cross.gif" onclick="if (cfrm (\'Удалить пользователя из списка администраторов?\')) deleteAdmin('+(lim_array.length - 1)+');" title="Удалить пользователя" alt="Удалить пользователя"></td>'+
          '</tr>';
  }
  
  function deleteAdmin(index)
  {
      $('#list tr:eq('+(index)+')').remove();
      
      if (typeof lim_array == 'undefined')
          lim_array = new Array();
      else
          lim_array.splice(index, 1);
  }
  
  function setResultAdmins()
  {
      var result = "";
      for (var key in lim_array) 
      {
          result += lim_array[key]+",";      
      }
      getElementById('result_admin').value = result;
  }
</script>

<form action=".?action=save&id=<?=$id;?>" method="post" onsubmit="setResultAdmins(); check (this); return false;">
  <input type="hidden" id="root" name="root" value="<?= config_get ('document-root'); ?>"/>
  <input type="hidden" id="result_admin" name="result_admin" value="<?php 
                $query = "select * from `Admin_FamilyContest` where `family_contest_id`=".$id;
                $result = mysql_query($query);
                $value="";
                while($row = mysql_fetch_array($result))
                    $value.=$row['user_id'].',';
                echo ($value);
                ?>"/>
  <table class="clear" width="100%">
    <tr>
      <td width="15%" style="padding: 0 2px;">
        Название семейства конкурсов
      </td>
      <td style="padding: 0 2px;">
        <input type="text" id="name" name="name" value="<?=$d['name']?>" class="txt block"><div id="hr"></div>
      </td>
    </tr>
  </table>
  <hr/>
  <table class="clear" width="100%">
    <tr>
        <td width="20%" style="padding: 0 2px;">
            Администраторы
        </td>
        <td>
            <table class="list" id="list" name="list">
            <?php
                $query = "select * from `Admin_FamilyContest` where `family_contest_id`=".$id;
                $result = mysql_query($query);
                $i=0;
                while($row = mysql_fetch_array($result))
                {
                    $user = user_get_by_id($row['user_id']);
                    echo ('<tr style="text-align: center;"><td>'.$user['login'].'</td>'.
                          '<td align="right"><img class="btn" src="'.config_get('document-root').
                          '/pics/cross.gif" onclick="if (cfrm (\'Удалить пользователя из списка администраторов?\'))'.
                          'deleteAdmin('.$i.');" title="Удалить пользователя" alt="Удалить пользователя"></td>'.'</tr>');
                    $i++;
                }
            ?>
            </table>
            <table class="clear" width="100%">
                <tr><td style="padding: 0 2px;">
                    <select id="user_name" name="user_name" class="txt block">
                        <?php
                            $query = "select * from `user`";
                            $result = mysql_query($query);
                            while($row = mysql_fetch_array($result))
                            {
                                echo ('<option value='.$row['id'].'>'.$row['login'].'</option>');
                            }
                        ?>
                    </select>
                    </td>
                    <td width="70px" style="padding: 0 2px;">    
                        <input type="button" value="Добавить" onclick="addAdmin();"/>   
                    </td>
                </tr>
            </table>
        </td>
    </tr>
  </table>
  <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
</form>
<?php
  formc ();
?>
