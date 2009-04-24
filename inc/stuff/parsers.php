<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Different parsers
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

  if ($_parsers_included_ != '#_parsers_Included_#') {
    $_parsers_included_ = '#_parsers_Included_#';

    include $DOCUMENT_ROOT.'/inc/stuff/fakecode.php';

    $VARS = null;  

    // Strip da suspicious code
    function strip_suspicious ($s) {
      $res=preg_replace ('/<\?.*?>/', '', $s);
      $res=preg_replace ('/<\?.*$/', '', $res);
      return $res;
    }

    function prepare_pattern ($s) {
      return preg_replace ('/([\/\?\+\.\^\$])/', '\\\\\1', $s);
    }

    function posted_html_string ($s) {
      return htmlspecialchars (stripslashes ($s));
    }

    function prepare_arg ($s) {
      return preg_replace ('/([\ \=;])/', '\\\\\1', $s);
    }

    function ecranvars ($s) {
      $s = preg_replace ('/\${information}/'  , '$$\$123###-INFORMATION-###321$$$', $s);
      $s = preg_replace ('/\${document-root}/', '$$\$123###-DOCUMENT-ROOT-###321$$$', $s);
      return $s;
    }

    function deecranvars ($s) {
      $s = preg_replace ('/\$\$\$123###-INFORMATION-###321\$\$\$/', '${information}', $s);
      $s = preg_replace ('/\$\$\$123###-DOCUMENT-ROOT-###321\$\$\$/', '${document-root}', $s);
      return $s;
    }

    function setvars ($s) {
      global $VARS;

      if (!$VARS) {
        $VARS = array ('document-root' => config_get ('document-root'));
      }

      foreach ($VARS as $k => $v) {
        $s = preg_replace ('/\${'.prepare_pattern ($k).'}/', $v, $s);
      }

      return $s;
    }

    function parseint ($s) {
      $r = preg_replace ('/(.*?)([0-9]*)(.*?)/si', '\2', $s);

      if ($r != '') {
        return $r;
      }

      return '0';
    }

    function html2txt ($s) {
      $search = array ("'<script[^>]*?>.*?</script>'si",  // Strip javaScript
                       "'<[\/\!]*?[^<>]*?>'si",           // Strip HTML tags
                       "'([\r\n])[\s]+'",                 // Strip space characters
                       "'&(quot|#34);'i",
                       "'&(amp|#38);'i",
                       "'&(lt|#60);'i",
                       "'&(gt|#62);'i",
                       "'&(nbsp|#160);'i",
                       "'&(iexcl|#161);'i",
                       "'&(cent|#162);'i",
                       "'&(pound|#163);'i",
                       "'&(copy|#169);'i",
                       "'&#(\d+);'e");

      $replace = array ("", "", "\\1", "\"", "&", "<", ">", " ",
                        chr(161), chr(162), chr(163), chr(169), "chr(\\1)");

      return preg_replace ($search, $replace, $s);
    }
  }
?>
