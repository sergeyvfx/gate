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

if (!user_authorized ()) {
  header('Location: ' . config_get('document-root') . '/login/profile');
}

if (!is_responsible(user_id())) {
  print (content_error_page(403));
  return;
}

global $DOCUMENT_ROOT, $redirect, $action, $config_get, $firstlogin, $noschool;

if ($firstlogin) {
  add_info("Это Ваш первый вход в систему. Заполните, пожалуйста, информацию о Вашем учебном заведении.");
}

if ($noschool) {
  add_info("Вы не сможете добавлять команды пока не заполните информацию о учебном заведении. Заполните, пожалуйста, информацию о Вашем учебном заведении.");
}
include '../menu.php';
$info_menu->SetActive('school');
$err_string='';

$r = responsible_get_by_id(user_id());
$sc = school_get_by_id($r['school_id']);

$f = new CVCForm ();
$f->Init('', 'action=.?action\=save' . (($redirect != '') ? ('&redirect=' . prepare_arg($redirect) . ';backlink=' . prepare_arg($redirect)) : ('')) . ';method=POST;add_check_func=check;');

if ($action == 'save') {
  global $name, $school_status, $zipcode, $country, $country_name, $region, $region_name, $area, $area_name, $city_status, $city, $city_name, $street, $house, $building, $flat, $comment, $timezone;
  $name = stripslashes($name);
  $school_status = stripslashes($school_status);
  $zipcode = stripslashes($zipcode);
  $country = stripslashes($country);
  $region = stripslashes($region);
  $area = stripslashes($area);
  $city_status = stripslashes($city_status);
  $city = stripslashes($city);
  $street = stripslashes($street);
  $house = stripslashes($house);
  $building = stripslashes($building);
  $flat = stripslashes($flat);
  $comment = stripslashes($comment);

  $arr = array();

  $arr['timezone_id'] = $timezone;

  //TODO Add check of all necessary
  if ($name!='')
    $arr['name'] = db_string($name);
  else
    $err_string='Название';
  if ($school_status!='')
    $arr['status_id'] = $school_status;
  else
    $err_string=$err_string==''?'Статус учебного заведения':$err_string.', Статус учебного заведения';
  if ($zipcode!='')
    $arr['zipcode'] = db_string($zipcode);
  else
    $err_string=$err_string==''?'Индекс':$err_string.', Индекс';

  if ($country>0)
    $arr['country_id'] = (int)$country;
  else if ($country_name!='')
  {
      $country_name = stripslashes($country_name);
      $country_fields = array();
      $country_fields['name'] = db_string($country_name);
      db_insert('country', $country_fields);
      $country_id = (int)db_max('country', 'id');
      $arr['country_id'] = $country_id;
  }
  else
    $err_string=$err_string==''?'Страна':$err_string.', Страна';
  
  if ($region>0)
    $arr['region_id'] = (int)$region;
  else if ($region_name!='')
  {
      $region_name = stripslashes($region_name);
      $region_fields = array();
      $region_fields['name'] = db_string($region_name);
      if ($country>0)
          $region_fields['country_id']=$country;
      else if ($country_name!=''){
          $region_fields['country_id']=$country_id;
      }
      db_insert('region', $region_fields);
      $region_id = (int)db_max('region', 'id');
      $arr['region_id'] = $region_id;
  }
  else
    $err_string=$err_string==''?'Регион':$err_string.', Регион';

  if ($area>0)
    $arr['area_id'] = (int)$area;
  else if ($area_name!='')
  {
      $area_name = stripslashes($area_name);
      $area_fields = array();
      $area_fields['name'] = db_string($area_name);
      if ($region!=-1)
          $area_fields['region_id']=$region;
      else if ($region_name!=''){
          $area_fields['region_id']=$region_id;
      }
      db_insert('area', $area_fields);
      $area_id = (int)db_max('area', 'id');
      $arr['area_id'] = $area_id;
  }
  else
      $arr['area_id'] = $area;

  if ($city>0)
    $arr['city_id'] = (int)$city;
  else if ($city_name!='')
  {
      $city_name = stripslashes($city_name);
      $city_fields = array();
      $city_fields['name'] = db_string($city_name);
      $city_fields['status_id']=$city_status;
      if ($region!=-1)
          $city_fields['region_id']=$region;
      else if ($region_name!=''){
          $city_fields['region_id']=$region_id;
      }
      if ($area!=-1)
          $city_fields['area_id']=$area;
      else if ($area_name!=''){
          $city_fields['area_id']=$area_id;
      }
      db_insert('city', $city_fields);
      $arr['city_id'] = (int)db_max('city', 'id');
      $city = $arr['city_id'];
  }
  else
    $err_string=$err_string==''?'Населенный пункт':$err_string.', Населенный пункт';
  
  if ($street!='')
    $arr['street'] = db_string($street);
  else
    $err_string=$err_string==''?'Улица':$err_string.', Улица';
  if ($house!='')
    $arr['house'] = db_string($house);
  else
    $err_string=$err_string==''?'Дом':$err_string.', Дом';
  $arr['building'] = db_string($building);
  $arr['flat'] = db_string($flat);
  $arr['comment'] = db_string($comment);
  
  //save info about school
  if ($err_string=='')
  {
    if ($r['school_id']!='' && $r['school_id']!=-1){
        db_update('school', $arr, '`id`=' . $sc['id']);
    }
    else
    {
        db_insert('school', $arr);
        $arr=array();
        //FIXME Why not used db_last_insert()?
        $arr['school_id'] = (int)db_max('school', 'id');
        db_update('responsible', $arr, '`user_id`='.$r['user_id']);
    }

    //save city status
    /*$arr= array();
    $arr['status_id'] = $city_status;
    if (count($arr) > 0)
        db_update('city', $arr, '`id`=' . $city);*/

//    $f->AppendCustomField(array('src' => '<div align="center">Сохранение прошло успешно</div>'));
  }
}


