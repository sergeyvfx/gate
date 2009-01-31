<?php if ($_CVirtual_!='#CVirtual_included#') {$_CVirtual_='#CVirtual_included#';
  class CVirtual {
    var $className;
    var $settings=array ();

    function Init () { $this->SetDefaultSettings (); }

    function GetClassName ()   { return $this->className; } // Get className
    function SetClassName ($v) { $this->className=$v; }     // Set className

    function GetSettings ()   { return $this->settings; }   // Get all settings
    function SetSettings ($s) { // Set settings
      if (!is_array ($s)) { return; }
      foreach ($s as $k=>$v)
        $this->settings[$k]=$v;
    }

    function SetDefaultSettings() {$this->settings = array ();} // Sets the default settings

    function GetSetting ($s) {return $this->settings[$s];} // Get single setting
    function SetSetting ($s,$v) {$this->settings[$s]=$v;}  // Set single setting
    function UpdateSettings ($s) {
      if (!is_array ($s)) return;
      foreach ($s as $k=>$v)
        $this->settings[$k]=$v;
    }

    function SerializeSettings () {return serialize ($this->settings);}
    function UnserializeSettings ($s) {
      $settings=unserialize ($s);
      if (is_array (&$settings))
        $this->settings=$settings; else
        $this->SetDefaultSettings ();
    }
  }

  content_Register_VCClass ('CVCVirtual', '');
}
?>
