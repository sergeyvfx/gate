<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Implementation of text with WYSIWYG editor datatype
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

  if ($_CDCIFrame_ != '#CDCIFrame_included#') {
    $_CDCIFrame_ = '#CDCIFrame_included#';

    class CDCIFrame extends CDCVirtual {
      function CDCIFrame () { $this->SetClassName ('CDCIFrame'); }
      function DrawEditorForm  ($name, $formname = '', $init = true) {
        iframe_draw_editor ($formname.'_'.$name, $this->GetValue (), $init,
          'editor_form', $this->settings, $this->settings);
      }

      function BuildInitScript ($field, $formname = '') {
        return 'iframeEditor_Init ("'.$formname.'_'.$field.'");';
      }

      function DrawLimits ($class, $field, $s = array ()) {
        $c = new $class ();
        $c->Init ();
        $c->SetSettings ($s);
        $c->DrawContentSettingsForm ('', $field, false);
      }

      function DrawImageLimits ($field) {
        $this->DrawLimits ('CDCImage', $field, $this->settings['image']);
      }

      function DrawFileLimits  ($field) {
        $this->DrawLimits ('CDCFile', $field, $this->settings['file']);
      }

      function DrawContentSettingsForm ($title, $field) {
        if ($title!='') {
          println ("<b>$title:</b><br>");
        }

        $this->DrawImageLimits ($field.'_image');
        $this->DrawFileLimits ($field.'_file');
        return true;
      }

      function ReceiveLimitsSettings ($class, $title, $field, $sfield) {
        $c = new $class ();
        $c->Init ();
        $c->ReceiveContentSettings ($title, $field);
        $tmp=$c->GetSettings ();
        $this->settings['data'][$sfield] = $tmp['data'];
      }

      function ReceiveImageSettings ($title, $field) {
        $this->ReceiveLimitsSettings ('CDCImage', $title, $field.'_image', 'image');
      }

      function ReceiveFileSettings  ($title, $field) {
        $this->ReceiveLimitsSettings ('CDCFile',  $title, $field.'_file',   'file');
      }

      function ReceiveContentSettings ($title, $field) {
        $this->ReceiveImageSettings ($title, $field);
        $this->ReceiveFileSettings ($title, $field);
        return true;
      }

      function ReceiveValue ($field, $formname = '') {
        $old_val = $this->GetValue ();
        CDCVirtual::ReceiveValue ($field, $formname);
        $this->SetValue (iframe_accept_content ($formname.'_'.$field, $old_val));
      }

      function DestroyValue () {
        iframe_destroy_content ($this->GetValue ());
      }

    }
    content_Register_DCClass ('CDCIFrame', 'Визуально редактирыемый текст с форматированием');
  }
?>
