<?php if ($_CMMeta_!='#CMMeta_Included#') {$_CMMeta_='#CMMeta_Included#';
  class CMMeta extends CMVirtual {
    function CMMeta () { $this->SetClassName ('CMMeta'); }
    function Init ($params) {
      $this->params=unserialize_params ($params);
    }
    function Source () {
      $result='<meta';
      foreach ($this->params as $k=>$v) {
        if (trim ($v)!='')
          $result.=" $k=\"$v\""; else
          $result.=" $k";
      }
      $result.='>';
      return $result;
    }
  }
}
?>
