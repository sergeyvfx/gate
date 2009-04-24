<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Tab control
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

  if ($_CVCTabCtrl_ != '#CVCTabCtrl_Included#') {
    $_CVCTabCtrl_ = '#CVCTabCtrl_Included#';

    class CVCTabCtrl extends CVCVirtual {
      var $pageStarted = false;
      var $pageParams = array ('title');
      var $pages = array ();
      var $active = -1;

      function CVCTabCtrl () { $this->SetClassName ('CVCForm'); }

      function Init ($name = '', $settings = '') {
        $params = unserialize_params ($settings);
        $this->settings['name'] = $name;
        $this->SetSettings (combine_arrays ($this->GetSettings (), $params));
      }

      function StartNewPage ($params) {
        if ($this->pageStarted) {
          $this->FinishPage ();
        }

        $p = unserialize_params ($params);
        if ($p['active']) {
          $this->active = count ($this->pages);
        }

        $n = count ($this->pageParams);
        $arr = array ();

        for ($i = 0; $i < $n; $i++) {
          $arr[$this->pageParams[$i]] = $p[$this->pageParams[$i]];
        }

        $this->pages[] = $arr;
        $this->pageStarted = true;
        ob_start ();
      }

      function FinishPage () {
        $i = count ($this->pages)-1;
        $this->pages[$i]['src'] = ob_get_contents ();
        $this->pageStarted = false;
        ob_end_clean ();
      }

      function SetActive ($n) {
        if ($n<0) {
          $n=0;
        }

        if ($n >= count ($this->pages)) {
          $n=count ($this->pages) - 1;
        }

        $this->active=$n;
      }

      function OuterHTML () {
        $res = '';
        $n = count ($this->pages);
        $p = "count=$n;";

        for ($i = 0; $i < $n; $i++) {
          $p.='tab'.$i.'='.prepare_arg ($this->pages[$i]['title']).';';
        }

        if ($this->settings['name'] == '') {
          $suff = 'tabctrl';
        } else {
          $suff = $this->settings['name'];
        }

        if (isset ($GLOBALS[$suff]) && $GLOBALS[$suff]!='') {
          $active = $GLOBALS[$suff];
        } else {
          $active = 0;
        }

        if ($active < 0) {
          $active = 0;
        }

        if ($active >= count ($this->pages)) {
          $active = count ($this->pages) - 1;
        }

        $p .= ';active='.$active;

        $url = $this->settings['url'];
        if ($url == '') {
          $url=content_url_get_full ();
        }
        $p .= ';url='.prepare_arg ($url);

        $res .= stencil_tabo ($p.';suff='.
                                 prepare_arg ($this->settings['name']));
        $res .= $this->pages[$active]['src'];
        $res .= stencil_tabc ();

        return $res;
      }
    }
    content_Register_VCClass ('CVCTabCtrl');
  }
?>
