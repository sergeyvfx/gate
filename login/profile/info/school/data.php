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

global $DOCUMENT_ROOT, $redirect, $action;
include $DOCUMENT_ROOT . '/login/profile/inc/menu.php';
include '../menu.php';
$profile_menu->SetActive('info');
$info_menu->SetActive('school');

if ($action == 'save') {
  global $name, $school_status, $zipcode, $region, $area, $city_status, $city, $street, $house, $building, $flat;
  $name = stripslashes($name);
  $school_status = stripslashes($school_status);
  $zipcode = stripslashes($zipcode);
  $region = stripslashes($region);
  $area = stripslashes($area);
  $city_status = stripslashes($city_status);
  $city = stripslashes($city);
  $street = stripslashes($street);
  $house = stripslashes($house);
  $building = stripslashes($building);
  $flat = stripslashes($flat);

  $arr = array();

  $u = user_get_by_id(user_id());

  //TODO Add check of all necessary
//  $arr['name'] = db_string($name);
//  $arr['school_status'] = db_string($school_status);
//  $arr['zipcode'] = db_string($zipcode);
//  $arr['region'] = db_string($region);
//  $arr['area'] = db_string($area);
//  $arr['city_status'] = db_string($city_status);
//  $arr['city'] = db_string($city);
//  $arr['street'] = db_string($street);
//  $arr['house'] = db_string($house);
//  $arr['building'] = db_string($building);
//  $arr['flat'] = db_string($flat);
  //TODO Add saving data
//  if (count($arr) > 0) {
//    db_update('user', $arr, '`id`=' . user_id ());
//  }
}

//$u = user_get_by_id(user_id());
//$sc_u = school_admin_get_by_id($u['id']);
//$sc = school_get_by_id($sc_u['school_id']);

$f = new CVCForm ();
$f->Init('', 'action=.?action\=save' . (($redirect != '') ? ('&redirect=' . prepare_arg($redirect) . ';backlink=' . prepare_arg($redirect)) : ('')) . ';method=POST;add_check_func=check;');
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Название</td><td><input id="name" name="name" type="text" class="txt block" value="' . htmlspecialchars($sc['name']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Статус учебного заведения</td><td><input id="school_status" name="school_status" type="text" class="txt block" value="' . htmlspecialchars($sc['school_status']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Почтовый индекс</td><td><input id="zipcode" name="zipcode" type="text" class="txt block" value="' . htmlspecialchars($sc['zipcode']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Регион</td><td><input id="region" name="region" type="text" class="txt block" value="' . htmlspecialchars($sc['region']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Район</td><td><input id="area" name="area" type="text" class="txt block" value="' . htmlspecialchars($sc['area']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Статус населенного пункта</td><td><input id="city_status" name="city_status" type="text" class="txt block" value="' . htmlspecialchars($sc['city_status']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Населенный пункт</td><td><input id="city" name="city" type="text" class="txt block" value="' . htmlspecialchars($sc['city']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Улица</td><td><input id="street" name="street" type="text" class="txt block" value="' . htmlspecialchars($sc['street']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Дом</td><td><input id="house" name="house" type="text" class="txt block" value="' . htmlspecialchars($sc['house']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Корпус</td><td><input id="building" name="building" type="text" class="txt block" value="' . htmlspecialchars($sc['building']) . '"></td></tr></table>'));
$f->AppendCustomField(array('src' => '<table class="clear" width="100%"><tr><td width="30%">Квартира</td><td><input id="flat" name="flat" type="text" class="txt block" value="' . htmlspecialchars($sc['flat']) . '"></td></tr></table>'));
?>

<div id="navigator">Мой профиль >> Учебное заведение</div>
${information}
<div class="form">
  <div class="content">
    <?php
    $profile_menu->Draw();
    $info_menu->Draw();
    $f->Draw();
    ?>
  </div>
</div>
