<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Cloud of tags
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

  if ($_CVCRegnum_ != '#CVCRegnum_Included#') {
    $_CVCContent_ = '#CVCRegnum_Included#';

    class CVCTagCloud extends CVCVirtual {
      var $tags = array ();

      function CVCTagCloud () { $this->SetClassName ('CVCTagCloud'); }

      function Init ($name, $tags = array (), $settings = '') {
        $params = unserialize_params ($settings);
        $this->SetSettings (combine_arrays ($this->GetSettings (), $params));

        $this->tags = $tags;
        $this->name = $name;
      }

      function InnerHTML () {
        global $CORE, $taglist_stuff_included;

        if (!isset ($taglist_stuff_included)) {
          $CORE->AddStyle ('tagcloud');
          $taglist_stuff_included = true;

          $CORE->AddScript ( 'language=JavaScript;',
                            $this->FromTemplate ('script', array (), false));
        }

        return $this->FromTemplate ('widget',
                                    array (
                                           'name'      => $this->name,
                                           'tags'      => $this->tags,
                                           'title'     => $this->settings['title'],
                                           'jshandler' => $this->settings['jshandler'],
                                           'userdata'  => $this->settings['userdata'],
                                           ));
      }
    }

    content_Register_VCClass ('CVCTagCloud');
  }
?>
