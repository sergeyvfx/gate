<?php if ($_CMMeta_!='#CMMeta_Included#') {$_CMMeta_='#CMMeta_Included#';
  class CMMeta extends CMHeadTag {
    function CMMeta () { $this->SetClassName ('CMMeta'); }
    function Init ($params) {
      $this->SetDefaultSettings ();
      $this->SetClassName ('meta');
      $this->SetSettings (unserialize_params ($params));
    }
    function SetDefaultSettings () {$this->SetClassName ('CMMeta');}
  }
  content_Register_MCClass ('CMMeta');
}
?>
