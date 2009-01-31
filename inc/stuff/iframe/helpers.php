<?php if ($_IFrame_helpers_included_!='#IFrameHelpers_Included#') {$_IFrame_helpers_included_='#IFrameHelpers_Included#';
  function iframe_get_images ($src) {
    preg_match_all ("'<img[\/\!]*?[^<>]*?>'si", $src, $arr);
    
    $res=array ();
    for ($i=0, $n=count ($arr); $i<$n; $i++)
      for ($j=0, $m=count ($arr[$i]); $j<$m; $j++) {
        $res[]=preg_replace ("'.*src\s*?\=\"(.*)\".*'si", '\1', $arr[0][$j]);
      }

    return $res;
  }
}
?>