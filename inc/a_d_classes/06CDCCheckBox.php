<?php if ($_CDCCheckBox_!='#CDCCheckBox_included#') {$_CDCCheckBox_='#CDCCheckoBox_included#';
  class CDCCheckBox extends CDCVirtual {
    function CDCCheckBox () { $this->SetClassName ('CDCCheckBox'); }

    function DrawEditorForm ($name, $formname='', $init=true) {
      $value=$this->val;
      $dummy=$formname.'_'.$name;
      println ('<div><input type="checkbox" name="'.$dummy.'"'.(($value)?(' checked'):('')).' class="cb">&nbsp;Активен</div>');
    }

    function ReceiveValue       ($field, $formname='') { if ($_POST[$formname.'_'.$field]) $this->val=true; else $this->val=false; }
    function BuildQueryValue    ()     { $v='TRUE'; if (!$this->val) $v='FALSE'; return "$v"; }

    function GetDBFieldType    () { return 'BOOL'; }
  }

  content_Register_DCClass ('CDCCheckBox', 'Флажок');
}
?>
