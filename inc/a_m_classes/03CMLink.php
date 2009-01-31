<?php if ($_CMLink_!='#CMLink_Included#') {$_CMLink_='#CMLink_Included#';
  class CMLink extends CMHeadTag {
    function CMLink () { $this->SetClassName ('CMLink'); }
    function Init ($params) {
      $this->SetDefaultSettings ();
      $this->SetClassName ('link');
      $this->SetSettings (unserialize_params ($params));
    }
    function SetDefaultSettings () {$this->SetClassName ('CMLink');}
  }
  content_Register_MCClass ('CMLink');
}
?>
