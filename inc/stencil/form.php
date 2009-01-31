<?php
  function stencil_formo ($settings='') {
    global $dd_form_stuff_included, $CORE;
    $CORE->AddScriptFile ('dd_form.js');
    $s=unserialize_params ($settings);
    return ('<div class="form"'.(($s['smb'])?(' style="margin-bottom: 0;"'):('')).'><div id="title" class="title">'.$s['title'].'</div><div class="content" id="content">');
  }
  function stencil_formc () {
    return '</div></div>';
  }

  function stencil_settings_formo ($url='.', $method='POST',$additional='') { return ('<form action="'.$url.'" method="'.$method.'"'.((trim ($additional)!='')?(' '.$additional):('')).'>'); }
  function stencil_settings_form_buttons ($back='.', $title='Сохранить') {
    return ('      <div class="formPast">'.
           (($back!='')?('<button class="submitBtn" type="button" onclick="nav (\''.$back.'\');">Назад</button>
                          <button class="submitBtn" type="submit">'.htmlspecialchars ($title).'</button>'):
                        ('<button class="block" type="submit">'.htmlspecialchars ($title).'</button>')
          ).
        '</div>
    ');
  }
  function stencil_settings_formc ($back='.', $title='Сохранить') { return stencil_settings_form_buttons ($back, $title).'</form>'; }

  function formo ($settings='') { print stencil_formo ($settings); }
  function formc () { print stencil_formc (); }

  function settings_formo ($url='.',$method='POST',$additional='') { print stencil_settings_formo ($url,$method,$additional);}
  function settings_form_buttons ($back='.', $title='Сохранить') { print stencil_settings_form_buttons ($back, $title); }
  function settings_formc ($back='.', $title='Сохранить'){ print stencil_settings_formc ($back, $title);}
  
?>
