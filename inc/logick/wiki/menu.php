<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Wiki menus
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

  if ($_wiki_NavMenu_included_ != '##wiki_NavMenu_Included##') {
    $_wiki_NavMenu_included_ != '##wiki_NavMenu_Included##';

    $wiki_menu_rules = array (
      array ('pattern' => '/\[url.*href\s*=\s*&quot;(.*)&quot;.*\]'.
             '(.*)\[\/url\]/si', 'replace'=>'<a href="\1">\2</a>')
    );
  
    $wiki_menu_global_vars = nil;

    function wiki_menu_parse_item ($src, $params) {
      global $wiki_menu_global_vars;

      if ($wiki_menu_global_vars == nil) {
        $wiki_menu_global_vars = array (
          'anon_redirect' => ((!user_authorized ())?('redirect='.
                                                     get_redirection ()):('')),
          'document_root' => config_get ('document-root')
                                      );
      }

      $vars = $wiki_menu_global_vars;
      foreach ($vars as $var => $val) {
        $src = preg_replace ('/\$'.$var.'/', $val, $src);
      }
  
      $params = array ();
      $modifers = preg_replace ('/^([\:lL]+)?(\s*)(.*)/si', '\1', $src);
      $data = preg_replace ('/^([\:lL]+)?(\s*)(.*)/si', '\3', $src);
      $parse = true;

      for ($i = 0; $i < count ($modifers); $i++) {
        if ($modifers[$i]==':') $parse = false;
        if ($modifers[$i]=='l' && !user_authorized ()) return false;
        if ($modifers[$i]=='L' && user_authorized ()) return false;
      }

      if (!$parse) {
        $src = $data;
      } else {
        global $wiki_menu_rules;
        $src = htmlspecialchars ($data);
        $n = count ($wiki_menu_rules);
        for ($i = 0; $i < $n; $i++)
          $src = preg_replace ($wiki_menu_rules[$i]['pattern'],
                               $wiki_menu_rules[$i]['replace'], $src);
      }
      return $src;
    }

    function wiki_items_src ($items, $indent = 0) {
      $prefix = '    ';

      for ($i = 0; $i < $indent; $i++) {
        $prefix .= '  ';
      }

      $n = count ($items);
      $opened = false;

      for ($i = 0; $i < $n; $i++) {
        $nomarker = $items[$i]['nomarker'];

        if (!$nomarker && !$opened) {
          $res .= "$prefix<ul>\n";
          $opened=true;
        }

        if ($nomarker && $opened) {
          $res .= "$prefix</ul>\n";
          $opened = false;
        }

        $res .= "$prefix  ".(($nomarker)?(''):('<li>')).$items[$i]['src'];

        if (count ($items[$i]['items']) > 0) {
          $res .= "\n".wiki_items_src ($items[$i]['items'], $indent + 2).
            $prefix.'  ';
        }
          $res .= (($nomarker)?(''):("</li>\n"));
      }

      if ($opened) {
        $res .= "$prefix</ul>\n";
      }

      return $res;
    }

    function wiki_menu_parse ($src) {
      $menus = array ();
      $menu_uk = -1;
      $src = preg_replace ('/\<\!--.*--\>/si', '', $src);
      $res = '';
      $n = strlen ($src);
      $curDepth = 0;
      $menuItems = array ();

      while ($i < $n) {
        // Get da string
        $buf = '';
        while ($src[$i] <= ' ' && $i<$n) {
          $i++;
        }

        while ($src[$i] != "\n" && $src[$i] != "\r" && $i < $n)  {
          $buf .= $src[$i];
          $i++;
        }

        if ($buf[0] == '+') { // Start new menu
          $menu_uk++;
          $caption = preg_replace ('/^\+\s*/si', '', $buf);
          $menus[$menu_uk]['caption'] = $caption;
          $curDepth = 0;
          $menuItems[0] = &$menus[$menu_uk]['items'];
        } else {
          if ($buf[0] == '.' || $buf[0] == ',') {
            $depth = strlen (preg_replace ('/([\.\,]+).*/si', '\1', $buf)) - 1;

            if ($depth > $curDepth) {
              $menuItems[$curDepth+1] =
                &$menuItems[$curDepth][count ($menuItems[$curDepth])-1]['items'];
              $curDepth++;
            } else {
              $curDepth = $depth;
            }

            $item = preg_replace ('/([\.\,]+\s*)(.*)/si', '\2', $buf);
            $s = wiki_menu_parse_item ($item, &$params);
            if ($s != false) {
              $menuItems[$curDepth][]=array ('src' => $s,
                                             'nomarker' => $buf[0] == ',');
            }
          }
        }
        $i++;
      }

      // Parse menu arr
      $n = count ($menus);
      for ($i = 0; $i < $n; $i++) {
        $res .= "<div class=\"menu\"".
          (($i>0)?(' style="padding-top: 8px;"'):('')).">\n";
        $res .= "  <div class=\"cpt\">".$menus[$i]['caption']."</div>\n";
        $res .= "  <div class=\"body\">\n";
        $res .= wiki_items_src ($menus[$i]['items']);
        $res .= "  </div>\n";
        $res .= "</div>\n";
      }
      return $res;
    }
  }
?>