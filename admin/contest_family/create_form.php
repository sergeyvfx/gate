<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Service creation for generator
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

  dd_formo ('title=Создать новое семество конкурсов;');
  $list = contest_list();
?>
<script language="JavaScript" type="text/javascript">
  //var cur_service='<?=$list[0]['class']?>';
  function check (frm) {
    var name = getElementById ('name').value;

    if (qtrim (name) == '') {
      alert ('Название создаваемого конкурса не может быть пустым.');
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

<form action=".?action=create" method="POST" onsubmit="setResultAdmins(); check (this); return false;">
  <input type="hidden" id="root" name="root" value="<?= config_get ('document-root'); ?>"/>
  <input type="hidden" id="result_admin" name="result_admin" value=""/>
  <table class="clear" width="100%">
    <tr>
      <td width="20%" style="padding: 0 2px;">
        Название нового семейства конкурса
      </td>
      <td style="padding: 0 2px;">
        <input type="text" class="txt block" name="name" id="name"> <!-- value="<?=$_POST['name'];?>">-->
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
    <button class="submitBtn block" type="submit">Создать</button>
  </div>
</form>
<?php
  dd_formc ();
?>
