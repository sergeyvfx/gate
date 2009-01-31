<?php if ($_CDCDate_!='#CDCDate_included#') {$_CDCDate_='#CDCDate_included#';
  class CDCDate extends CDCVirtual {
    function CDCDate            () { $this->SetClassName ('CDCDate'); }
    function DrawEditorForm     ($name, $formname='', $init=true) { calendar ($formname.'_'.$name, $this->GetValue ()); }
    function GetDBFieldType     ()       { return 'DATE'; }
    function SettingsForm () { print '<span class="shade">Настройки класса &laquo;Дата&raquo; отсутствуют</span>'; }
    function Value () {
      global $months;
      $v=$this->GetValue ();
      $y=preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si', '\1', $v);
      $m=preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si', '\2', $v);
      $d=preg_replace ('/([0-9]+)\-([0-9]+)\-([0-9]+)/si', '\3', $v);
      return $d.', '.$months[$m].' '.$y;
    }
  }

  content_Register_DCClass ('CDCDate', 'Дата');
}
?>
