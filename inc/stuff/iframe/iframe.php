<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * IFrame editor
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

  if ($_IFrame_included_ != '#IFrame_Included#') {
    $_IFrame_included_ = '#IFrame_Included#';

    include 'helpers.php';

    $iframe_stuff_included = false;

    function iframe_include_stuff () {
      global $iframe_stuff_included, $CORE;

      if ($iframe_stuff_included) {
        return;
      }

      $CORE->AddStyle ('iframe');
      include 'scripts.js';
      tplp ('back/iframe/scripts');
      $iframe_stuff_included=true;
    }

    function iframe_editor ($name, $innerHTML = '',$init = true,
                            $handler_body = 'editor_form',
                            $settings = array ()) {
      iframe_include_stuff ();

      $res = tpl ('back/iframe/form',
                      array ('name' => $name,
                             'innerHTML' => iframe_prepare_content ($innerHTML),
                             'settings' => $settings));

      if ($init) {
        if (browser_engine () != 'DONKEY') {
          add_body_handler ('onload', 'iframeEditor_Init', array ('"'.$name.'"'));
        } else {
          $add = '<script language="JavaScript" type="text/javascript">'.
            'iframeEditor_Init ("'.$name.'");</script>';
        }
      }

      handler_add ($handler_body,  'onsubmit', 'iframeEditor_OnSubmit',
                   array ('"'.$name.'"'));

      return $res.$add;
    }

    function iframe_draw_editor ($name, $innerHTML = '', $init = true,
                                 $handler_body = 'editor_form',
                                 $settings = array ()) {
      print (iframe_editor ($name, $innerHTML, $init,$handler_body,$settings));
    }

    ////
    //

    function iframe_prepare_images ($val) {
      $val = preg_replace ("'(<img[\/\!]*?[^<>]*?)(src\s*=\s*\"?\\\${document-root}([\w\:\/\%\\$\#\.\,]*)\"?)([^<>]*?>)'si", '\1src="\3"\4', $val);
      $val = ecranvars ($val);
      $val = preg_replace ("'(<img[\/\!]*?[^<>]*?)(src\s*=\s*\"?\/([\w\:\/\%\$\#\.\,]*)\"?)([^<>]*?>)'si", '\1src="'.config_get ('http-document-root').'/\3"\4', $val);
      return $val;
    }

    function iframe_prepare_content ($val) {
      $val = iframe_prepare_images ($val);
      return $val;
    }

    ////
    //

    function iframe_accept_images ($val) {
      $root_patt = prepare_pattern (config_get ('http-document-root'));
      $val = preg_replace ("'(<img[\/\!]*?[^<>]*?)(ilo-full-src\s*=\s*\"?[\w\:\+\-\/\%\\$\#\.\,]*\"?)([^<>]*?>)'si", '\1\3', $val);
      $val = preg_replace ("'(<img[\/\!]*?[^<>]*?)(src\s*=\s*\"?$root_patt([\w\+\-\:\/\%\\$\#\.\,]*)\"?)([^<>]*?>)'si", '\1src="\3"\4', $val);

      $images = iframe_get_images ($val);
      $files = iframe_get_files ($val);

      $f = combine_arrays ($images, $files);
      for ($i = 0, $n = count ($f); $i < $n; $i++) {
        $storage=manage_storage_by_dir (dirname ($f[$i]));
        if ($storage) {
          $storage->AcceptFile (filename ($f[$i]));
        }
      }

      $val = preg_replace ("'(<img[\/\!]*?[^<>]*?)(src\s*=\s*\"?\/([\w\+\-\:\/\%\$\#\.\,]*)\"?)([^<>]*?>)'si", '\1src="${document-root}/\3"\4', $val);

      return $val;
    }

    function iframe_accept_content ($name, $old = '') {
      $val = stripslashes ($_POST[$name]);

      $val = preg_replace ("'(<a[\/\!]*?[^<>]*?)(href\s*=\s*\"?".config_get ('http-document-root')."([\w\+\-\:\/\%\$\#\.\,]*)\"?)([^<>]*?>)'si", '\1href="${document-root}\3"\4', $val);
      $val = preg_replace ("'(<a[\/\!]*?[^<>]*?)(href\s*=\s*\"?(http\\:\\/\\/)?\\$\\%7Bdocument-root\\%7D([\w\+\-\:\/\%\$\#\.\,]*)\"?)([^<>]*?>)'si", '\1href="${document-root}\4"\5', $val);
      $val = preg_replace ('/\?\>/', '?&gt;', preg_replace ('/\<\?/', '&lt;?', $val));

      $val = iframe_accept_images ($val);

      iframe_reaccept_content ($old, $val);
      return $val;
    }

    ////
    //

    function iframe_reaccept_images ($old, $new) {
      $old_images=iframe_get_images ($old);
      $new_images=iframe_get_images ($new);

      $old_files = iframe_get_files ($old);
      $new_files = iframe_get_files ($new);

      $old = combine_arrays ($old_images, $old_files);
      $new = combine_arrays ($new_images, $new_files);

      $arr = array ();
      for ($i = 0, $n = count ($old); $i < $n; $i++) {
        $found = false;
        for ($j = 0, $m = count ($new); $j < $m; $j++) {
          if ($old[$i] == $new[$j]) {
            $found = true;
            break;
          }
        }

        if (!$found) {
          $arr[] = $old[$i];
        }
      }

      for ($i = 0, $n = count ($arr); $i < $n; $i++) {
        $storage = manage_storage_by_dir (dirname ($arr[$i]));

        if ($storage) {
          $storage->UnlinkFile (basename ($arr[$i]));
        }
      }
    }

    function iframe_reaccept_content ($old, $new) {
      iframe_reaccept_images ($old, $new);
    }

    function iframe_destroy_content ($val) {
      iframe_reaccept_content ($val, '');
    }
  }
?>
