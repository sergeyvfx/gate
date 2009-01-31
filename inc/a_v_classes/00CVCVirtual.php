<?php if ($_CVCVirtual_!='#CVCVirtual_included#') {$_CVCVirtual_='#CVCVirtual_included#';
  class CVCVirtual extends CVirtual {
    function CVCVirtual () { $this->SetClassName ('CVCVirtual'); }
    function SetDefaultSettings () {$this->SetClassName ('CVCVirtual');}
    function InnerHTML () {return '';} // Return container HTML code
    function OuterHTML () { // Return full HTML code
      return $this->PrefixHTML () . $this->InnerHTML () . $this->PostfixHTML ();
    }
    function PrefixHTML () {return '';}  // Return prefix HTML code
    function PostfixHTML () {return '';} // Return postfix HTML code
    function Draw () {print ($this->OuterHTML ());}
    function FromTemplate ($tpl,$args=array (),$parse=true) {return tpl ('back/vclasses/'.$this->GetClassName ().'/'.$tpl, $args, $parse);}
  }
  content_Register_VCClass ('CVCVirtual');
}
?>
