<?php if ($_CVCMenu_!='#CVCMenu_Included#') {$_CVCContent_='#CVCMenu_Included#';
  class CVCMenu extends CVCVirtual {
    var $metas;
    var $scripts;
    var $CSStyles;
    var $items, $active_item_tag;
    var $selectedBy;
    function CVCMenu () { $this->SetClassName ('CVCMenu'); }
    function Init ($name='', $settings='') {
      $this->SetDefaultSettings ();
      $this->contents=array ();

      $params=unserialize_params ($settings);
      $this->SetSettings (combine_arrays ($this->GetSettings (), $params));
    }
    function SetDefaultSettings() {$this->SetClassName ('CVCMenu');}
    function AppendItem ($title, $href, $tag, $img='') {
      $this->items[]=array ('title'=>$title, 'href'=>$href, 'tag'=>$tag, 'img'=>$img);
      return count ($this->items);
    }
    function SetActive ($tag) { $this->active_item_tag=$tag; $this->selectedBy='tag'; }
    function SetActiveByIndex ($index) { $this->active_item_index=$index; $this->selectedBy='index'; }
    function InnerHTML () {
      if (count ($this->items)==0) return '';
      if ($this->selectedBy=='index') $this->active_item_tag=$this->items[$this->active_item_index]['tag'];
      return $this->FromTemplate ($this->GetSetting ('type').'_menu',
        array ('settings'=>$this->GetSettings (), 'items'=>$this->items, 'active'=>$this->active_item_tag));
    }
    function Free () {$this->contents=array ();}
  }

  content_Register_VCClass ('CVCMenu');
}
?>
