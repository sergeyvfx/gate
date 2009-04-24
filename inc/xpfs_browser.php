<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Browser of XP FileSystem
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

  if ($_xpfs_browser != '#XPFS-Browser-Included#') {
    $_xpfs_browser = '#XPFS-Browser-Included#';

    global $XPFS;

    class XPFSBrowser {
      var $volume, $path, $volumes, $xpfs;

      function XPFSBrowser () {
        global $XPFS;

        $this->volume = $_GET['volume'];
        $this->path   = $_GET['path'];

        if (isset ($XPFS)) {
          $this->XPFS = $XPFS;
        } else {
          $this->XPFS=new XPFS ();
        }

        $this->fillVolumes ();

        if ($this->path == '') {
          $this->path='/';
        }

        if ($this->volume == '') {
          $this->volume=$this->volumes[0];
        }
      }

      function fillVolumes () {
        $this->volumes = $this->XPFS->lsVolumes ();
      }

      function getFullPath ($file) {
        if ($file[0]=='/') {
          return $file;
        }

        return $this->path.($this->path!='/'?'/':'').$file;
      }

      function parseCommand ($command) {
        $res = array ();
        $command = trim ($command);
        $arr = explode (' ', $command);

        for ($i = 0, $n = count ($arr); $i < $n; ++$i) {
          $t = trim ($arr[$i]);
          if ($t != '') {
            $res[] = $t;
          }
        }

        return $res;
      }

      function interact ($command='') {
        if ($command == '') {
          $command = stripslashes ($_POST['xpfs_command']);
          $_POST['xpfs_command'] = '';
        }

        if ($command != '') {
          $args = $this->parseCommand ($command);
          switch ($args[0]) {
            case 'cd':
              $path=$this->path;
              if ($args[1][0] == '/') {
                $path = $args[1];
              } else {
                $path .= ($path!='/'?'/':'').$args[1];
              }

              $this->path = $path;
              break;

            case 'mkdir':
              $this->XPFS->createVolumeDir ($this->volume, $this->path,
                                            $args[1], $args[2]);
              break;

            case 'mkfile':
              $this->XPFS->createVolumeFile ($this->volume, $this->path,
                                             $args[1], $args[2]);
              break;

            case 'mkvol':
              $this->XPFS->createVolume ($args[1]);
              $this->fillVolumes ();
              break;

            case 'rename':
              $this->XPFS->renameVolumeItem ($this->volume,
                                             $this->getFullPath ($args[1]),
                                             $args[2]);
              break;

            case 'rm':
              $this->XPFS->removeVolumeItem ($this->volume,
                                             $this->getFullPath ($args[1]));
              break;

            case 'rmdir':
              $this->XPFS->removeVolumeRec ($this->volume,
                                            $this->getFullPath ($args[1]));
              break;

            case 'attr':
              $this->XPFS->setVolumeAttr ($this->volume,
                                          $this->getFullPath ($args[1]),
                                              $args[2]);
              break;

            case 'access':
              $this->XPFS->setVolumeAccess ($this->volume,
                                            $this->getFullPath ($args[1]),
                                                $args[2]);
              break;
          }
        }
      }

      function prepareURL ($path = '', $volume = '') {
        $url = content_url_get_full ();

        if ($path == '') {
          $path = $this->path;
        }

        if ($volume == '') {
          $volume = $this->volume;
        }

        return htmlspecialchars ($url).'&volume='.htmlspecialchars ($volume).
          '&path='.htmlspecialchars ($path);
      }

      function drawUpLink ($last) {
        if ($this->path == '/') {
          return;
        }

        $url = $this->prepareURL (dirname ($this->path));
        println ('<tr'.($last?'  class="last"':'').' onclick="nav(\''.
                 addslashes (htmlspecialchars ($url)).
                 '\');" title="Переход к родительскому каталогу">'.
                 '<td colspan="4"><img src="'.config_get ('document-root').
                 '/pics/arrup_blue.gif">&nbsp;&nbsp;<a href="'.
                 addslashes (htmlspecialchars ($url)).'">Вверх</a></td></tr>');
      }

      function drawNode ($node, $last) {
        $img = 'file';
        $time = '';
        if ($this->XPFS->isDirNode ($node)) {
          $img = 'dir';
          $url = $this->prepareURL ($this->path.
                                    ($this->path!='/'?'/':'').$node['name']);
        } else {
          $url = $this->prepareURL ().'&action=edit&file='.
            htmlspecialchars ($node['name']);
          $time = format_date_time ($node['mtime']);
        }

      println ('<tr'.($last?'  class="last"':'').'  onclick="nav(\''.
               addslashes (htmlspecialchars ($url)).'\');">'.
               '<td class="first"><img src="'.config_get ('document-root').
               '/pics/'.$img.'.gif">&nbsp;&nbsp;<a href="'.
               addslashes (htmlspecialchars ($url)).'">'.
               htmlspecialchars ($node['name']).'</a></td><td align="center">'.
               $time.'</td><td align="center">'.$node['access'].
               '</td><td align="center">'.$node['attr'].'</td></tr>'."\n");
      }

      function drawEditForm ($name) {
        $url = $this->prepareURL ();
        $data = $this->XPFS->readFile ($this->getFullPath ($name));

        formo ('title=Редактирование файла "'.
               prepare_arg (htmlspecialchars ($name)).'"');

        println ('<form action="'.htmlspecialchars ($url).
                 '&action=save&file='.htmlspecialchars ($name).
                 '" method="POST">'."\n");

        println ('  <center><textarea style="width: 95%; height: 200px;" '.
                 'name="xpfs_content">'.htmlspecialchars ($data).
                 '</textarea></center>'."\n");

        println ('  <div style="margin-top: 6px;"><center>'.
                 '<button type="submit" class="submitBtn"><b>Сохранить</b>'.
                 '</button>&nbsp;<button class="submitBtn" type="button" '.
                 'onclick="nav(\''.addslashes (htmlspecialchars ($url)).
                 '\');">Отменить</button></center></div>'."\n");

        println ('</form>'."\n");
        formc ();
      }

      function Draw () {
        global $CORE;

        if ($_GET['action'] != 'edit') {
          redirector_add_skipvar ('file');
        }

        $CORE->AddStyle ('xpfs-browser');

        $volumes = '';
        for ($i = 0, $n = count ($this->volumes); $i < $n; ++$i) {
          $volumes .= prepare_arg ('<option value="'.
                                   htmlspecialchars ($this->volumes[$i]).'"'.
                                   ($this->volume==$this->volumes[$i]?' selected':'').
                                   '>'.htmlspecialchars ($this->volumes[$i]).'</option>');
        }

        formo ('title=<table width\="100%"><tr><td>Браузер файловой системы'.
               '</td><td width\="150px">Том:'.
               '<select style\="margin-left: 6px\; width: 100px" '.
               'onchange\="nav (\'./?volume\=\'+this.value)">'.$volumes.
               '</select></td></tr></table>');

        if ($this->volume == '') {
          println ('<i>Нет томов для просмотра</i>');
        } else {
          if ($_GET['action'] == 'edit') {
            $this->drawEditForm ($_GET['file']);
          } else if ($_GET['action'] == 'save') {
            $this->XPFS->writeVolumeBlock ($this->volume,
                                           $this->getFullPath ($_GET['file']),
                                           stripslashes ($_POST['xpfs_content']));
          }

          println ('<table class="list xpfs_browser" width="100%">'.
                   '<tr class="h"><th class="first">Путь: '.
                   htmlspecialchars ($this->path).'</th><th width="90">'.
                   'Время</th><th width="80">Доступ</th>'.
                   '<th width="80" class="last">Атрибут</th></tr>'."\n");

          $listing = $this->XPFS->lsVolumeDir ($this->volume, $this->path);

          $this->drawUpLink (count ($listing) == 0);

          for ($i = 0, $n = count ($listing); $i < $n; ++$i) {
            $this->drawNode ($listing[$i], $i == $n - 1);
          }

          println ('</table>');
          println ('<form action="'.$this->prepareURL ().'" method="post">');
          println ('<div class="xpfs_cmdline"><table class="clear" '.
                   'width="100%"><tr><td width="120px"><b>Командная строка:</b>'.
                   '</td><td><input type="text" class="txt block" '.
                   'name="xpfs_command"></td><td width="80px">'.
                   '<button style="margin-left: 8px; width: 24px" '.
                   'type="submit">»</button></td></tr></table></div>');
          println ('</form>');
        }

        formc ();
      }
    }
  }
?>
