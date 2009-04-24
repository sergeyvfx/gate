<script language="JavaScript" type="text/javascript">
/**
 * Gate - Wiki engine and web-interface for WebTester Server
 *
 * Main scripting file for IFrame editor
 *
 * Copyright (c) 2008-2009 Sergey I. Sharybin <g.ulairi@gmail.com>
 *
 * This program can be distributed under the terms of the GNU GPL.
 * See the file COPYING.
 */

  var iframeEditors = new Array ();
  var iframeEditor_selection = Array ();
  var iframeEditor_ActiveDialog = new Array ();

  function iframeEditor_get_editor (name) {
    var tmp = iframeEditors[name];

    if (tmp) {
      return tmp;
    }

    tmp = getElementById ('iframeEditor_' + name + '_editor');
    iframeEditors[name] = tmp;

    return tmp;
  }
  
  function iframeEditor_SetDialogVisibility (name, dialog, vis) {
    var id = 'iframeEditor_' + name + '_dialog_' + dialog;
    var dlg = getElementById (id);

    if (!dlg) {
      return;
    }

    if (vis) {
      sb (id);
    } else {
      hide (id);
    }
  }
  
  function iframeEditor_ShowDialog (name, dialog) {
    iframeEditor_SetDialogVisibility (name, dialog, true);
  }

  function iframeEditor_HideDialog (name, dialog) {
    iframeEditor_SetDialogVisibility (name, dialog, false);
  }
  
  function iframeEditor_SetActiveDialog (name, dialog) {
    if (iframeEditor_ActiveDialog[name]) {
      iframeEditor_HideDialog (name, iframeEditor_ActiveDialog[name]);
    }

    iframeEditor_ShowDialog (name, dialog);
    iframeEditor_ActiveDialog[name]=dialog;
  }

  function iframeEditor_OnSubmit (name) {
    var val = getElementById (name);
    val.value = iframeEditor_GetInnerHTML (name);
  }

  function iframeEditor_SetValue (name) {
    var val = getElementById (name).value;
    if (val != '') {
      iframeEditor_SetInnerHTML (name, val);
    }
  }

  function iframeEditor_GetElementAscensor (A, B) {
    var e = A;
    var C= "," + B.toUpperCase() + ",";

    while (e) {
      if (C.indexOf ("," + e.nodeName.toUpperCase () + ",") != -1) {
        return e;
      }
      e = e.parentNode;
    };

    return null;
  };

  function iframeEditor_LockSelection (name) {
    if (iframeEditor_SelectionLocked (name)) {
      return;
    }

    iframeEditor_selection[name] = {
      selection: iframeEditor_GetSelection (name),
      range:     iframeEditor_GetRange (name),
      type:      iframeEditor_GetSelectionType (name),
      text:      iframeEditor_GetSelectionString (name),
      locked:    true};
  }

  function iframeEditor_UnlockSelection (name) {
    if (!iframeEditor_selection[name]) {
      return;
    }

    iframeEditor_selection[name].locked = false;
  }

  function iframeEditor_SelectionLocked (name) {
    if (!iframeEditor_selection[name]) {
      return false;
    }

    return iframeEditor_selection[name].locked;
  }

  function iframeEditor_GetLockedSelection (name) {
    if (!iframeEditor_selection[name]) {
      return null;
    }

    return iframeEditor_selection[name].selection;
  }

  function iframeEditor_GetLockedSelectionString (name) {
    if (!iframeEditor_selection[name]) {
      return null;
    }

    return iframeEditor_selection[name].text;
  }

  function iframeEditor_GetLockedRange (name) {
    if (!iframeEditor_selection[name]) {
      return null;
    }

    return iframeEditor_selection[name].range;
  }

  function iframeEditor_GetLockedType (name) {
    if (!iframeEditor_selection[name]) {
      return null;
    }

    return iframeEditor_selection[name].type;
  }

  function iframeEditor_CreateElement (name, elem) {
    var document = iframeEditor_GetDocument (name);
    return document.createElement (elem);
  }

  function iframeEditor_DialogValue (name, dialog, val) {
    var id='iframeEditor_' + name + '_' + dialog + '_' + val;
    return getElementById (id).value;
  }

  function iframeEditor_SetDialogValue (name, dialog, val, v) {
    var id = 'iframeEditor_' + name + '_' + dialog + '_' + val;
    getElementById (id).value = v;
  }

  // Hyperlink built-in
  function iframeEditor_hrefCheck (proto, link) {
    if (link == '') {
      alert ('Пустой адрес ссылки.');
      return false;
    }

    if (proto == 'MAILTO' && !check_email (link)) {
      alert ('Неправильный адрес электронной почты.');
      return false;
    }

    return true;
  }

  function iframeEditor_href (proto, link) {
    if (link.charAt (0)=='/') return link;
    if (proto == 'MAILTO') return 'mailto:'+link;
    if (proto == 'HTTP')   return 'http://'+link;
    if (proto == 'HTTPS')  return 'https://'+link;
    if (proto == 'FTP')    return 'ftp://'+link;
  }

<?php
  include 'scripts_'.browser_engine ().'/main.js';
?>

</script>
