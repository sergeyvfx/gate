<?php if ($_CMScript_!='#CMScript_Included#') {$_CMScript_='#CMScript_Included#';
  class CMScript extends CMHeadTag {
    var $innerHTML;
    function CMScript () { $this->SetClassName ('CMScript'); }
    function Init ($params,$innerHTML='') {
      $this->SetDefaultSettings ();
      $this->closeTag=true;
      $this->innerHTML=$innerHTML;
      $this->SetClassName ('script');
      $this->SetSettings (unserialize_params ($params));
    }
    function SetDefaultSettings () {$this->SetClassName ('CMScript');}
    function SetSource ($src) {$this->innerHTML=$src;}
    function InnerHTML () {return $this->innerHTML;}
  }
  content_Register_MCClass ('CMScript');
}
?>
