/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Core functions
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */


function dn () {}
function nav (url) { if (url == '') url = '/'; document.location = url; }
function cfrm (s) { return window.confirm (s); }
function cnav (c,url) { if (cfrm (c)) nav (url); }

function isalphanum (s) {
  if (s.length == 0) {
    return false;
  }

  return s.replace (/[A-z0-9_]+/gi, '').length == 0;
}

function qtrim(s)  { return s.replace(/^(\s*)/,"$`").replace(/(\s*)$/,"$'"); }
function qhtrim(s) { return s.replace(/^(\s*)/,"$`").replace(/(\s*)$/,"$'").replace (/(\<br\>)/gi, ''); }

function isnumber (s) {
  if (s.length == 0) {
    return false;
  }

  return s.replace (/^[0-9]+$/g, '').length == 0;
}

function isSignedNumber (s) {
  if (s.length == 0) {
    return false;
  }

  return s.replace (/^([\+\-])?[0-9]+$/g, '').length==0;
}

function elementByIdInTree (node, id) {
  if (node.id == id) {
    return node;
  }

  for (var i = 0; i < node.childNodes.length; i++) {
    var tid = elementByIdInTree (node.childNodes.item (i), id);
    if (tid != 0) {
      return tid;
    }
  }

  return 0;
}

function getElementById (id) { return document.getElementById (id); }

function checkDir (s) {
  if (s.length == 0) {
    return false;
  }

  return s.replace (/(\/[A-Z0-9]+)+\/?/gi, '').length == 0;
}

function check_folder (s) {
  if (s.length == 0) {
    return false;
  }

  return s.replace (/[A-z0-9_\-]+/gi, '').length==0;
}

function check_email (str) {
  if (str.length == 0) {
    return false;
  }

  result = str;
  result = result.replace (/^([A-Za-z0-9_\.]+)@(([A-Za-z0-9_]+\.?)+)$/g, '');

  return result.length == 0;
}

function show_msg (id, type, text) {
  var node = getElementById (id);

  if (type == 'ok') {
    node.className = 'msg_ok';
  } else if (type == 'err') {
    node.className = 'msg_err';
  }

  node.style.display = 'block';
  node.innerHTML = text;
}

function set_display (id, val) {getElementById (id).style.display=val;}
function hide (id) { set_display (id, 'none'); }
function sb (id)   { set_display (id, 'block'); }
function si (id)   { set_display (id, 'inline'); }

////////////
// Ajax stuff
function ipc_send_request (addr, post, callback) {
  if (engine == 'GECKO' || engine == 'OPERA') {
    var http_request = new XMLHttpRequest ();
  } else if (engine=='DONKEY') {
    var http_request = new ActiveXObject('Microsoft.XMLHTTP');
  } else {
    alert ('Ваш браузер использует неизвестный движок. Выполнение операции невозможно.');
    return;
  }

  if (post == false)
    http_request.open ('GET', document_root + addr, true); else
    http_request.open ('POST', document_root + addr, true);

  http_request.setRequestHeader ('Content-Type', 'application/x-www-form-urlencoded');
  http_request.onreadystatechange = function() { callback (http_request); };
  http_request.send (post);
}

function Deformat (text) {
  text = text.replace(/<(P|A|BR|IMG)([^>]*)>/gi, "<~$1$2>");
  text = text.replace(/<\/(P|A|BR)>/gi, "<~/$1>");
  text = text.replace(/<([^~>][^>]*)>/gi, "");
  text = text.replace(/<\/([^~>][^>]*)>/gi, "");
  text = text.replace(/<~([^>]+)>/gi, "<$1>");
  text = text.replace(/<~\/([^>]+)>/gi, "<$1>");

  return text;
}

////////
// Core built-in
function core_GetPageScroll (wnd) {
  var X, Y;

  if (wnd == null) {
    wnd = window;
  }

  if (typeof wnd.pageXOffset == 'number') {
    X = wnd.pageXOffset;
    Y = wnd.pageYOffset;
  } else {
    if ((wnd.document.compatMode) &&
        (wnd.document.compatMode == 'CSS1Compat')) {
      X = wnd.document.documentElement.scrollLeft;
      Y = wnd.document.documentElement.scrollTop;
    } else {
      X = wnd.document.body.scrollLeft;
      Y = wnd.document.body.scrollTop;
    }
  }

  return {scrollX:X, scrollY:Y};
}

