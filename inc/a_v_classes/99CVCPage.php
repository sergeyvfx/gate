<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * The whole page
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

  if ($_CVCPage_ != '#CVCPage_Included#') {
    $_CVCPage_ = '#CVCPage_Included#';

    global $main_page;

    class CVCPage extends CVCVirtual {
      var $metas;
      var $scripts;
      var $CSStyles;
      var $content;
      var $topmenu;
      var $information;
      var $appenedScripts;
      var $vars;

      function CVCPage ($v = '') { $this->SetClassName ('CVCPage'); }
      function Init ($v='') {
        $this->vars=array ();

        $this->SetDefaultSettings ();
        $this->metas = array ();
        $this->CSStyles = array ();
        $this->content = new CVCContent;
        $params = unserialize_params ($v);
        $this->topmenu = new CVCMenu;
        $this->topmenu->Init ('PageTopMenu',
                              'type=hor;transparent=1;align=right;small=1;');
        $this->settings['favicon'] = '';
        $this->SetSettings (combine_arrays ($this->GetSettings (), $params));
      }

      function SetDefaultSettings () { $this->SetClassName ('CVCPage'); }

      function AddInfo ($txt) { $this->information .= $txt; }

      function SetIcon ($ico) { $this->settings['favicon'] = $ico; }
      function GetIcon ()     { return $this->settings['favicon']; }

      function SetRSS ($title, $href) {
        $this->settings['rss'] = array ('title' => $title, 'href' => $href);
      }

      function GetRSS () { return $this->settings['rss']; }

      function AddMeta ($m) {
        $meta = new CMMeta;
        $meta->Init ($m);
        $this->metas[] = $meta;
      }

      function AddScript ($params, $src = '') {
        $script = new CMScript;
        $script->Init ($params,$src);
        $this->scripts[] = $script;
      }

      function AddScriptFile ($file, $lang = 'JavaScript') {
        if ($this->appenedScripts[$file]) {
          return;
        }

        $this->appenedScripts[$file] = true;
        $script = new CMScript;
        $script->Init ('language='.$lang.';type=text/'.$lang.
                       ';src='.config_get ('document-root').
                       '/scripts/'.$file, '');
        $this->scripts[] = $script;
      }

      function AddStyle ($fn) { $this->CSStyles[] = $fn; }

      ////////////////////////////////////////////////////////
      // Working with content
      function TPrint ($text) { $this->content->TPrint ($text); }
      function CFree () { $this->content->Free (); }

      ////////////////////////////////////////////////////////
      // Generating page source
      function CMSource ($item) {
        $result = '';
        for ($i = 0; $i<count ($item); $i++) {
          $result = swriteln ($result, $item[$i]->Source ());
        }

        return $result;
      }

      function MetasSource () { return ($this->CMSource ($this->metas)); }
      function ScriptsSource () { return ($this->CMSource ($this->scripts)); }

      function HeadSource () {
        $result = '';
        $result = swriteln ($result, '<title>'.$this->GetSetting('title').
                                     '</title>');

        // Styles
        $p = config_get ('document-root').'/styles/';
        foreach ($this->CSStyles as $s) {
          $result = swriteln ($result, $this->FromTemplate ('link',
              array ('rel'=>'stylesheet', 'type'=>"text/css",
                     'href'=>$p.$s.'.css')));
        }

        $result .= $this->MetasSource ();

        // favicon
        $ico = $this->GetIcon ();
        if ($ico != '') {
          $result = swriteln ($result, $this->FromTemplate ('link',
              array ('rel' => 'icon', 'href' => $ico,
              'type' => 'image/x-icon')));

          $result = swriteln ($result, $this->FromTemplate ('link',
              array ('rel' => 'SHORTCUT ICON', 'href'=>$ico)));
        }

        $rss = $this->GetRSS  ();
        if ($rss['href'] != '') {
          $result = swriteln ($result, $this->FromTemplate ('link',
            array ('rel' => 'alternate', 'type' => 'application/rss+xml',
              'title' => $rss['title'], 'href' => $rss['href'])));
        }

        $result .= $this->ScriptsSource ();
        return $result;
      }

      function ContentSource () {
        $result = '';

        for ($i = 0; $i < count ($this->contents); $i++) {
          $content = $this->contents[$i];
          if ($content['type'] == 'text') {
            $result .= $content['text'];
          }
        }

        return $result;
      }

      function FillTopMenu () {
        if (!user_authorized ()) {
          $this->topmenu->AppendItem ('Представиться системе / Зарегистрироваться', config_get ('document-root').'/login?redirect='.get_redirection (), 'logout', 'key.gif');
        }

        if (!user_authorized () || user_access_root ()) {
          if (nav_inside ('/admin')>=0) {
            $this->topmenu->AppendItem ('Основной раздел', config_get ('document-root').'/', 'main', '');
          } else {
            $this->topmenu->AppendItem ('Административный интерфейс', config_get ('document-root').'/admin/?redirect='.get_redirection (), 'main', '');
          }
        }

        if (user_authorized ()) {
          $this->topmenu->AppendItem ('Выйти из системы', config_get ('document-root').'/?action=logout&redirect='.get_redirection (), 'logout', 'lock.gif');
        }
      }

      function SetVars ($content) {
        $content = preg_replace ('/\${information}/',
            stencil_info ($this->information), $content);
        $content = setvars ($content);

        foreach ($this->vars as $k=>$v) {
          $content = preg_replace ('/\${'.prepare_pattern ($k).'}/', $v, $content);
        }

        $content = deecranvars ($content);

        return $content;
      }

      function InnerHTML () {
        $content = $this->content->InnerHTML ();

        ob_start ();
        eval ('?>'.$content);
        $content = ob_get_contents ();
        ob_end_clean ();

        $content = $this->SetVars ($content);
        $this->FillTopMenu ();
        $topmenu = $this->topmenu->InnerHTML ();
        $head = $this->HeadSource ();
        return tpl ('common/index', array ('head' => $head,
            'content' => $content, 'topmenu' => $topmenu));
      }

      function SetTitle    ($v) { $this->settings['title']=$v; }
      function AppendTitle ($v,$last = false) {
        $this->settings['title'].=(($last)?(' - '):( (($this->title_appended>1)?(' || '):(': ')) )).$v;
        $this->title_appended++;
      }
    }

    content_Register_VCClass ('CVCPage');
  }
?>
