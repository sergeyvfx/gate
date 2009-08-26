<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * PAgintation class
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

  if ($_CVCPagintation_ != '#CVCPagintation_Included#') {
    $_CVCPagintation_ = '#CVCPagintation_Included#';
    class CVCPagintation extends CVCVirtual {
      var $pages = array ();
      var $defaultStencil = array (
        'outer' => '<div class="pagintation">Страницы: $(items)</div>',
        'item' => ' <a href="$(href)" title="$(hint)">$(title)</a>',
        'activeItem' => ' <a href="$(href)" title="$(hint)" class="active">[$(title)]</a>'
      );
      var $topStencil;
      var $bottomStencil;
      var $header = '';
      var $footer = '';

      function CVCPagintation () { $this->SetClassName ('CVCPagintation'); }
      function Init ($name = '', $settings = '') {
        $params = unserialize_params ($settings);
        $this->settings['name'] = $name;
        $this->SetSettings (combine_arrays ($this->GetSettings (), $params));

        if ($this->settings['urlprefix'] == '') {
          $this->settings['urlprefix'] = content_url_get_full ();
        }

        $s = $this->settings['urlprefix'];
        if (preg_match ('/\?/', $s)) {
          if (!preg_match ('/\?$/', $s)) {
            $s.='&';
          }
        } else {
          $s.='?';
        }

        $this->settings['urlprefix'] = $s;

        if ($this->settings['pageid'] == '') {
          $this->settings['pageid'] = 'page';
        }

        $this->topStencil = $this->bottomStencil = $this->defaultStencil;
        if ($this->settings['perpage'] == '') {
          $this->settings['perpage'] = 1;
        }
      }

      function CopyStencil ($s) {
        return array ('outer' => $s['outer'], 'item' => $s['item']);
      }

      function SetTopStencil ($s) {
        $this->topStencil = $this->CopyStencil ($s);
      }

      function SetBottomStencil ($s) {
        $this->bottomStencil = $this->CopyStencil ($s);
      }

      function AppendPage ($src, $hint = '') {
        $this->pages[] = array ('src' => $src, 'count' => $this->settings['perpage'],
                                'hint' => $hint);
      }

      function AppendItem ($src) {
        $uk = count ($this->pages) - 1;
        if ($uk < 0 || $this->pages[$uk]['count']>$this->settings['perpage']) {
          $this->pages[] = array ('src' => $src, 'count' => 1);
        } else {
          $this->pages[$uk]['src'] .= $src;
          $this->pages[$uk]['count']++;
        }
      }

      function GetActivePage () {
        $pageid = $this->settings['pageid'];
        $active = $this->settings['active'];

        if ($active == '') $active = $GLOBALS[$pageid];
        if ($active == '') $active=0;

        if ($active < 0) return 0;

        if ($active >= count ($this->pages)) {
          return count ($this->pages) - 1;
        }

        return $active;
      }

      function GetPagintation ($stencil) {
        if ($this->settings['skiponcepage'] && count ($this->pages) <= 1) {
          return '';
        }

        $items = '';
        $itStencil = $stencil['item'];
        $itActiveStencil = $stencil['activeItem'];
        $urlprefix = $this->settings['urlprefix'];
        $pageid = $this->settings['pageid'];
        $active = $this->GetActivePage ();

        for ($i = 0; $i < count ($this->pages); $i++) {
          if ($i != $active) {
            $st = $itStencil;
          } else {
            $st = $itActiveStencil;
          }

          $itemTmp = preg_replace ('/\$\(hint\)/', $this->pages[$i]['hint'], $st);
          $itemTmp = preg_replace ('/\$\(title\)/', $i + 1, $itemTmp);
          $itemTmp = preg_replace ('/\$\(href\)/', $urlprefix.$pageid.
                                     '='.$i, $itemTmp);
          $items.=$itemTmp;
        }

        $res = preg_replace ('/\$\(items\)/', $items, $stencil['outer']);
        return $res;
      }

      function SetCurrentPage ($n) {
        $this->settings['active'] = $n;
      }

      function GetTopPagintation () {
        return $this->GetPagintation ($this->topStencil);
      }

      function GetBottomPagintation () {
        return $this->GetPagintation ($this->bottomStencil);
      }

      function SetHeader ($v) { $this->header=$v; }
      function SetFooter ($v) { $this->footer=$v; }

      function InnerHTML () {
        $res = $this->GetTopPagintation ();
        $res .= $this->pages[$this->GetActivePage ()]['src'];

        if (!$this->settings['bottomPages']) {
          $res.=$this->GetBottomPagintation ();
        }

        return $this->header.$res.$this->footer;
      }
     }

     content_Register_VCClass ('CVCPagintation');
  }
?>