$r = responsible_get_by_id(user_id());
$sc = school_get_by_id($r['school_id']);
$cit = city_get_by_id($sc['city_id']);

//find all school statuses
$query = "select * from `school_status`";
$result = db_query($query);
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    if ($rows['id']==$sc['status_id'])
        $statuses .= '<option value="'.$rows["id"].'" selected>'.$rows["name"].'</option> ';
    else
        $statuses .= '<option value="'.$rows["id"].'">'.$rows["name"].'</option> ';

$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Название: <span class="error">*</span></td><td><input id="name" name="name" type="text" class="txt block" onblur="check_frm_name ();" value="' . htmlspecialchars($sc['name']) . '"></td></tr></table><div id="name_check_res" style="display: none;"></div>'));
$f->AppendCustomField(array('src' => '<table width="100%"><tr><td width="30%">Статус учебного заведения: <span class="error">*</span></td><td><select id="school_status" name="school_status" class="block">'.addslashes($statuses).'</select></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Почтовый индекс: <span class="error">*</span></td><td><input id="zipcode" name="zipcode" type="text" class="txt block" onblur="check_frm_zipcode ();" value="' . htmlspecialchars($sc['zipcode']) . '"></td></tr></table><div id="zipcode_check_res" style="display: none;"></div>'));

//find all countries
$country=-1;
$query = "select * from `country`";
$result = db_query($query);
while($rows = mysql_fetch_array($result, MYSQL_ASSOC)){
    if ($country=='-1')
          $country=$rows['id'];
    if ($rows['id']==$sc['country_id']){
        $countries .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
        $country = $rows['id'];
    }
    else
        $countries .= '<option value='.$rows["id"].'>'.$rows["name"].'</option> ';
}
if ($countries!=''){
    $countries .='<option value=-1>Другая</option>';
    $f->AppendCustomField(array('src' => '<table width="100%"><tr><td width="30%">Страна: <span class="error">*</span></td><td><select id="country" name="country" class="block" onchange="other_country()">'.addslashes($countries).'</select></td></tr><tr><td width="30%"></td><td><div id="other_country" name="other_country" style="display: none; margin-top:3px"></div></td></tr></table><div id="country_check_res" style="display: none;"></div>'));
} else {
    $countries .='<option value=-1>Другая</option>';
    $f->AppendCustomField(array('src' => '<table width="100%"><tr><td width="30%">Страна: <span class="error">*</span></td><td><select id="country" name="country" class="block" onblur="check_frm_country()">'.addslashes($countries).'</select></td></tr><tr><td width="30%"></td><td><div id="other_country" name="other_country" style="display: block; margin-top:3px"><input id="country_name" name="country_name" type="text" class="txt block" onblur="check_frm_country ()"></div></td></tr></table><div id="country_check_res" style="display: none;"></div>'));
}

