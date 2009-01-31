<?php if ($_CVCRegnum_!='#CVCRegnum_Included#') {$_CVCContent_='#CVCRegnum_Included#';
  class CVCCaptcha extends CVCVirtual {
    function CVCCaptcha () { $this->SetClassName ('CVCCaptcha'); }
    function Init ($name='', $settings='') {
      $this->SetDefaultSettings ();
      $this->contents=array ();

      $params=unserialize_params ($settings);
      $this->SetSettings (combine_arrays ($this->GetSettings (), $params));
    }
    function SetDefaultSettings() { $this->SetClassName ('CVCCaptcha'); }

    function InnerHTML () {
      return '<img src="'.config_get ('document-root').'/inc/stuff/captcha/data.php">';
    }
  }

  content_Register_VCClass ('CVCCaptcha');
}
?>
