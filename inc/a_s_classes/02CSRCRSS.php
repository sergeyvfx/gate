<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * RSS feeder class
   *
   * Copyright (c) `2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  global $IFACE;

  if ($IFACE != "SPAWNING NEW IFACE" || $_GET['IFACE'] != '') {
    print ('HACKERS?');
    die;
  }

  if ($_CSCRSS_included_ != '#CSCRSS_Included#') {
    $_CSCRSS_included_ = '#CSCRSS_Included#';

    class CSCRSS extends CSCVirtual {
      var $sName;
      var $filename;

      function InitInstance ($id = -1, $virtual = false) {
        $this->id = $id;

        $this->_virtual = $virtual;

        $q = db_select ('service', array ('*'),"`id` = $id");

        if (db_affected () <= 0) {
          $this->id=0;
        } else {
          $r = db_row ($q);
          $this->UnserializeSettings ($r['settings']);
        }

        if (!$virtual) {
          content_url_var_push_global ('action');
          content_url_var_push_global ('id');
          editor_add_function ('Управление сервисом', 'Editor_RSSManage');
          editor_add_function ('Разделы',             'Editor_ContentManage');
        }

        if ($this->id>0) { // Id>0 so the service has been created
          $url = content_url_get_full ();

          if (preg_match ('/^'.prepare_pattern (config_get ('document-root')).
            '[(\/)|(\/index.php)]?(\?(.*))?$/si', $url)) {
            global $CORE;
            $CORE->PAGE->SetRSS ($this->settings['title'], config_get ('http-document-root').'/rss.php');
          }
        }
      }

      function CSCRSS () {
        $this->SetServiceName ('RSS Feed');
        $this->SetClassName ('CSCRSS');
        $this->filename = config_get ('site-root').
          config_get ('document-root').'/rss.php';
      }

      function Create () {
        manage_settings_create ('Количество записей в RSS для одного раздела',
          'Сервисы', 'rss_items_per_content', 'CSCNumber');
        opt_set ('rss_items_per_content', 7);
        manage_setting_use ('rss_items_per_content');
      }

      function ReceiveSettings ($formnane='') {
        $this->settings['title']       = posted_html_string ($_POST[$formname.'_RSSTitle']);
        $this->settings['description'] = posted_html_string ($_POST[$formname.'_RSSDescription']);
        $this->settings['url']         = posted_html_string ($_POST[$formname.'_RSSURL']);
        $this->settings['timestamp']=time ();
        $this->WriteRSSFile ();
      }

      function PerformDeletion () {
        manage_setting_unuse ('rss_items_per_content');
        manage_settings_delete_by_ident ('rss_items_per_content');
        unlink ($this->filename);
      }

      function CanCreate () {
        if (db_count ('service', '`sclass`="CSCRSS"') == 0) {
          return true;
        }

        add_info ('Может существовать лишь один RSS серфис.');
        return false;
      }

      function DrawSettingsForm ($formnane = '') {
        print ('Заголовок:<input type="text" class="txt block" name="'.
          $formname.'_RSSTitle"><div id="hr"></div>'."\n");

        print ('Описание:<input type="text" class="txt block" name="'.
          $formname.'_RSSDescription"><div id="hr"></div>'."\n");

        print ('URL:<input type="text" class="txt block" name="'.
          $formname.'_RSSURL">'."\n");
      }

      function UpdateContentsLookup ($list) {
        $this->settings['contents'] = array ();

        $n = count ($list);
        for ($i=0; $i<$n; $i++) {
          if ($_POST['content_'.$list[$i]['id']]) {
            $this->settings['contents'][$list[$i]['id']] = 1;
          }
        }

        $this->UpdateSettings ();
      }

      function WriteRSSFile () {
        $f = fopen ($this->filename, 'w');
        $this->WriteRSSData ($f);
        fclose ($f);
        chmod ($this->filename, 0664);
      }

      function WriteRSSData ($f) {
        fwrite ($f, '<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Rss feeder
   *
   * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
   *
   * This program can be distributed under the terms of the GNU GPL.
   * See the file COPYING.
   */

  include \'globals.php\';
  include $DOCUMENT_ROOT.\'/inc/include.php\';

  db_connect (config_get (\'check-database\'));
  content_initialize ();
  wiki_initialize ();
  manage_initialize ();
  security_initialize ();
  ipc_initialize ();
  service_initialize ();
  editor_initialize ();

  $c = service_by_classname (\'CSCRSS\');

  if (count ($c) <= 0) {
    die;
  }

  $c = $c[0];
  $s = $c->GetService ();

  header (\'content-type: application/xhtml+xml\');
  print (\'<?xml version="1.0" encoding="utf-8"?>\');
?>

<rss version="2.0">
  <channel>
    <link><?=$s->GetURL ();?></link>
    <language>ru</language>
    <title><?=$s->GetTitle ();?></title>
    <description><?=$s->GetDescription ();?></description>
    <pubDate><?=FullLocalTime (time ());?></pubDate>
  </channel>
<?=$s->GetRSSData (); ?>
</rss>
');
      }

      function GetRSSData () {
        $res = '';
        $percontent = opt_get ('rss_items_per_content');

        $prefix = config_get ('http-document-root');

        if (is_array ($this->settings['contents']))
          foreach ($this->settings['contents'] as $cid=>$dummy) {
            $c = wiki_spawn_content ($cid);

            if ($c->GetID () <= 0) {
              unset ($this->settings['contents'][$cid]);
              continue;
            }

            $arr = $c->GetRSSData ($percontent);
            $n = min (count ($arr), $percontent);

            for ($i = 0; $i < $n; $i++) {
              $linkPrefix = $prefix.$c->GetFullHTTPPath ();
              $row = $arr[$i];

              $row['timestamp']= $timestamp;
              $res .= "  <item>\n";
              $res .= '    <title>'.htmlspecialchars ($row['title']).'</title>'."\n";

              if ($row['link'] != '') {
                $res.='    <link>'.$linkPrefix.'/'.$row['link'].'</link>'."\n";
              }

              $res.='    <description>'.htmlspecialchars ($row['description']).'</description>'."\n";

              if ($row['comments'] != '') {
                $res.='    <comments>'.$row['comments'].'</comments>'."\n";
              }

              $res .= '    <pubDate>'.FullLocalTime ($row['pubdate']).'</pubDate>'."\n";

              if ($row['dccreator'] != '') {
                $res .= '    <dc:creator>'.htmlspecialchars ($row['dccreator']).'</dc:creator>'."\n";
              }

              $res .= "  </item>\n";
            }
          }
        return setvars ($res);
      }

      function Editor_RSSManage () {
        global $act;

        if ($act == 'save') {
          $oldTitle = $this->settings['title'];
          $oldURL = $this->settings['url'];
          $this->settings['title'] = stripslashes ($_POST['title']);
          $this->settings['description'] = stripslashes ($_POST['description']);
          $this->settings['url'] = stripslashes ($_POST['url']);
          $this->UpdateSettings ();
          $this->WriteRSSFile ();
        }

        $full = content_url_get_full ();
        formo ('title=Управление сервисом;');
        println ('<form action="'.$full.'&act=save" method="POST">');

        println ('Заголовок: <input type="text" class="txt block" value="'.
          htmlspecialchars ($this->settings['title']).
          '" name="title"><div id="hr"></div>');

        println ('Описание: <input type="text" class="txt block" value="'.
          htmlspecialchars ($this->settings['description']).
          '" name="description"><div id="hr"></div>');

        println ('URL: <input type="text" class="txt block" value="'.
          htmlspecialchars ($this->settings['url']).'" name="url">');

        settings_form_buttons ();
        println ('</form>');
        formc;
      }

      function GetContentsByPid ($pid, $list) {
        $arr = array ();

        for ($i = 0; $i < count ($list); $i++) {
          if ($list[$i]['pid'] == $pid) {
            $arr[] = $list[$i];
          }
        }

        return $arr;
      }

      function PrintContents ($pid, $list, $pIndexes = array (), $path = '', $afterInfo = false) {
        global $cclasses;

        $arr = $this->GetContentsByPid ($pid, $list);
        $n = count ($arr);
        $cntPrefix = '';

        for ($i = 0; $i < count ($pIndexes); $i++) {
          $cntPrefix.=$pIndexes[$i].'.';
        }

        $depth = count ($pIndexes);

        for ($i = 0; $i < $n; $i++) {
          $r = $arr[$i];
          $cntString = $cntPrefix.($i+1);
          $p = $path.'/'.$r['path'];
          $childs = $this->GetContentsByPid ($r['id'], $list);
          $nChilds = count ($childs);
          $checked = false;

          if ($this->settings['contents'][$r['id']]) {
            $checked=true;
          }

          println (
  '<div style="padding-left: '.($depth*24).'px;"><table class="list'.(($nChilds==0 && $i==$n-1 && !$afterInfo)?(' smb'):('')).'">'.
  '<tr '.(($nChilds==0 && $i==$n-1 && !$afterInfo)?('class="last"'):('')).'>'.
  '<td class="n"><input type="checkbox" name="content_'.$r['id'].'" value="1" '.(($checked)?(' checked'):('')).'></td>'.
  '<td class="n">'.$cntString.'.</td>'.
  '<td style="padding: 0 16">'.$r['name'].'</td>'.
  '</tr></table></div>');

          $printed = true;
          $s_pIndexes = $pIndexes;
          $pIndexes[] = $i+1;
          $this->PrintContents ($r['id'], $list, $pIndexes, $p, $i!=$n-1);
          $pIndexes = $S_pIndexes;
        }
      }

      function Editor_ContentManage () {
        global $act;
        $list = wiki_content_get_list ();

        if ($act == 'save') {
          $this->UpdateContentsLookup ($list);
        }

        formo ('title=Следить за разделами;');
        println ('<form action="'.content_url_get_full ().
          '&act=save" method="post">');
        $this->PrintContents (1, $list);
        settings_form_buttons ();
        println ('</form>');
        formc ();
      }

      function GetTitle       () { return $this->settings['title']; }
      function GetDescription () { return $this->settings['description']; }
      function GetURL         () { return $this->settings['url']; }
    }

    content_Register_SCClass ('CSCRSS', 'RSS Feed');
  }
?>
