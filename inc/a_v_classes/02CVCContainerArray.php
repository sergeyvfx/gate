<?php if ($_CVCContainerArray_!='#CVCContainerArray_Included#') {$_CVCContainerArray_='#CVCContainerArray_Included#';
  class CVCContainerArray extends CVCVirtual {
    var $containers;
    var $containerLinks;
    function CVCContainerArray () { $this->SetClassName ('CVCContainerArray'); }
    function Init () {
      $this->SetDefaultSettings ();
      $cintainers = array ();
      $containerLinks = array ();
    }

    function SetDefaultSettings() {$this->SetClassName ('CVCContainer');}

    function InnerHTML () {
      $result = '';
      for ($i=0; $i<count ($this->containers); $i++) {
        $container = $this->containers[$i];
        $result .= $container->OuterHTML ();
      }
      return $result;
    }

    function AppendContainer ($container, $name='') {
      $this->containerLinks[$container->GetName ()] = &$container;
      $this->containers[] = $container;
    }

    function GetContainerByName ($name) {return $this->containerLinks[$name];}
    function GetContainerByNumber ($i) {return $this->containers[$i];}
  }
  
  content_Register_VCClass ('CVCContainerArray');
}
?>
