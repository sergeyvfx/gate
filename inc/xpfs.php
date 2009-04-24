<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * XP FileSystem
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

  if ($_xpfs_included_ != '#xpfs_Included#') {
    $_xpfs_included_ = '#xpfs_Included#'; 

    global $XPFS_DEFAULT_VOLUME;
    $XPFS_DEFAULT_VOLUME = 'xpfs';

    class XPFS {
      var $ATTR_FILE = 1;
      var $ATTR_DIR  = 2;
      var $_CACHE = array ();

      function XPFS () {
        $this->checkTables ();
      }

      function checkTables () {
        if (config_get ('check-database')) {
          if (!db_table_exists ('xpfs_volumes')) {
            db_create_table ('xpfs_volumes', array (
                               'name' => 'TEXT',
                                                    ));
          }
        }
      }

      function createVolume ($volume = '') {
        global $XPFS_DEFAULT_VOLUME;

        if ($volume == '') {
          $volume = $XPFS_DEFAULT_VOLUME;
        }

        if (db_count ('xpfs_volumes', '`name`="'.addslashes ($volume).'"') > 0) {
          return false;
        }

        $name = $volume;
        $volume = 'xpfs_volume_'.$volume;

        if (!db_table_exists ($volume)) {
          db_create_table ($volume, array (
                             'id'        => 'INT NOT NULL PRIMARY KEY AUTO_INCREMENT',
                             'pid'       => 'INT',
                             'name'      => 'TEXT',
                             'data'      => 'LONGBLOB',
                             'mtime'     => 'INT DEFAULT 0',
                             'access'    => 'INT DEFAULT 0',
                             'attr'      => 'INT DEFAULT 0'
                                           ));

          db_insert ('xpfs_volumes', array ('name'=>'"'.addslashes ($name).'"'));
          db_insert ($volume, array ('name'=>'"/"', 'pid'=>0));

          return true;
        }

        return false;
      }

      function lsVolumes () {
        $q = db_select ('xpfs_volumes', array ('*'), '', 'ORDER BY `name`');
        $r = array ();

        while ($row = db_row ($q)) {
          $r[] = $row['name'];
        }

        return $r;
      }

      function checkNodeAttr ($node, $attr, $follow_symlink = true) {
        return $node['attr'] & $attr;
      }

      function isFileNode ($node, $follow_symlink = true) {
        return $this->checkNodeAttr ($node, $this->ATTR_FILE, $follow_symlink);
      }

      function isDirNode ($node, $follow_symlink = true) {
        return $this->checkNodeAttr ($node, $this->ATTR_DIR, $follow_symlink);
      }

      function nodeDescrFromUnknownArr ($volume, $arr) {
        return array ('vol' => $volume, 'id' => $arr['id'],
                      'pid' => $arr['pid'], 'access' => $arr['access'],
                      'attr' => $arr['attr'], 'mtime' => $arr['mtime'],
                      'name'=>$arr['name']);
      }

      function parsePath ($path) {
        global $XPFS_DEFAULT_VOLUME;

        $volume = preg_replace ('/([A-Z]*)\:\:.*/i', '\1', $path);
        $dir = preg_replace ('/([A-Z]*)\:\:(.*)/i', '\2', $path);

        if ($volume[0] == '/' || $volume == '') {
          $volume = $XPFS_DEFAULT_VOLUME;
        }

        return array ('vol' => $volume, 'dir' => $dir);
      }

      function getNode ($path) {
        $p = $this->parsePath ($path);
        return $this->getVolumeNode ($p['vol'], $p['dir']);
      }

      function getVolumeNode ($volume, $dir) {
        $id = 1;
        $res = array ('id'=>-1);

        if (isset ($this->_CACHE['VolumeNode'][$volume][$dir])) {
          return $this->_CACHE['VolumeNode'][$volume][$dir];
        }

        if ($dir!='/') {
          $name = basename ($dir);
          $parent = $this->getVolumeNode ($volume, dirname ($dir));
          $pid = $parent['id'];

          if ($pid != -1) {
            $q = db_select ('xpfs_volume_'.$volume,
                            array ('id', 'name', 'pid', 'attr', 'access'),
                            '`pid`='.$pid.' AND `name`="'.addslashes ($name).'"');
          }
        } else {
          $q = db_select ('xpfs_volume_'.$volume,
                          array ('id', 'name', 'pid', 'attr', 'access'), '`id`='.$id);
        }

        if (db_affected () > 0) {
          $arr = db_row_array ($q);
          $res = $this->nodeDescrFromUnknownArr ($volume, $arr);
        }

        $this->_CACHE['VolumeNode'][$volume][$dir] = $res;

        $this->_CACHE['NodeInfo'][$res['id']] = $res;
        $this->_CACHE['NodeInfo'][$res['id']]['dir'] = $dir;

        return $res;
      }

      function createNode ($path, $name, $access = 0, $attr = 0, $content = "") {
        $p = $this->parsePath ($path);
        return $this->createVolumeNode ($p['vol'], $p['dir'], $name,
                                        $access, $attr, $content);
      }

      function createVolumeNode ($volume, $dir, $name, $access = 0,
                                 $attr = 0, $content = "") {

        $full = $dir.($dir[strlen ($dir)-1]=='/'?'':'/').$name;
        $self = $this->getVolumeNode ($volume, $full);

        if ($self['id'] >= 0) {
          return true;
        }

        $parent = $this->getVolumeNode ($volume, $dir);

        if ($parent['id'] == -1) {
          return false;
        }

        if (!isnumber ($access)) {
          $access = 0;
        }

        if (!isnumber ($attr)) {
          $attr = 0;
        }

        db_insert ('xpfs_volume_'.$volume, array ('pid' => $parent['id'],
                                                  'name' => db_string ($name),
                                                  'access' => $access,
                                                  'attr' => $attr,
                                                  'mtime' => time (),
                                                  'data' => db_string ($content)));

        unset ($this->_CACHE['VolumeNode'][$volume][$full]);

        return true;
      }

      function renameNode ($node, $new_name) {
        if (!$this->isNodeAvaliable ($node)) {
          return false;
        }

        if (db_count ('xpfs_volume_'.$node['vol'], '`name`="'.
                      addslashes ($new_name).'" AND `id`!='.$node['id']) > 0) {
          return false;
        }

        db_update ('xpfs_volume_'.$node['vol'],
                   array ('name' => '"'.addslashes ($new_name).'"'),
                   '`id`='.$node['id']);

        if (db_affected () > 0) {
          return true;
        }

        return false;
      }

      function renameItem ($path, $new_name) {
        $p = $this->parsePath ($path);
        return $this->renameVolumeItem ($p['vol'], $p['dir'], $new_name);
      }

      function renameVolumeItem ($volume, $dir, $new_name) {
        $node = $this->getVolumeNode ($volume, $dir);
        return $this->renameNode ($node, $new_name);
      }

      function moveNode ($node, $parent_node) {
        if (!$this->isNodeAvaliable ($node)) {
          return false;
        }

        if ($node['vol'] != $parent_node['vol']) {
          return false;
        }

        db_update ('xpfs_volume_'.$node['vol'],
                   array ('pid' => $parent_node['id']),
                   '`id`='.$node['id']);
      }

      function moveItem ($item, $parent) {
        $_item = $this->parsePath ($item);
        $_parent = $this->parsePath ($parent);
        if ($_item['vol'] != $_parent['vol']) {
          return false;
        }

        return $this->moveVolumeItem ($_item['vol'], $_item['dir'],
                                      $_parent['dir']);
      }

      function moveVolumeItem ($volume, $item, $parent) {
        $node = $this->getVolumeNode ($volume, $item);
        $parent_node = $this->getVolumeNode ($volume, $parent);
        return $this->moveNode ($node, $parent_node);
      }

      function lsDir ($path) {
        $p = $this->parsePath ($path);
        return $this->lsVolumeDir ($p['vol'], $p['dir']);
      }

      function lsVolumeDir ($volume, $dir) {
        $node = $this->getVolumeNode ($volume, $dir);
        return $this->lsNode ($node);
      }

      function lsNode ($node) {
        $r = array ();

        if (!$this->isNodeAvaliable ($node)) {
          return $r;
        }

        if ($node['id'] >= 0) {
          $q = db_select ('xpfs_volume_'.$node['vol'],
                          array ('*'), '`pid`='.$node['id']);
          while ($row = db_row ($q)) {
            $r[] = $this->nodeDescrFromUnknownArr ($node['vol'], $row);
          }
        }

        return $r;
      }

      function createFile ($path, $name, $access = 0, $content = "") {
        $p = $this->parsePath ($path);
        return $this->createVolumeFile ($p['vol'], $p['dir'], $name,
                                        $access, $content);
      }

      function createVolumeFile ($volume, $dir, $name,
                                 $access = 0, $content = "") {
        return $this->createVolumeNode ($volume, $dir, $name,
                                        $access, $this->ATTR_FILE, $content);
      }

      function createDirectory ($path, $name, $access = 0) {
        $p = $this->parsePath ($path);
        return $this->createVolumeDir ($p['vol'], $p['dir'], $name, $access);
      }

      function createVolumeDir ($volume, $dir, $name, $access = 0) {
        return $this->createVolumeNode ($volume, $dir, $name,
                                        $access, $this->ATTR_DIR);
      }

      function createDirWithParents ($path, $access=0) {
        if ($path == '/' || $path == '') {
          return true;
        }

        if ($this->createDirWithParents (dirname ($path), $access)) {
          return $this->createDirectory (dirname ($path),
                                         basename ($path), $access);
        }
      }

      function readFile ($path) {
        $p = $this->parsePath ($path);
        return $this->readVolumeFile ($p['vol'], $p['dir']);
      }

      function readVolumeFile ($volume, $dir) {
        $node = $this->getVolumeNode ($volume, $dir);
        return $this->readNodeContent ($node);
      }

      function readNodeContent ($node) {
        if ($node['id'] == -1) {
          return '';
        }

        if (!$this->isNodeAvaliable ($node)) {
          return '';
        }

        $q = db_select ('xpfs_volume_'.$node['vol'],
                        array ('data'), '`id`='.$node['id']);
        $arr = db_row ($q);

        return $arr['data'];
      }

      function writeBlock ($path, $content = "") {
        $p = $this->parsePath ($path);
        return $this->writeVolumeBlock ($p['vol'], $p['dir'], $content);
      }

      function writeVolumeBlock ($volume, $dir, $content = "") {
        $node = $this->getVolumeNode ($volume, $dir);
        return $this->writeNodeBlock ($node, $content);
      }

      function writeNodeBlock ($node, $content = "") {
        if (!$this->isNodeAvaliable ($node)) {
          return false;
        }

        if ($node['id'] < 0) {
          return false;
        }

        db_update ('xpfs_volume_'.$node['vol'],
                   array ('data' => '"'.addslashes ($content).'"',
                          'mtime' => time ()),
                   '`id`='.$node['id']);
      }

      function removeItem ($path) {
        $p = $this->parsePath ($path);
        return $this->removeVolumeItem ($p['vol'], $p['dir']);
      }

      function removeVolumeItem ($volume, $dir) {
        $node = $this->getVolumeNode ($volume, $dir);
        return $this->removeNode ($node);
      }

      function removeNode ($node, $force = false) {
        if (!$this->isNodeAvaliable ($node)) {
          return false;
        }

        if (!$force && db_count ('xpfs_volume_'.$node['vol'],
                                 '`pid`='.$node['id']) > 0) {
          return false;
        }

        db_delete ('xpfs_volume_'.$node['vol'], '`id`='.$node['id']);

        $dir = $this->_CACHE['NodeInfo'][$node['id']]['dir'];
        unset ($this->_CACHE['VolumeNode'][$node['vol']][$dir]);
        unset ($this->_CACHE['NodeAvaliable'][$node['id']]);
        unset ($this->_CACHE['NodeInfo'][$node['id']]);
      }

      function removeRec ($path) {
        $p = $this->parsePath ($path);
        return $this->removeVolumeRec ($p['vol'], $p['dir']);
      }

      function removeVolumeRec ($volume, $dir) {
        $node = $this->getVolumeNode ($volume, $dir);
        $this->removeNodeRec ($node);
      }

      function removeNodeRec ($node) {
        if (!$this->isNodeAvaliable ($node)) {
          return false;
        }

        $listing = $this->lsNode ($node);

        for ($i = 0, $n = count ($listing); $i < $n; ++$i) {
          if (!$this->removeNodeRec ($listing[$i])) {
            return false;
          }
        }

        $this->removeNode ($node, true);

        return true;
      }

      function getParentNode ($node) {
        if (isset ($this->_CACHE['NodeInfo'][$node['pid']])) {
          return $this->_CACHE['NodeInfo'];
        }

        $q = db_select ('xpfs_volume_'.$node['vol'],
                        array ('*'), '`id`='.$node['pid']);

        $arr = db_row ($q);
        $res = $this->nodeDescrFromUnknownArr ($node['vol'], $arr);

        $this->_CACHE['NodeInfo'][$node['pid']] = $res;

        return $res;
      }

      function isNodeAvaliable ($node) {
        $res = false;

        if (isset ($this->_CACHE['NodeAvaliable'][$node['id']])) {
          return $this->_CACHE['NodeAvaliable'][$node['id']];
        }

        if ($node['access'] <= user_access ()) {
          if ($node['pid'] <= 0) {
            $res = true;
          } else {
            $pnode = $this->getParentNode ($node);
            $res = $this->isNodeAvaliable ($pnode);
          }
        }

        $this->_CACHE['NodeAvaliable'][$node['id']] = $res;

        return $res;
      }

      function setAttr ($path, $attr) {
        $p = $this->parsePath ($path);
        return $this->setVolumeAttr ($p['vol'], $p['dir'], $attr);
      }

      function setVolumeAttr ($volume, $dir, $attr) {
        $node = $this->getVolumeNode ($volume, $dir);
        return $this->setNodeAttr ($node, $attr);
      }

      function setNodeAttr ($node, $attr) {
        if (!isnumber ($attr)) {
          $attr = 0;
        }

        return $this->setNodeField ($node, 'attr', $attr);
      }

      function setAccess ($path, $access) {
        $p = $this->parsePath ($path);
        return $this->setVolumeAccess ($p['vol'], $p['dir'], $access);
      }

      function setVolumeAccess ($volume, $dir, $access) {
        $node = $this->getVolumeNode ($volume, $dir);
        return $this->setNodeAccess ($node, $access);
      }

      function setNodeAccess ($node, $access) {
        if (!isnumber ($access)) {
          $access = 0;
        }

        if ($access < 0) {
          $access = 0;
        }

        if ($access > 7) {
          $access = 7;
        }

        return $this->setNodeField ($node, 'access', $access);
      }

      function setNodeField ($node, $field, $val) {
        if (!$this->isNodeAvaliable ($node)) {
          return false;
        }

        db_update ('xpfs_volume_'.$node['vol'], array ($field => $val),
                   '`id`='.$node['id']);
      }

      function updateNodeMTime ($node) {
        db_update ('xpfs_volume_'.$node['vol'],
                   array ('mtime' => time ()), '`id`='.$node['id']);
      }
    }
  }
?>
