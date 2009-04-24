<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Receiver for data from server
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

  if ($_WT_datareceiver_included_ != '###WT_DataReceiver_Inclided###') {
    $_WT_datareceiver_included_ = '###WT_DataReceiver_Inclided###';

    function WT_CleanupIPCCacheStorage ($storage) {
      global $XPFS;

      $now = time ();
      $lifetime = config_get ('WT-Cache-Lifetime');

      $listing = $XPFS->lsDir ($storage);

      for ($i = 0, $n = count ($listing); $i < $n; ++$i) {
        $node = $listing[$i];

        if ($now-$node['mtime'] > $lifetime) {
          $XPFS->removeNode ($node);
        }
      }
    }

    function WT_ReceiveIPCData ($storage, $unique, $cmd, $args = array (),
                                $authRoot = true, $preCommands = array ()) {
      global $XPFS;

      $res = null;

      $node = $XPFS->getNode ($storage.'/'.$unique);

      if ($node['id'] >= 0) {
        /* Return data from cache */
        $XPFS->updateNodeMTime ($node);
        $res = $XPFS->readNodeContent ($node);
      } else {
        /* Receive data and store in cache */
        $wt = new WebTester ();

        if ($authRoot) {
          $wt->AuthRoot ();
        }

        $cmds = $preCommands;
        $cmds[] = array ('cmd' => $cmd, 'args' => $args);

        $err = false;
        for ($i = 0, $n = count ($cmds); $i < $n; ++$i) {
          $c = $cmds[$i];
          $b = $wt->SendCommand ($c['cmd'], $c['args']);
          $b = $wt->ParseBuf ($b, &$state);
          if ($state != '+OK') {
            $err = true; break;
          }
        }

        if (!$err) {
          $XPFS->createDirWithParents ($storage);
          $XPFS->createFile ($storage, $unique, 0, $b);

          $res = $b;
        }
      }

      WT_CleanupIPCCacheStorage ($storage);

      return $res;
    }
  }
?>
