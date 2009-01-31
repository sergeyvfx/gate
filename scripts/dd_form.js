function dd_form_expand (sender) {
  var node=sender;
  while (node.className!='dd_form') node=node.parentNode;
  elementByIdInTree (node, 'content').style.display='block';
  elementByIdInTree (node, 'show').style.display='none';
  elementByIdInTree (node, 'hide').style.display='block';
}

function dd_form_hide (sender) {
  var node=sender;
  while (node.className!='dd_form') node=node.parentNode;
  elementByIdInTree (node, 'content').style.display='none';
  elementByIdInTree (node, 'show').style.display='block';
  elementByIdInTree (node, 'hide').style.display='none';
}