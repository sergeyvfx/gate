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
  print ('HACKERS?');
  die;
}

global $id;
formo('title=Редактирование ограничения');

$limit = limit_get_by_id($id);

?>

<script language="JavaScript" type="text/javascript">
  $(document).ready(function(){
    lim_array = new Array();
    var result = getElementById('result_limit').value;
    var regex = /(\d+) (<|<=|=|>|>=|<>|is null|is not null) (\S*) (OR|AND)/g;
    var matches = result.match(regex);
    for (var key in matches) 
    {
        regex = /(\d+) (<|<=|=|>|>=|<>|is null|is not null) (\S*) (OR|AND)/;
        var res = matches[key].match(regex);
        lim_array[lim_array.length]=new Array(res[1], res[2], res[3], res[4]);
    }    
  });
  
  function check(frm) {
    var name = qtrim(getElementById ('name').value);
    
    if (name == '') {
      alert("Поле \"Название\" обязательно для заполнения");
      return;
    }
    
    frm.submit ();
  }

  function field_changed()
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
          document.getElementById("field_value").innerHTML=xmlhttp.responseText;
      }
    }
    var value = document.getElementById("field_name").value;
    xmlhttp.open("GET","getFieldValues.php?field="+value,true);
    xmlhttp.send();
  }
  
  function addCondition()
  {
      var select_field = getElementById ('field_name');
      var select_operation = getElementById ('operation');
      var txt_value = getElementById ('field_value');
      var select_connection = getElementById ('connection');      
      
      var field = qtrim(select_field.value);
      var field_text = qtrim(select_field.options[select_field.selectedIndex].text);
      var operation = qtrim(select_operation.value);
      var operation_text = qtrim(select_operation.options[select_operation.selectedIndex].text);
      var value = qtrim(txt_value.value);
      var connection = qtrim(select_connection.value);
      var connection_text = qtrim(select_connection.options[select_connection.selectedIndex].text);
      
      if (field == '') 
      {
        alert("Укажите значение в поле \"Имя поля\"");
        return;
      }
      if (operation == '') 
      {
        alert("Укажите значение в поле \"Критерий\"");
        return;
      }
      if (operation != 'is null' && operation != 'is not null' && value == '') 
      {
        alert("Укажите значение в поле \"Значение\"");
        return;
      }
      if (connection == '') 
      {
        alert("Укажите значение в поле \"Связка\"");
        return;
      }      
      
      if (typeof lim_array == 'undefined')
          lim_array = new Array();
      lim_array[lim_array.length]=new Array(field, operation, value, connection);
      
      getElementById('list').innerHTML += 
          '<tr style="text-align: center;">'+
            '<td>'+field_text+'</td>'+
            '<td>'+operation_text+'</td>'+
            '<td>'+value+'</td>'+
            '<td>'+connection_text+'</td>'+
            '<td align="right"><img class="btn" src="'+getElementById('root').value+'/pics/cross.gif" onclick="if (cfrm (\'Удалить условие?\')) deleteCondition('+(lim_array.length - 1)+');" title="Удалить условие" alt="Удалить условие"></td>'+
          '</tr>';
  }
  
  function deleteCondition(index)
  {
      $('#list tr:eq('+index+1+')').remove();
      
      if (typeof lim_array == 'undefined')
          lim_array = new Array();
      else
          lim_array.splice(index, 1);
  }
  
  function setResultLimit()
  {
      var result = "";
      for (var key in lim_array) 
      {
          result += lim_array[key][0]+" "+lim_array[key][1]+" "+lim_array[key][2]+" "+lim_array[key][3]+" "; 
      }
      getElementById('result_limit').value = result;
  }
</script>

