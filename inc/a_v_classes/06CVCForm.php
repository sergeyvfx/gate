<?php
  /**
   * Gate - Wiki engine and web-interface for WebTester Server
   *
   * Form with fields
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

  if ($_CVCForm_ != '#CVCForm_Included#') {
    $_CVCForm_ = '#CVCForm_Included#';

     class CVCForm extends CVCVirtual {
      var $fields = array ();

      function CVCForm () { $this->SetClassName ('CVCForm'); }

      function Init ($name = '', $settings = '') {
        $params = unserialize_params ($settings);

        $this->settings['name'] = $name;
        $this->SetSettings (combine_arrays ($this->GetSettings (),
                                            $params));
        if ($this->settings['caption'] == '') {
          $this->settings['caption']='Сохранить';
        } else {
          $this->settings['caption'] =
            htmlspecialchars ($this->settings['caption']);
        }
      }

      function AppendField ($title, $name, $type, $value = '',
                            $settings = array ()) {
        $this->fields[] = array ('title' => $title, 'name' => $name,
                                 'type' => $type, 'value' => $value,
                                 'settings' => $settings);
      }

      function AppendCustomField ($src) {
        $this->AppendField ('', '', 'Custom', $src);
      }

      function AppendTextField ($title, $name, $value,
                                $important = false, $settings = array ()) {
        $settings['important'] = $important;
        $this->AppendField ($title, $name, 'Text', $value, $settings);
      }

      function AppendPasswordField ($title, $name, $value,
                                    $important = false, $settings = array ()) {
          $settings['important'] = $important;
          $this->AppendField ($title, $name, 'Password', $value, $settings);
      }

      function AppendEMailField ($title, $name, $value,
                                 $important = false, $settings = array ()) {
        $settings['important'] = $important;
        $this->AppendField ($title, $name, 'Email', $value, $settings);
      }

      function AppendCheckBoxField ($title, $name, $value, $important = false,
                                   $settings = array ()) {
        $settings['important'] = $important;
        $this->AppendField ($title, $name, 'CheckBox', $value, $settings);
      }

      function AppendComboBoxField ($title, $name, $value, $items = array (),
                                    $important = false, $settings = array ()) {
        $settings['important'] = $important;
        $settings['items'] = $items;
        $this->AppendField ($title, $name, 'ComboBox', $value, $settings);
      }

      function AppendLabelField ($title, $name, $value,
                                 $important = false, $settings = array ()) {
        $settings['important'] = $important;
        $this->AppendField ($title, $name, 'Label', $value, $settings);
      }

      function TextFieldHTML ($f) {
        return '    <input type="text" class="txt block"'.
               (($f['name'] != '') ? (' name="'.$this->settings['name'].
                                      '_'.$f['name'].'" id="'.
                                      $this->settings['name'].'_'.
                                      $f['name'].'"') : ('')).
               ' value="'.htmlspecialchars ($f['value']).'"'.
                '>'."\n";
      }

      function PasswordFieldHTML ($f) {
        return '    <input type="password" class="txt block"'.
               (($f['name'] != '')?(' name="'.$this->settings['name'].
                                    '_'.$f['name'].'" id="'.
                                    $this->settings['name'].'_'
                                    .$f['name'].'"'):('')).
              ' value="'.htmlspecialchars ($f['value']).'"'.
              '>'."\n";
        }

      function EMailFieldHTML ($f) {
        return '    <input type="text" class="txt block"'.
               (($f['name'] != '')?(' name="'.$this->settings['name'].
                                    '_'.$f['name'].'" id="'.
                                    $this->settings['name'].
                                    '_'.$f['name'].'"'):('')).
              ' value="'.htmlspecialchars ($f['value']).'"'.
              '>'."\n";
        }

      function LabelFieldHTML ($f) {
        return htmlspecialchars ($f['value']);
      }

      function CheckBoxFieldHTML ($f) {
        return '    <input type="checkbox" '.
               (($f['name'] != '')?(' name="'.$this->settings['name'].
                                    '_'.$f['name'].'" id="'.
                                    $this->settings['name'].'_'.
                                    $f['name'].'"'):('')).
              ' value="1"'.(($f['value'])?(' checked'):('')).
              '>'."\n";
        }

      function ComboBoxFieldHTML ($f) {
        $res = '';
        $items = $f['settings']['items'];
        $res = swriteln ($res, '  <select class="block" name="'.
                         $this->settings['name'].'_'.$f['name'].'">');
        $n = count ($items);

        for ($i = 0; $i < $n; $i++) {
          $it = $items[$i];
          $val = $it['value'];

          if (!isset ($val)) {
            $val = $it['id'];
          }

          $res = swriteln ($res, '    <option'.
                                 ((isset ($val))?(' value="'.$val.'"'):('')).
                                 (($f['value']==$val)?(' selected'):('')).
                                  '>'.$it['title'].'</option>');
        }

        $res = swriteln ($res, '  </select>');

        return $res;
      }

      function CUSTOMFieldCheckScript ($f) {
        $field = $f['value']['check_value'];
        return 'qtrim (getElementById ("'.$this->settings['name'].
               '_'.$field.'").value)!=""';
      }

      function CustomFieldHTML ($f) {
        return tpl_src ($f['value']['src'],
                        array ('value' => $f['value']['value']));
      }

     function LabelFieldCheckScript ($f) {
        $field = $f['value']['check_value'];
        return 'qtrim (getElementById ("'.$this->settings['name'].
               '_'.$field.'").value)!=""';
      }

      function FieldHTML ($f) {
        if ($f['type']=='') {
          return;
        }

        $handler = $f['type'].'FieldHTML';

        return $this->$handler ($f);
      }

      function FieldsHTML () {
        $res = '';
        $res = swriteln ($res, '<table class="form">');
        $n = count ($this->fields);
        $engine = browser_engine ();

        for ($i = 0; $i < $n; $i++) {
          $f = $this->fields[$i];
          $dummy = '';
          $dummy = (($this->settings['titlewidth']!='')?
                        (' width="'.$this->settings['titlewidth'].'"'):
                        (' width="30%"'));

          if ($f['type'] != 'Custom') {
            $res = swriteln ($res, '  <tr'.(($i == $n - 1)?
                                            (' class="last"'):('')).
                             '><td'.$dummy.'>'.$f['title'].'</td><td>'
                             .$this->FieldHTML ($f).'</td></tr>');
          } else {
            $res = swriteln ($res, '</table>');
            $res = swriteln ($res, '<table class="form">');
            $res = swriteln ($res, '  <tr'.(($i == $n - 1)?
                                            (' class="last"'):('')).'>'.
                                    (($f['value']['title'] != '')?
                                        ('<td '.$dummy.' valign="top">'.
                                        $f['value']['title'].'</td>'):
                                        ('')).
                                    '<td>'.$this->FieldHTML ($f).
                                    '</td></tr>');
            $res = swriteln ($res, '</table>');
            $res = swriteln ($res, '<table class="form">');
          }
        }

        $res = swriteln ($res, '</table>');

        return $res;
      }

      function TextFieldCheckScript ($f) {
        return 'qtrim (getElementById ("'.$this->settings['name'].'_'.
               $f['name'].'").value)!=""';
      }

      function PasswordFieldCheckScript ($f) {
        return 'qtrim (getElementById ("'.$this->settings['name'].'_'.
               $f['name'].'").value)!=""';
      }

      function EMailFieldCheckScript ($f) {
        return 'check_email (getElementById ("'.$this->settings['name'].'_'.
               $f['name'].'").value)';
      }

      function ComboBoxFieldCheckScript ($f) { return 'true'; }

      function CheckScripts () {
        $res = '';
        $n = count ($this->fields);

        for ($i = 0; $i < $n; $i++) {
          $f = $this->fields[$i];

          if (!$f['settings']['important']) {
            continue;
          }

          $handler = $f['type'].'FieldCheckScript';
          $cond = $this->$handler ($f);
          $src = '    if (!('.$cond.')) { alert ("Не указано обязательное '.
                 'поле \"'.$f['title'].'\" или поле содержит ошику.");'.
                 ' return false; }';
          $res = swriteln ($res, $src);
        }

        return $res;
      }

      function InnerHTML () {
        $res = '';
        $res = swriteln ($res, '<script language="JavaScript" '.
                               'type="text/javascript">');

        $res = swriteln ($res, '  function form_'.$this->settings['name'].
                               '_check (frm) {');
        $res = swriteln ($res, $this->CheckScripts ());
        $res = swriteln ($res, $this->settings['onsubmit']);

        if ($this->settings['add_check_func']) {
          $res = swriteln ($res, '    if (!'.$this->settings['add_check_func'].
                                 ' ()) return false;');
        }

        $res = swriteln ($res, '    frm.submit ();');
        $res = swriteln ($res, '  }');
        $res = swriteln ($res, '</script>');
        $res = swriteln ($res, $this->FieldsHTML ());

        $res = swriteln ($res, '<div class="formPast">');
        if ($this->settings['backlink'] != '') {
          $res = swriteln($res, '<button type="button" onclick="nav (\''.
                                $this->settings['backlink'].
                                '\');" class="submitBtn">Назад</button>');
          $res = swriteln($res, '<button class="submitBtn" type="submit">'.
                                 $this->settings['caption'].'</button>');
        } else
          $res = swriteln($res, '<button class="submitBtn block" '.
                                'type="submit">'.$this->settings['caption'].
                                '</button>');
          $res = swriteln($res, '</div>');

          return $res;
      }

      function OuterHTML () {
        $res = '';
        $res = swriteln ($res, '<form'.
          (($this->settings['action']!='')?(' action="'.$this->settings['action'].'"'):('')).
          (($this->settings['method']!='')?(' method="'.$this->settings['method'].'"'):('')).
          (($this->settings['enctype']!='')?(' enctype="'.$this->settings['enctype'].'"'):('')).
          ' onsubmit="form_'.$this->settings['name'].'_check (this); return false;">');
        $res = swriteln ($res, $this->InnerHTML ());
        $res = swriteln ($res, '</form>');
        return $res;
      }
    }

    function FormPOSTValue ($variable, $formname = '') {
      return $_POST[$formname.'_'.$variable];
    }

     content_Register_VCClass ('CVCForm');
  }
?>
