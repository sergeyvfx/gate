function iframeEditor_TypographyReplace (str) {
  var result=str;
  // Parsing nb-spaces to single space
  result=result.replace (/(\&nbsp;?)/g, ' ');
  // Deleting extra-spacing characters
  result=result.replace (/(\s+)/g, ' ');
  // Properly word-wrapping
  result=result.replace (/(\s-\s)/g, '&nbsp;&#8211; ');
  // Possible direct speech
  result=result.replace (/(<br>|\n\s*)\-\s+/gi, '$1&#8211; ');
  // Good-looking multi-dots :)
  result=result.replace (/(\.\.\.)/g, '&#8230;');
  // Parsing some mathematics and literature -specified expressions (like <= etc...)

/////
//  Because of some troubles with different fonts
//  result=result.replace (/(&lt;--&gt;)/g, '&#8596;').replace (/(&lt;--)/g, '&#8592;').replace (/(--&gt;)/g, '&#8594;');
//  result=result.replace (/(&lt;==&gt;)/, '&#8660;').replace (/(&lt;==)/g, '&#8656;').replace (/(==&gt;)/g, '&#8658;');
/////

  result=result.replace (/(&lt;=)/g, '&#8804;').replace (/(&gt;=)/g, '&#8805;');
  result=result.replace (/(\!=|&lt;&gt;)/g, '&#8800;');
  result=result.replace (/(--)/g, '&#8212;');
  // Parsing some other characters (tm, r...)
  result=result.replace (/(\(r\))/gi, '&#174;').replace (/(\(tm\))/gi, '&#8482;').replace (/\(c\)/gi, '&#169;');
  // Parsing commas
  result=result.replace (/(^)(\")/gi, '$1&#8222;').replace (/([>|\s])(\")/gi, '$1&#8222;');
  result=result.replace (/(\")($)/gi, '&#8221;$2').replace (/(\")([<|\,|\.\!\?])/gi, '&#8221;$2');

  result=result.replace (/(^)(\')/gi, '$1&#8218;').replace (/([>|\s])(\')/gi, '$1&#8218;');
  result=result.replace (/(\')($)/gi, '&#8217;;$2').replace (/(\')([<|\,|\.\!\?])/gi, '&#8217;$2');
  return result;
}

function iframeEditor_action_typography (name, userdata) {
  var src=iframeEditor_GetInnerHTML (name);
  src=iframeEditor_TypographyReplace (src);
  iframeEditor_SetInnerHTML (name, src);
}
