<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Compilers' stuff
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

  if ($_WT_compilers_included_ != '###WT_compilers_included###') {
    $_WT_compilers_included_ != '###WT_compilers_included###';

    global $WT_compilers_container;
    $WT_compilers_container = nil;

    class CGCompilersContainer {
      var $data;

      function CGCompilersContainer () {
        global $WT_Compilers;
        $this->data = $WT_Compilers;
      }
    
      function CompilerSelector ($allowed = '*', $formname = '',$active = '') {
        $n = count ($this->data);
        $res = '<select id="'.$formname.'_compiler" name="'.$formname.
          '_compiler" class="block">'."\n";

        if ($active == '') {
          $res .= '  <option value="">&lt;Не указан&gt;</option>'."\n";
        }

        for ($i = 0; $i < $n; $i++) {
          $it = $this->data[$i];
          if ($allowed == '*' || $allowed[$it['id']]) {
            $res .= '  <option value="'.
              $it['id'].'"'.(($it['id']==$active)?(' selected'):('')).'>'.
              $it['title'].'</option>'."\n";
          }
        }
        $res .= '</select>'."\n";
        return $res;
      }
    
      function DrawCompilerSelector ($allowed = '*',
                                     $formname = '', $active = '') {
        println ($this->CompilerSelector ($allowed, $formname, $active));
      }
    
      function GetList () {
        return $this->data;
      }
    }

    function WT_spawn_new_compilers_container () {
      global $WT_compilers_container;

      if ($WT_compilers_container != nil) {
        return $WT_compilers_container;
      }

      $WT_compilers_container = new CGCompilersContainer ();
      return $WT_compilers_container;
    }

    function WT_compiler_selector ($allowed = '*', $formname = '',
                                   $active = '') {
      $cnt = WT_spawn_new_compilers_container ();
      return $cnt->CompilerSelector ($allowed, $formname, $active);
    }

    function WT_draw_compiler_selector ($allowed = '*', $formname = '',
                                        $active = '') {
      $cnt = WT_spawn_new_compilers_container ();
      $cnt->DrawCompilerSelector ($allowed, $formname, $active);
    }

    function WT_receive_compiler_from_selector ($formname = '') {
      return $_POST[$formname.'_compiler'];
    }

    function WT_compiler_list () {
      $cnt = WT_spawn_new_compilers_container ();
      return $cnt->GetList ();
    }
  }
?>
