<?php if ($_CDCComboBox_!='#CDCComboBox_included#') {$_CDCComboBox_='#CDCComboBox_included#';
  class CDCComboBox extends CDCVirtual {
    function CDCComboBox () { $this->SetClassName ('CDCComboBox'); }

    function SettingsForm () {
?>
      Варианты. Каждая строка - новый вариант. Пустые строки игнорируются:
      <textarea name="CDCComboBox_items" class="block" rows="5"><?=htmlspecialchars (stripslashes ($_POST['CDCComboBox_items']));?></textarea>
<?
    }
    function ReceiveSettings () {
      $items=stripslashes ($_POST['CDCComboBox_items']);
      $items=explode ("\n", $items);
      $arr=array ();
      for ($i=0; $i<count ($items); $i++) {
        $dummy=trim ($items[$i]);
        if ($dummy!='')
          $arr[]=$dummy;
      }
      $this->settings['items']=$arr;
    }
    function DrawEditorForm ($name, $formname='', $init=true) {
      $value=$this->val;
      $items=$this->settings['items'];
      $dummy=$formname.'_'.$name;
?>
      <select name="<?=$dummy;?>" class="block">
<?php
      for ($i=0; $i<count ($items); $i++) {
        $it=$items[$i];
?>
        <option value="<?=htmlspecialchars ($it);?>"<?=(($it==$value)?(' selected'):(''));?>><?=$it;?></option>
<?php
      }
?>
      </select>
<?php
    }
    function BuildCheckImportancy ($var, $formname='') {
      $dummy=$formname.'_'.$var;
      return "(qtrim (getElementById ('$dummy').value)!='')";
    }
    function GetDBFieldType    () { return 'LONGTEXT'; }
  }

  content_Register_DCClass ('CDCComboBox', 'Выпадающий список');
}
?>