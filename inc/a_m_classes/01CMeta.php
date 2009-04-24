<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Class for common meta tag
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

  if ($_CMMeta_ != '#CMMeta_Included#') {
    $_CMMeta_ = '#CMMeta_Included#';

    class CMMeta extends CMVirtual {
      function CMMeta () { $this->SetClassName ('CMMeta'); }

      function Init ($params) {
        $this->params = unserialize_params ($params);
      }

      function Source () {
        $result = '<meta';

        foreach ($this->params as $k=>$v) {
          if (trim ($v)!='')
            $result.=" $k=\"$v\""; else
            $result.=" $k";
        }

        $result .= '>';

        return $result;
      }
    }
  }
?>
