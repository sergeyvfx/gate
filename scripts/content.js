/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Helpers for AJAX-loading conent
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */

function CONTENT_SetOpening (wnd) {
  var document = window.document;

  if (wnd != null)
    document = wnd.document; else
    wnd = window;

  var dim = core_WindowDimensions (wnd);
  var scroll = core_GetPageScroll (wnd);
  var w, h;

  w = dim.X;
  h = dim.Y;

  if (!wnd.CONTENT_wait_widget) {
    wnd.CONTENT_wait_widget = document.createElement ('div');
    wnd.CONTENT_wait_widget.className = 'cdialog';
    wnd.CONTENT_wait_widget.innerHTML = '<table class="clear" style="margin: 0 4px; height: 26px; width: 100%;"><tr><td style="padding-right: 8px;"><img src="' +
        document_root + '/pics/wait.gif"></td><td>Загрузка...</td></tr></table>';
  }

  wnd.CONTENT_wait_widget.style.left = (w - 96) / 2 + 'px';
  wnd.CONTENT_wait_widget.style.top  = (h - 26) / 2 + 'px';

  if (!wnd.CONTENT_thread_counter)
    wnd.document.body.appendChild (wnd.CONTENT_wait_widget);

  wnd.CONTENT_thread_counter++;
}

function CONTENT_FreeOpening (wnd) {
  if (!wnd)
    wnd=window;

  wnd.CONTENT_thread_counter--;

  if (!wnd.CONTENT_thread_counter && wnd.CONTENT_wait_widget) {
    wnd.document.body.removeChild (wnd.CONTENT_wait_widget);
    wnd.CONTENT_wait_widget = null;
  }
}

function CONTENT_SetHTML (widget, html) {
  widget.innerHTML = html;
}

function CONTENT_RequestCallback (http_request, widget, window) {
  if (http_request.readyState == 4) {
    CONTENT_FreeOpening (window);
    CONTENT_SetHTML (widget, http_request.responseText);
    if (window)
      window.onresize ();
    http_request = null;
  }
}

function CONTENT_open (widget, url, window) {
  CONTENT_SetOpening (window);
  ipc_send_request (url, 0, function (http_request) { CONTENT_RequestCallback (http_request, widget, window);  });
}
