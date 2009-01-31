<?php if ($_CDCTime_!='#CDCTime_included#') {$_CDCTime_='#CDCTime_included#';
  class CDCTime extends CDCVirtual {
    function CDCTime            () { $this->SetClassName ('CDCTime'); }
    function DrawEditorForm     ($name, $formname='', $init=true) {
      global $CORE;
      $CORE->AddStyle ('time');
      tplp ('back/timepicker', array ('name'=>$formname.'_'.$name, 'value'=>$this->GetValue () ));
    }
  }

  content_Register_DCClass ('CDCTime', 'Время');
}
?>
