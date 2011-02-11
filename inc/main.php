<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main stuff (entry point0
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

  if ($_main_included_ != '#main_Included#') {
    $_main_included_ = '#main_Included#'; 
    $contentPage = null;

    global $CORE, $XPFS;
    $CORE = $XPFS = nil;

    class CCore {
      var $PAGE;
      var $wiki = false, $URL = '';

      function ActionHandler () {
        global $action;
        if ($action == 'login') {
          header ('Location: '.config_get ('document-root').'/login');
        }

        if ($action == 'logout') {
          user_logout ();
          redirect ();
        }
      }

      function DeleteUnwanted () {
        $storages = manage_storage_get_list ();

        $n = count ($storages);
        for ($i = 0; $i < $n; $i++) {
          $storages[$i]->DeleteUnwanted ();
        }

        user_delete_unwanted ();
      }

      function CCore ($url = '', $wiki = true) {
        global $DOCUMENT_ROOT, $content_type, $CORE, $ipc, $XPFS;
        debug_watchdog_clear ();

        if ($url == '') $url = config_get ('document-root');

        $this->wiki = $wiki;
        $this->URL  = $url;

        $this->PAGE = new CVCPage ();
        $this->PAGE->Init ('title=Тризформашка;');
        $CORE = $this;

        // Starting session
        session_start ();

        // Set the internal encoding
        mb_internal_encoding (config_get ('internal-charset'));

        // Connect to database
        db_connect (config_get ('check-database'));

        // Initialize XPFS
        $XPFS = new XPFS ();
        $XPFS->createVolume ();

        // Initialize content stuff
        content_initialize ();

        // Initialize wiki stuff
        wiki_initialize ();

        // Initialie manage stuff
        manage_initialize ();
        security_initialize ();
        tipsling_initialize();
        ipc_initialize ();

        service_initialize ();
        editor_initialize ();

        $this->DeleteUnwanted ();

        if ($ipc != '') {
          ipc_exec ($ipc);
          die;
        } else {
          // Make default actions
          $this->ActionHandler ();

          // Creating page
          $this->PAGE->AddStyle ('content');
          $this->PAGE->AddStyle ('pages');
          $this->PAGE->AddScript ('language=JavaScript;type=text/javascript', "\n".tpl ('common/globals', array (), false));
          $this->PAGE->AddScriptFile ('core.js');
          $this->PAGE->AddMeta ('http-equiv=content-language;content='.config_get ('content-language'));
          $this->PAGE->AddMeta ('name=url;content='.config_get ('meta-url'));
          $this->PAGE->AddMeta ('name=keywords;content='.config_get ('meta-keywords'));
          $this->PAGE->AddMeta ('name=description;content='.config_get ('meta-description'));
          $this->PAGE->AddMeta ('http-equiv=Content-Type;content=text/html\; charset\='.config_get ('character-set'));
          $this->PAGE->AddMeta ('name=robots;content=all');
          $this->PAGE->SetIcon (config_get ('document-root').'/pics/favicon.ico');
          add_body_handler ('onmousemove', 'core_StoreMousePos', array ('event'));

          if (browser_engine () == 'OPERA') {
            $this->PAGE->AddStyle ('content_opera_rep');
          }

          foreach (config_get ('default-scripts') as $k) {
            $this->AddScriptFile ($k);
          }
          $this->PAGE->AddScript ('language=JavaScript;type=text/javascript', "\n".tpl ('common/googleanalytics', array (), false));
        }
      }

      function ReturnContents () {
        global $CORE;

        if (!check_locked ()) {

          // Getting the content and printing it to page
          if (!$this->wiki) {
            $content_type = "wiki";

            if (nav_inside ('/admin') >= 0) {
              $this->PAGE->AppendTitle ('Администрирование', true);
            }

            $this->PAGE->TPrint (content_static_page ($this->URL));
          } else {
            $content_type = "system";
            $this->PAGE->TPrint (wiki_get_page ($this->URL));
          }
        } else {
          die;
        }
      }

      function DrawContents () { $this->PAGE->Draw (); }

      function AddScript     ($p, $s) { $this->PAGE->AddScript ($p,$s); }
      function AddScriptFile ($f)     { $this->PAGE->AddScriptFile ($f); }

      function AddStyle ($f) { $this->PAGE->AddStyle ($f); }
    }

    function spawn_new_core ($url = '', $wiki = true) {
      global $CORE;

      if ($CORE != nil) {
        return $CORE;
      }

      $CORE = new CCore ($url, $wiki);

      return $CORE;
    }

    function Main ($url = '', $wiki = true) {
      $CORE = spawn_new_core ($url, $wiki);
      $CORE->ReturnContents ();
      $CORE->DrawCOntents ();
    }
  }
?>
