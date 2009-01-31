<?php if ($_CMVirtual_!='#CMVirtual_Included#') {$_CMVirtual_='#CMVirtual_Included#';
  class CMVirtual extends CVCVirtual {
    function CMVirtual () { $this->SetClassName ('CMVirtual'); }
    function SetDefaultSettings () {$this->SetClassName ('CMVirtual');}
  }
  content_Register_MCClass ('CMVirtual');
}
?>