<form action=".?action=save&id=<?= $id; ?>" method="POST" onsubmit="setResultLimit(); check (this); return false;">
    <input type="hidden" id="root" name="root" value="<?= config_get ('document-root'); ?>"/>
    <input type="hidden" id="result_limit" name="result_limit" value="<?= $limit['limit'] ?>"/>
    <table class="clear" width="100%">
        <tr>
            <td width="70px" style="padding: 0 2px;">Название:</td>
            <td style="padding: 0 2px;"> <input type="text" id="name" name="name" value="<?= $limit['name']; ?>" class="txt"/></td>
        </tr>
    </table>
    
    <table class="list" id="list" name="list">
        <tr class="h">
            <th width="35%" style="text-align: center;">Поле</th>
            <th width="100px" style="text-align: center;">Критерий</th>
            <th width="35%" style="text-align: center;">Значение</th>
            <th width="100px" style="text-align: center;">Связка</th>
            <th width="72" class="last">&nbsp;</th>
        </tr>
        <?php
            preg_match_all('/(\d+) (<|<=|=|>|>=|<>|is null|is not null) (\S*) (OR|AND)/', $limit['limit'], $limits,PREG_SET_ORDER);
            $i=0;
            foreach ($limits as $value) {
                $field_id = $value[1];
                $operation = $value[2];
                $val = $value[3];
                $connection = $value[4];
                if ($operation=='=') $operation='равно';
                if ($operation=='>') $operation='больше';
                if ($operation=='>=') $operation='больше или равно';
                if ($operation=='<') $operation='меньше';
                if ($operation=='<=') $operation='меньше или равно';
                if ($operation=='<>') $operation='не равно';
                if ($operation=='is null') $operation='пусто';
                if ($operation=='is not null') $operation='не пусто';
                
                if ($connection=='OR') $connection='ИЛИ';
                if ($connection=='AND') $connection='И';
                
                $field_text = db_field_value("visible_field", "caption", "`id`=".$field_id);
                
                echo '<tr style="text-align: center;">'.
                        '<td>'.$field_text.'</td>'.
                        '<td>'.$operation.'</td>'.
                        '<td>'.$val.'</td>'.
                        '<td>'.$connection.'</td>'.
                        '<td align="right"><img class="btn" src="'.config_get ('document-root').'/pics/cross.gif" onclick="if (cfrm (\'Удалить условие?\')) deleteCondition('.$i.');" title="Удалить условие" alt="Удалить условие"></td>'.
                     '</tr>';
                $i++;
            }
        ?>
    </table>
    <table class="clear" width="100%">
        <tr><td width="35%" style="padding: 0 2px;">Имя поля:</td>
            <td width="100px" style="padding: 0 2px;">Критерий:</td>
            <td width="35%" style="padding: 0 2px;">Значение:</td>
            <td width="100px" style="padding: 0 2px;">Связка:</td>
        </tr>
        <tr><td width="35%" style="padding: 0 2px;">
                <select id="field_name" name="field_name" class="txt block">
                    <?php
                        $query = "select * from `visible_field`";
                        $result = mysql_query($query);
                        while($row = mysql_fetch_array($result))
                        {
                            echo ('<option value='.$row['id'].'>'.$row['caption'].'</option>');
                        }
                    ?>
                </select>
            </td>
            <td width="100px" style="padding: 0 2px;">
                <select id="operation" name="operation" class="txt block">
                    <option value="=">равно</option>
                    <option value="<>">не равно</option>
                    <option value=">">больше</option>
                    <option value=">=">больше или равно</option>
                    <option value="<">меньше</option>
                    <option value="<=">меньше или равно</option>
                    <option value="is null">пусто</option>
                    <option value="is not null">не пусто</option>
                </select>
            </td>
            <td width="35%" style="padding: 0 2px;">
                <input type="text" id="field_value" name="field_value" class="txt block"/>
                <!--<select width="35%" id="field_value" name="field_value" class="txt block">
                    
                </select>-->
            </td>
            <td width="100px" style="padding: 0 2px;">
                <select id="connection" name="connection" class="txt block">
                    <option value="AND">И</option>
                    <option value="OR">ИЛИ</option>
                </select>
            </td>
        </tr>
    </table>
      <button type="button" onclick="addCondition();">Добавить</button>
    <div id="hr"></div>
    
    <div class="formPast">
    <button class="submitBtn" type="button" onclick="nav ('.?');">Назад</button>
    <button class="submitBtn" type="submit">Сохранить</button>
  </div>
  </form>
          
<?php
  formc ();
?>