//find all region
$region=-1;
if ($country!=-1)
    $query = 'select * from `region` where `country_id`='.$country;
else
    $query = 'select * from `region`';
$result = db_query($query);

while ($rows = mysql_fetch_array($result, MYSQL_ASSOC)) {
  if ($rows['country_id']==$sc['country_id'] || $sc==''){
    if ($region==-1)
        $region=$rows['id'];
    if ($sc['region_id'] <= 0 || $sc['region_id'] == '') {
      $selected = ($rows['name'] == 'Пермский край') ? ('selected') : ('');
      $region=($rows['name'] == 'Пермский край') ? $rows['id']: $region;
    } else {
      $selected = ($rows['id'] == $sc['region_id']) ? ('selected') : ('');
      $region=($rows['id'] == $sc['region_id']) ? $rows['id'] : $region;
    }
    $regions .= '<option value="' . $rows['id'] . '" ' . $selected . '>' . $rows['name'] . '</option>';
  }
  $regions .= '<option value="' . $rows['id'] . '" ' . $selected . '>' . $rows['name'] . '</option>';
}
if ($regions!='') {
    $regions .='<option value="-1">Другой</option>';
    $f->AppendCustomField(array('src' => '<table width="100%" class="clear"><tr><td width="30%">Регион: <span class="error">*</span></td><td><select id="region" name="region" class="block" onchange="other_region()">'.addslashes($regions).'</select></td></tr><tr><td width="30%"></td><td><div id="other_region" name="other_region" style="display: none; margin-top:3px"></div></td></tr></table><div id="region_check_res" style="display: none;"></div>'));
} else {
    $regions .='<option value="-1">Другой</option>';
    $f->AppendCustomField(array('src' => '<table width="100%"><tr><td width="30%">Регион: <span class="error">*</span></td><td><select id="region" name="region" class="block" onblur="check_frm_region ()">'.addslashes($regions).'</select></td></tr><tr><td width="30%"></td><td><div id="other_region" name="other_region" style="display: block; margin-top:3px"><input id="region_name" name="region_name" type="text" class="txt block" onblur="check_frm_region ()"></div></td></tr></table><div id="region_check_res" style="display: none;"></div>'));
}

$area=-1;
if ($region!=-1)
    $query = "select * from `area` where `region_id`=".$region;
else
    $query = "select * from `area`";
$result = db_query($query);
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    if ($rows['region_id']==$sc['region_id'] || $sc==''){
        if ($area==-1)
            $area=$rows['id'];
        if ($rows['id']==$sc['area_id']){
            $areas .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
            $area = $rows['id'];
        }
        else
            $areas .= '<option value='.$rows["id"].'>'.$rows["name"].'</option>';
    }
if ($areas!='' && $sc['area_id']!=-1){
        $areas .='<option value="-1">Другой</option>';
    $f->AppendCustomField(array('src' => '<table width="100%"><tr><td width="30%">Район: </td><td><select id="area" name="area" class="block" onchange="other_area()">'.addslashes($areas).'</select></td></tr><tr><td width="30%"></td><td><div id="other_area" name="other_area" style="display: none; margin-top:3px"></div></td></tr></table>'));
} else {
    $area=-1;
    $areas .='<option value="-1" selected>Другой</option>';
    $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Район: </td><td><select id="area" name="area" class="block" onchange="other_area()">'.addslashes($areas).'</select></td></tr><tr><td width="30%"></td><td><div id="other_area" name="other_area" style="display:block; margin-top:3px"><input id="area_name" name="area_name" type="text" class="txt block"></div></td></tr></table>'));
}

