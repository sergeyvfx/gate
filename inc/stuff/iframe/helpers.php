<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Helpers for IFrame editor
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  global $IFACE;

  if ($IFACE != "SPAWNING NEW IFACE" || $_GET['IFACE'] != '') {
    print ('HACKERS?');
    die;
  }

  if ($_IFrame_helpers_included_ != '#IFrameHelpers_Included#') {
    $_IFrame_helpers_included_ = '#IFrameHelpers_Included#';

    function iframe_get_images ($src) {
      preg_match_all ("'<img[\/\!]*?[^<>]*?>'si", $src, $arr);

      $res=array ();
      for ($i = 0, $n = count ($arr); $i < $n; $i++) {
        for ($j = 0, $m = count ($arr[$i]); $j < $m; $j++) {
          $res[] = preg_replace ("'.*src\s*?\=\"(\\$\\{document-root\\})(.*)\".*'si",
                                 '\2', $arr[0][$j]);
        }
      }

      return $res;
    }

  function iframe_get_files ($src) {
    preg_match_all ("'<div class=\"file_pub\"><[^<]*<a[\/\!]*?[^<>]*?>'si", $src, $arr);
    $res = array ();

    for ($i = 0, $n = count ($arr); $i < $n; $i++)
      for ($j = 0, $m = count ($arr[$i]); $j < $m; $j++) {
        $res[] = preg_replace ("'.*href\s*?\=\"(\\$\\{document-root\\})(.*)\".*'si",
                               '\2', $arr[0][$j]);
      }

    return $res;
  }
}
?>
