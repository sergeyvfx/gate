/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Dialogs' stuff
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */

var opened_dialogs       = Array ();
var opened_dialogs_count = 0;
var total_dialogs        = 0;
var dialogs_locked       = 0;

function CloseDialogs () {
  dialogs_locked = 1;
  for (var i = 0; i < opened_dialogs_count; i++) {
    opened_dialogs[i].wnd.close ();
  }
}

function RegisterDialog (__wnd) {
  var gid = total_dialogs++;
  opened_dialogs[opened_dialogs_count++] = {wnd: __wnd, id: gid};
  return gid;
}

function UnregisterDialog (id) {
  var found = 0;

  if (dialogs_locked) {
    return;
  }

  for (var i = 0; i < opened_dialogs_count; i++) {
    if (opened_dialogs[i].id == id) {
      found = 1;
      break;
    }
  }

  if (found) {
    for (var j = i; j < opened_dialogs_count - 1; j++) {
      opened_dialogs[j] = opened_dialogs[j + 1];
    }
    opened_dialogs_count--;
  }
}

function DialogOnClose (id) { UnregisterDialog (id); }

function ShowDialog (w, h, html, onok, oncancel, active) {
  var t = (screen.height - h) / 2,
      l = (screen.width - w) / 2;

  var params = 'scrollbars=0,fullscreen=0,status=0,toolbar=0,width=' +
      w + 'px,height=' + h + 'px,resizable=0,top=' + t + 'px,left=' + l + 'px';
  var wnd=open ('', '', params);

  var id = RegisterDialog (wnd);
  wnd.onunload = function() { if (window.DialogOnClose) DialogOnClose (id); };

  var s_sheet = wnd.document.createElement("link");
  s_sheet.setAttribute ('rel','stylesheet');
  s_sheet.setAttribute ('type','text/css');
  s_sheet.setAttribute ('href',document_root + '/styles/content.css');
  var head = wnd.document.getElementsByTagName('HEAD');
  var body = wnd.document.body;

  if (!head.length)
    body.appendChild (s_sheet); else
    head[0].appendChild (s_sheet);

  body.id = 'content';
  body.className = 'dialog';

  var contentDiv = wnd.document.createElement ('div');
  contentDiv.style.padding = '4px';
  contentDiv.innerHTML = html;

  var div = wnd.document.createElement ('div');
  div.className = 'center';
  div.style.margin = '4px 0 0 0';

  var okBtn = wnd.document.createElement ('button');
  okBtn.onclick = function () { if (onok (wnd)) wnd.close (); }
  okBtn.className = 'submitBtn';
  okBtn.style.margin = '0 2px 0 0';
  okBtn.innerHTML = 'OK';

  var cancelBtn = wnd.document.createElement ('button');
  cancelBtn.onclick = function () { if (oncancel (wnd)) wnd.close (); }
  cancelBtn.className = 'submitBtn';
  cancelBtn.style.margin = '0 0 0 2px';
  cancelBtn.innerHTML = 'Отмена';

  div.appendChild (okBtn);
  div.appendChild (cancelBtn);

  body.appendChild (contentDiv);
  body.appendChild (div);

  var obj = wnd.document.getElementById (active);
  if (obj) {
    obj.focus ();
  }
}