$city_status=1;
$query = "select * from `city_status`";
$result = db_query($query);
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    if ($rows['id']==$cit['status_id']){
        $city_statuses .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
        $city_status=$rows['id'];
    }
    else
        $city_statuses .= '<option value='.$rows["id"].'>'.$rows["name"].'</option> ';
$f->AppendCustomField(array('src' => '<table width="100%"><tr><td width="30%">Статус населенного пункта: <span class="error">*</span></td><td><select id="city_status" name="city_status" class="block" onchange="other_city_status()">'.addslashes($city_statuses).'</select></td></tr></table>'));

//find all cities
if ($area!=-1)
    $query = 'select * from `city` where (`status_id`=NULL or `status_id`='.$city_status.') and `area_id`='.$area;
else if ($region!=-1)
    $query = 'select * from `city` where (`status_id`=NULL or `status_id`='.$city_status.') and (`area_id` IS NULL or `area_id`=-1) and `region_id`='.$region;
else
    $query = 'select * from `city` where `status_id`=NULL or `status_id`='.$city_status;
$result = db_query($query);
while($rows = mysql_fetch_array($result, MYSQL_ASSOC))
    if ($sc=='' || ($rows['area_id']==$sc['area_id'] && $sc['area_id']!=NULL && $sc['area_id']>0)||
            ($rows['region_id']==$sc['region_id'] && ($sc['area_id']!=NULL || $sc['area_id']>0)))
        if ($rows['id']==$sc['city_id'])
            $cities .= '<option value='.$rows["id"].' selected>'.$rows["name"].'</option> ';
        else
            $cities .= '<option value='.$rows["id"].'>'.$rows["name"].'</option> ';
if ($cities!='')
{
    $cities .='<option value="-1">Другой</option>';
    $f->AppendCustomField(array('src' => '<table width="100%"><tr><td width="30%">Населенный пункт: <span class="error">*</span></td><td><select id="city" name="city" class="block" onchange="other_city()">'.addslashes($cities).'</select></td></tr><tr><td width="30%"></td><td><div id="other_city" name="other_city" style="display: none; margin-top:3px"></div></td></tr></table><div id="city_check_res" style="display: none;"></div>'));
}
else
{
    $cities .='<option value="-1">Другой</option>';
    $f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Населенный пункт: <span class="error">*</span></td><td><select id="city" name="city" class="block" onblur="check_frm_city ();">'.addslashes($cities).'</select></td></tr><tr><td width="30%"></td><td><div id="other_city" name="other_city" style="display:block; margin-top:3px"><input id="city_name" name="city_name" type="text" class="txt block" onblur="check_frm_city ()"></div></td></tr></table><div id="city_check_res" style="display: none;"></div>'));
}

$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Улица: <span class="error">*</span></td><td><input id="street" name="street" type="text" class="txt block" onblur="check_frm_street ();" value="' . htmlspecialchars($sc['street']) . '"></td></tr></table><div id="street_check_res" style="display: none;"></div>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Дом: <span class="error">*</span></td><td><input id="house" name="house" type="text" class="txt block" onblur="check_frm_house ();" value="' . htmlspecialchars($sc['house']) . '"></td></tr></table><div id="house_check_res" style="display: none;"></div>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Корпус:</td><td><input id="building" name="building" type="text" class="txt block" value="' . htmlspecialchars($sc['building']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Квартира:</td><td><input id="flat" name="flat" type="text" class="txt block" value="' . htmlspecialchars($sc['flat']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Примечание:</td><td><input id="comment" name="comment" type="text" class="txt block" onblur="comment_frm_street ();" value="' . htmlspecialchars($sc['comment']) . '"></td></tr></table><div id="comment_check_res" style="display: none;"></div>'));

