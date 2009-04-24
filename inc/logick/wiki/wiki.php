<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Main Wiki stuff
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

  if ($_wiki_included_ != '#wiki_Included#') {
    $_wiki_included_ = '#wiki_Included#';

    function wiki_initialize () {
      if (config_get ('check-database')) {
        if (!db_table_exists ('content')) {
          db_create_table_safe ('content', array (
                                  'id'       => 'INT NOT NULL PRIMARY '.
                                                'KEY AUTO_INCREMENT',
                                  'order'    => 'INT',
                                  'pid'      => 'INT DEFAULT 1',
                                  'class'    => 'TEXT',
                                  'name'     => 'TEXT',
                                  'path'     => 'TEXT',
                                  'settings' => 'TEXT DEFAULT ""'
                                                  ));
          db_insert ('content', array ('order' => '1', 'pid' => '0',
                                       'name' => '"Корневой раздел"',
                                       'path' => '"/"',
                                       'settings' => '"'.
                                       addslashes ('a:1:{s:8:"security";a:1:'.
                '{s:3:"ALL";a:2:{s:5:"order";s:10:"allow_deny";s:4:"acts";'.
                'a:1:{i:0;a:2:{s:3:"act";s:8:"AllowAll";s:3:"val";'.
                's:0:"";}}}}}').'"'));
        }
      }
    }

    function wiki_get_indexfile ($action) {
      $file = config_get ('wiki-index');

      if ($action == 'edit') {
        $file = 'edit.php';
      }

      if ($action == 'history') {
        $file = 'history.php';
      }

      return $file;
    }

    function wiki_get_page_src ($url, $action = '') {
      $file = wiki_get_indexfile ($action);
      $url .= $file;
      $root = config_get ('site-root');
      $fn = "$root$url";

      if (($src=get_file ($fn)) == false) {
        return false;
      }

      return $src;
    }

    function wiki_eval_page ($url, $wiki, $error) {
      $error = false;

      if (($src = wiki_get_page_src ($url, $wiki)) == false) {
        $error = true;
        return stencil_core_page (
          content_error_page ('404',
                          array ('url' => $url.wiki_get_indexfile ($wiki))));
      }
      return eval_code ($src);
    }

    function wiki_get_page ($url) {
      global $wiki, $history, $oldid, $uid;
      $tabacts = array ('edit' => 1, 'history' => 1, 'admin' => 1);

      if ($url == '') {
        $url = '/';
      }

      if ($url[strlen ($url)-1] != '') {
        $url.='/';
      }

      content_url_var_push ('wiki', $wiki);
      redirector_add_skipvar ('oldid');

      $error = false;
      $src = wiki_eval_page ($url, $wiki, &$error);
      if ($error) {
        return $src;
        return;
      }

      $cur = prepare_arg (get_redirection (false, true));
      $items = array ();
      $uidurl = (($uid!='')?('uid\='.$uid):(''));
      $dir = $url;

      if ($url[strlen ($url) - 1] == '/') {
        $url .= '?';
      } else {
        $url .= '&';
      }

      if ($uidurl != '') {
        $uidurl = '&'.$uidurl;
      }

      $items[] = 'title=Статья;hint=Чтение статьи;url='.
        ((isset ($tabacts[$wiki]))?($url.$uidurl):
           ('JavaScript:refreshPage (\''.
            urlencode (urlencode ($cur)).'\');')).';active='.
          ((!isset ($tabacts[$wiki]))?('1'):('0')).';';

      if (content_get_allowed ('EDIT') || content_get_allowed ('EDITINFO') ) {
        $items[] = 'separator=1;';
        if (file_exists (config_get ('site-root').$dir.'edit.php')) {
          $items[] = 'title=Редактирование;hint=Редактирование '.
            'содержимого страницы;url='.(($wiki!='edit')?($url.'wiki\=edit'.
                     $uidurl):($cur)).';active='.(($wiki=='edit')?('1'):('0'));
        }

        if (file_exists (config_get ('site-root').$dir.'history.php')) {
          $items[] = 'title=История;hint=История изменения документа;url='.
            (($wiki!='history')?($url.'wiki\=history'.$uidurl):($cur)).
            ';active='.(($wiki=='history')?('1'):( (($oldid!='')?('shaded'):
                                                    ('0')) ));
        }
      }
    
      $static_rules = config_get ('static-privacy-rules');

      if (user_access_root () ||
          $static_rules[strtolower (user_login ())][$dir.'admin.php']) {
        if (file_exists (config_get ('site-root').$dir.'admin.php')) {
          $items[] = 'title=Администрирование;hint=Администрирование '.
            'раздела;url='.(($wiki!='admin')?($url.'wiki\=admin'.$uidurl):
                            ($cur)).';active='.(($wiki=='admin')?('1'):('0'));
        }
      }

      return stencil_wiki_page ($src, $items);
    }

    function wiki_admin_page () { global $wiki; return $wiki=='admin'; }
  }
?>
