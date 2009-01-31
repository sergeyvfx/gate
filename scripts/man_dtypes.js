function man_dtypes_class_changed (sender) {
  var item=getElementById ('man_dtypes_forms');
  var dcName=getElementById ('dcName').value;
  for (i=0; i<item.childNodes.length; i++) {
    var node=item.childNodes.item (i);
    if (node.tagName)
      if (node.tagName.toLowerCase ()=='div') node.style.display='none';
  }
  elementByIdInTree (item, 'dts_'+dcName).style.display='block';
}