$sql = arr_from_query('SELECT * FROM timezone');
$timezome = '';
foreach ($sql as $k) {
  $sign = ($k['offset'] > 0) ? ('+') : ('');
  if ($sc['timezone_id'] <= 0 || $sc['timezone_id'] == '') {
    $selected = ($k['name'] == 'Пермь') ? ('selected') : ('');
  } else {
    $selected = ($k['id'] == $sc['timezone_id']) ? ('selected') : ('');
  }
  $timezome .= '<option value="' . $k['id'] . '" '. $selected . '>' . $k['name'] . ' (' . $sign . $k['offset'] . ')</option>';
}

$f->AppendCustomField(array('src' => '<table width="100%"><tr><td width="30%">Часовой пояс: <span class="error">*</span></td><td><select id="timezone" name="timezone" class="block">'.addslashes($timezome).'</select></td></tr><tr><td width="30%"></td></tr></table>'));

if ($err_string!='')
    $f->AppendCustomField(array('src' => '<div class="txt error">Вы не заполнили следующие обязательные поля: '.stripslashes($err_string).'</div>'));
?>


<script type="text/JavaScript"  language="JavaScript">
  function check () {
      var name = getElementById("name").value;
      var zipcode = getElementById("zipcode").value;
      var country = getElementById("country").value;
      var other_country = getElementById("country_name").value;
      var region = getElementById("region").value;
      var other_region = getElementById("region_name").value;
      var city = getElementById("city").value;
      var other_city = getElementById("city_name").value;
      var street = getElementById("street").value;
      var house = getElementById("house").value;
      var comment = getElementById("comment").value;
      
    if (qtrim(name)=='') {
        alert ('Поле "Название" обязательно для заполнения');
        return false;
    }

    if (qtrim(zipcode)=='') {
      alert('Поле "Почтовый индекс" обязательно для заполнения');
      return false;
    }

    if (!check_zipcode(zipcode)) {
      alert('Указанный почтовый индекс не выглядит корректным. Индекс должен состоять из 6 цифр');
      return false;
    }
    
    if (country==-1 && qtrim(other_country)=='') {
      alert('Поле "Страна" обязательно для заполнения');
      return false;
    }
    
    if (region==-1 && qtrim(other_region)=='') {
      alert('Поле "Регион" обязательно для заполнения');
      return false;
    }
    
    if (city==-1 && qtrim(other_city)=='') {
      alert('Поле "Населенный пункт" обязательно для заполнения');
      return false;
    }
    
    if (qtrim(street)=='') {
      alert('Поле "Улица" обязательно для заполнения');
      return false;
    }
    
    if (qtrim(house)=='') {
      alert('Поле "Дом" обязательно для заполнения');
      return false;
    }

    if (comment.length > <?=opt_get('max_comment_len');?>) {
      alert('Поле "Комментарий" не может содержать более <?=opt_get('max_comment_len');?> символов');
      return false;
    }
    return true;
  }

  function update_region_select(http_request){
    if (http_request.readyState == 4) {
        var region_select = getElementById("region");

        region_select.length=0;
        var regions = http_request.responseText.split(";");
        for (i = 0; i < regions.length; i++){
        if (document.createElement){
            var newListOption = document.createElement("OPTION");
            newListOption.text = regions[i].split(",")[1];
            newListOption.value = regions[i].split(",")[0];
            newListOption.Selected=regions[i].split(",")[2];
            // тут мы используем для добавления элемента либо метод IE, либо DOM, которые, alas, не совпадают по параметрам…
            (region_select.options.add) ? region_select.options.add(newListOption) : region_select.add(newListOption, null);
        }else{
            // для NN3.x-4.x
            region_select.options[i] = new Option(regions[i].split(",")[0], regions[i].split(",")[1], false, false);
        }
        }
        if (region_select.value==-1){
            var other_region = getElementById("other_region");
            other_region.style.display = 'block';
            if (other_region.innerHTML=='')
                other_region.innerHTML = '<input id="region_name" name="region_name" type="text" class="txt block" onblur="check_frm_region ();">'
        }
        else{
            getElementById("other_region").style.display = 'none';
            hide_msg('region_check_res');
        }

        ipc_send_request ('/', 'ipc=find_areas&region='+region_select.value, update_area_select);
//        region_select.innerHTML = http_request.responseText;
    }
  }

    function update_area_select(http_request){
    if (http_request.readyState == 4) {
        var area_select = getElementById("area");

        area_select.length=0;
        var areas = http_request.responseText.split(";");
        for (i = 0; i < areas.length; i++){
        if (document.createElement){
            var newListOption = document.createElement("OPTION");
            newListOption.text = areas[i].split(",")[1];
            newListOption.value = areas[i].split(",")[0];
            newListOption.Selected=areas[i].split(",")[2];
            // тут мы используем для добавления элемента либо метод IE, либо DOM, которые, alas, не совпадают по параметрам…
            (area_select.options.add) ? area_select.options.add(newListOption) : area_select.add(newListOption, null);
        }else{
            // для NN3.x-4.x
            area_select.options[i] = new Option(areas[i].split(",")[0], areas[i].split(",")[1], false, false);
        }
        }
        if (area_select.value==-1){
            var other_area = getElementById("other_area");
            other_area.style.display = 'block';
            if (other_area.innerHTML=='')
                other_area.innerHTML = '<input id="area_name" name="area_name" type="text" class="txt block" onblur="check_frm_area ();">'

            ipc_send_request ('/', 'ipc=find_cities&area=-1&region='+getElementById("region").value+'&city_status='+getElementById("city_status").value, update_city_select);
        }
        else{
            getElementById("other_area").style.display = 'none';
            ipc_send_request ('/', 'ipc=find_cities&region=-1&area='+area_select.value+'&city_status='+getElementById("city_status").value, update_city_select);
        }
    }
  }

    function update_city_select(http_request){
    if (http_request.readyState == 4) {
        var city_select = getElementById("city");

        city_select.length=0;
        var cities = http_request.responseText.split(";");
        for (i = 0; i < cities.length; i++){
        if (document.createElement){
            var newListOption = document.createElement("OPTION");
            newListOption.text = cities[i].split(",")[1];
            newListOption.value = cities[i].split(",")[0];
            newListOption.Selected=cities[i].split(",")[2];
            // тут мы используем для добавления элемента либо метод IE, либо DOM, которые, alas, не совпадают по параметрам…
            (city_select.options.add) ? city_select.options.add(newListOption) : city_select.add(newListOption, null);
        }else{
            // для NN3.x-4.x
            city_select.options[i] = new Option(cities[i].split(",")[0], cities[i].split(",")[1], false, false);
        }
        }
        if (city_select.value==-1){
            var other_city = getElementById("other_city");
            other_city.style.display = 'block';
            if (other_city.innerHTML=='')
                other_city.innerHTML = '<input id="city_name" name="city_name" type="text" class="txt block" onblur="check_frm_city ();">'
        }
        else{
            getElementById("other_city").style.display = 'none';
            hide_msg('city_check_res');
        }
    }
  }

  function other_country() {
    var id = getElementById("country").value;
    var opt = getElementById("other_country");
    if (id == -1) {
      opt.style.display = 'block';
      if (opt.innerHTML=='')
        opt.innerHTML = '<input id="country_name" name="country_name" type="text" class="txt block" onblur="check_frm_country ()">'
      getElementById('country_name').focus();

    } else {
      opt.style.display = 'none';
      hide_msg('country_check_res');
    }
    ipc_send_request ('/', 'ipc=find_regions&country='+id, update_region_select);
  }

  function other_region() {
    var id = getElementById("region").value;
    var opt = getElementById("other_region");
    if (id == -1) {
      opt.style.display = 'block';
      if (opt.innerHTML=='')
        opt.innerHTML = '<input id="region_name" name="region_name" type="text" class="txt block" onblur="check_frm_region ();">'
      getElementById('region_name').focus();
    } else {
      opt.style.display = 'none';
      hide_msg('region_check_res');
    }
    //ipc_send_request ('/', 'ipc=find_cities&area='+id+'&region='+getElementById("region").value, update_city_select);
    ipc_send_request ('/', 'ipc=find_areas&region='+id, update_area_select);
  }

  function other_area() {
    var id = getElementById("area").value;
    var opt = getElementById("other_area");
    if (id == -1) {
      opt.style.display = 'block';
      if (opt.innerHTML=='')
        opt.innerHTML = '<input id="area_name" name="area_name" type="text" class="txt block">'
      getElementById('area_name').focus();

    } else {
      opt.style.display = 'none';
    }
    ipc_send_request ('/', 'ipc=find_cities&area='+id+'&region='+getElementById("region").value+'&city_status='+getElementById("city_status").value, update_city_select);
  }

  function other_city_status() {
    ipc_send_request ('/', 'ipc=find_cities&area='+getElementById("area").value+'&region='+getElementById("region").value+'&city_status='+getElementById("city_status").value, update_city_select);
  }

  function other_city() {
    var id = getElementById("city").value;
    var opt = getElementById("other_city");
    if (id == -1) {
      opt.style.display = 'block';
      if (opt.innerHTML=='')
        opt.innerHTML = '<input id="city_name" name="city_name" type="text" class="txt block" onblur="check_frm_city ();">'
      getElementById('city_name').focus();
    } else {
      opt.style.display = 'none';
      hide_msg('city_check_res');
    }
  }

  function check_frm_name () {
    var name = getElementById ('name').value;

    if (qtrim(name)=='') {
      show_msg ('name_check_res', 'err', 'Это поле обязательно для заполнения');
      return false;
    }

    hide_msg('name_check_res');
  }

  function check_frm_zipcode () {
    var zipcode = getElementById ('zipcode').value;

    if (qtrim(zipcode)=='') {
      show_msg ('zipcode_check_res', 'err', 'Это поле обязательно для заполнения');
      return false;
    }

    if (!check_zipcode(zipcode)) {
      show_msg ('zipcode_check_res', 'err', 'Указанный индекс не выглядит корректным. Индекс должен состоять из 6 цифр');
      return false;
    }

    hide_msg('zipcode_check_res');
  }

  function check_frm_country () {
    var country = getElementById ('country').value;
    var other_country = getElementById("country_name").value;
    
    if (country==-1 && qtrim(other_country)=='') {
      show_msg ('country_check_res', 'err', 'Это поле обязательно для заполнения');
      return false;
    }

    hide_msg('country_check_res');
  }

  function check_frm_region () {
    var region = getElementById ('region').value;
    var other_region = getElementById("region_name").value;

    if (region==-1 && qtrim(other_region)=='') {
      show_msg ('region_check_res', 'err', 'Это поле обязательно для заполнения');
      return false;
    }

    hide_msg('region_check_res');
  }

  function check_frm_city () {
    var city = getElementById ('city').value;
    var other_city = getElementById('city_name').value;

    if (city==-1 && qtrim(other_city)=='') {
      show_msg ('city_check_res', 'err', 'Это поле обязательно для заполнения');
      return false;
    }

    hide_msg('city_check_res');
  }

  function check_frm_street () {
    var street = getElementById ('street').value;

    if (qtrim(street)=='') {
      show_msg ('street_check_res', 'err', 'Это поле обязательно для заполнения');
      return false;
    }

    hide_msg('street_check_res');
  }

  function check_frm_house () {
    var house = getElementById ('house').value;

    if (qtrim(house)=='') {
      show_msg ('house_check_res', 'err', 'Это поле обязательно для заполнения');
      return false;
    }

    hide_msg('house_check_res');
  }

  function check_frm_comment() {
      var comment = getElementById ('comment').value;

      if (comment.length > <?=opt_get('max_comment_len');?>) {
          show_msg ('comment_check_res', 'err', 'Поле "Комментарий" не может содержать более <?=opt_get('max_comment_len');?> символов');
          return;
      }

      hide_msg('comment_check_res');
  }


</script>

<div id="snavigator"><a href="<?= config_get('document-root') . "/login/profile/" ?>">Мой профиль</a>Учебное заведение</div>
${information}
<div class="form">
  <div class="content">
    <?php
    $info_menu->Draw();
    $f->Draw();
    ?>
  </div>
</div>
