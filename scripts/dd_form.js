/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Helpers for drop-down scripts
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */

function dd_form_expand (sender) {
  var node = sender;

  while (node.className != 'dd_form') {
    node = node.parentNode;
  }

  elementByIdInTree (node, 'content').style.display = 'block';
  elementByIdInTree (node, 'show').style.display    = 'none';
  elementByIdInTree (node, 'hide').style.display    = 'block';
}

function dd_form_hide (sender) {
  var node = sender;

  while (node.className != 'dd_form') {
    node = node.parentNode;
  }

  elementByIdInTree (node, 'content').style.display = 'none';
  elementByIdInTree (node, 'show').style.display    = 'block';
  elementByIdInTree (node, 'hide').style.display    = 'none';
}
