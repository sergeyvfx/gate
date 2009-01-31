<?php if ($_Calendar_included!='##calendar_Included##') { $_Calendar_included='##calendar_Included##';
  $calendar_sutff_included=false;
  function calendar_include_stuff () {
    global $calendar_sutff_included, $CORE;
    if ($calendar_sutff_included) return;
    $CORE->AddStyle ('calendar');
    $CORE->AddScriptFile ('calendar.js');
    return true;
  }
  function calendar ($name='', $date='') {
    calendar_include_stuff ();
    if ($date=='') $date=date ('Y-m-d');
    tplp ('back/calendar', array ('name'=>$name, 'date'=>$date));
    add_body_handler ('onload', 'calendar_Init', array ('"'.$name.'"'));
  }
}
?>