function core_ElementPosition (self) {
  var node = self;
  var res = {x:0, y:0};

  while (node.tagName.toLowerCase () != 'body') {
    var pos = node.style.position.toLowerCase ();
    res.x += node.offsetLeft;
    res.y += node.offsetTop;
    node=node.offsetParent;
  }

  return res;
}

function core_ElementRectangle (element)     { var pos = core_ElementPosition (element); return {x:pos.x, y:pos.y, w:element.clientWidth, h:element.clientHeight}; }
function core_PointInRectangle (x,y,rec)     { return (x >= rec.x && x <= rec.x + rec.w && y >= rec.y && y <= rec.y+rec.h); }
function core_PointInElement (x, y, element) { var rec = core_ElementRectangle (element); return core_PointInRectangle (x,y,rec); }

var core_MouseCoords = {x:0, y:0};
function core_StoreMousePos (event) { core_MouseCoords = {x:event.clientX, y:event.clientY}; }
function core_GetMouseCoords ()     { return core_MouseCoords; }

function math_Sign (a) { if (a > 0) return 1; if (a < 0) return -1; return 0; }
function math_Dest (x1,y1,x2,y2) { return Math.sqrt ((x1 - x2) * (x1 - x2) + (y1 - y2) * (y1 - y2)); }

function atoi (str) {
  var i, n, res, ch;
  s=new String (str);
  n=s.length;
  res='';

  for (i = 0; i < n; i++) {
    c=s.charAt(i);
    if (c == '0') res = res + '0'; else if (c == '1') res = res + '1'; else if (c == '2') res = res + '2'; else
    if (c == '3') res = res + '3'; else if (c == '4') res = res + '4'; else if (c == '5') res = res + '5'; else
    if (c == '6') res = res + '6'; else if (c == '7') res = res + '7'; else if (c == '8') res = res + '8'; else
    if (c == '9') res = res + '9'; else break;
  }

  return res;
}

//////////
function select_title_by_value (node, val) {
  var item;

  for (var i = 0; i < node.childNodes.length; i++) {
    item = node.childNodes.item (i);
    if (item.tagName && item.tagName.toLowerCase () == 'option' && item.value == val) {
      return item.innerHTML;
    }
  }

  return '';
}

function remove_node (node) { if (node.parentNode) node.parentNode.removeChild (node); }

function isEditEmpty (id) { var node = getElementById (id); if (node == null) return true; return qtrim (node.value) == ''; }

function setForceURL (val) { force_url=val; }
function processForceURL () {
  if (force_url != '') {
    nav (force_url);
    return true;
  }
  return false;
}

function refreshPage (url) {
  if (!processForceURL ())
    nav (url);
}

function core_WindowDimensions (w) {
  var x,y;

  if (!w) w = window;

  if (w.innerHeight) {
    x = w.innerWidth;
    y = w.innerHeight;
  } else if (w.document.documentElement && w.document.documentElement.clientHeight) {
    x = w.document.documentElement.clientWidth;
    y = w.document.documentElement.clientHeight;
  } else if (document.body) {
    x = w.document.body.clientWidth;
    y = w.document.body.clientHeight;
  }

  return {X:x,Y:y};
}

function getKeyCode(event) {
  return window.event ? window.event.keyCode : (event.keyCode ? event.keyCode : (event.which ? event.which : null));
}

function escapeURLVal(val) {
  //return val.replace (/\&/g, '&quot;');
  return encodeURI (val) . replace (/\+/, '%2B') . replace (/\&/, '%26');
}

function htmlspecialchars(s) {
  return s.replace (/\&/g, '&amp;').replace (/\</g, '&lt;').replace (/\>/g, '&gt;');
}

function htmlspecialchars_decode(s) {
  return s.replace (/\&amp;/g, '&').replace (/\&lt;/g, '<').replace (/\&gt;/g, '>');
}
