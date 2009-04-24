<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Fake-code re-placer
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

  if ($_FAKECODE_Included_ != '###FAKE_Included###') {
    $_FAKECODE_Included_ == '###FAKE_Included###';

    global $fake_rules;

    $fake_rules = array (
      // Font formation
      array ('replace', '/\[b\](.*?)\[\/b]/si', '<b>\1</b>'),
      array ('replace','/\[i\](.*?)\[\/i]/si', '<i>\1</i>'),
      array ('replace', '/\[u\](.*?)\[\/u]/si', '<u>\1</u>'),
      array ('replace', '/\[s\](.*?)\[\/s]/si', '<s>\1</s>'),

      // Text color
      array ('replace', '/\[color\s*\=\s*([A-Z]+)](.*?)\[\/color]/si',
             '<font style="color: \1">\2</font>'),
      array ('replace', '/\[color\s*\=\s*(#[0-9A-F]+)](.*?)\[\/color]/si',
             '<font style="color: \1">\2</font>'),

      // Font size
      array ('replace', '/\[size\s*\=\s*([0-9]+)](.*?)\[\/size]/si',
             '<font style="font-size: \1pt">\2</font>'),

      // Lists
      array ('callback','/\[list](.*?)\[\/list]/si', 'fake_ulist_callback'),
      array ('callback','/\[list\s*\=\s*1](.*)\[\/list]/si', 'fake_olist_callback'),

      // Links
      array ('replace', '/\[url\](.*?)\[\/url\]/si', '<a href="\1">\1</a>'),
      array ('replace', '/\[url\s*=\s*(.*?)\](.*?)\[\/url\]/si', '<a href="\1">\2</a>')
    );

    function fake_list_callback_entry ($type, $match) {
      $r =  $match[1];
      $r = str_replace ('[*]', '</li><li>', $r);
      $r = preg_replace ('/^\\<li\>/si', '', $r);
      $r .= '</li>';
      return '<'.$type.'l>'.$r.'</'.$type.'l>';
    }

    function fake_ulist_callback ($match) {
      return fake_list_callback_entry ('u', $match);
    }

    function fake_olist_callback ($match) {
      return fake_list_callback_entry ('o', $match);
    }

    function fake_apply_one_rule ($s, $rule) {
      if ($rule[1] == '') {
        return $s;
      }

      if ($rule[0] == 'replace') {
        $s=preg_replace ($rule[1], $rule[2], $s);
      }

      if ($rule[0] == 'callback') {
        $s = preg_replace_callback ($rule[1], $rule[2], $s);
      }

      return $s;
    }

    function fake_apply_rules ($s) {
      global $fake_rules;
      $n = count ($fake_rules);

      for ($i = 0; $i < $n; $i++) {
        $s = fake_apply_one_rule ($s, $fake_rules[$i]);
      }

      return $s;
    }

    function fakecode ($s) {
      $s = htmlspecialchars ($s);

      // Parsing new lines
      $s = str_replace ("\n", '<br>', str_replace ("\r\n", '<br>', $s)); 

      $s = fake_apply_rules ($s);
      return $s;
    }
  }
?>
