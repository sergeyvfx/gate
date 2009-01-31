<?php if ($_CSCVirtual_included_!='#CSCVirtual_Included#') {$_CSCVirtual_included_='#CSCVirtual_Included#';
  class CSCVirtual extends CVirtual {
    var $sName, $id;
    function CSCVirtual () { $this->SetServiceName ('CSCVirtual'); $this->SetClassName ('CSCVirtual'); }
    function CanCreate () { return true; }
    function Create () {  }
    function InitInstance ($id=-1, $virtual=false) {  }
    function PerformDeletion () {  }
    function DrawSettingsForm ($formnane='') { print '<span class="shade">Для сервиса &laquo;<b>'.$this->GetServiceName ().'</b>&raquo; настройки отсутствуют</span>'; }
    function ReceiveSettings ($formnane='') { return true; }
    function SetServiceName ($v) { $this->sName=$v; }
    function GetServiceName ()   { return $this->sName; }
    
    function UpdateSettings () {
      $settings=addslashes ($this->SerializeSettings ());
      db_update ('service', array ('settings'=>"\"$settings\""), '`id`='.$this->id);
    }
  }
//  content_Register_SCClass ('CSCVirtual', 'CSCVirtual');
}
?>
