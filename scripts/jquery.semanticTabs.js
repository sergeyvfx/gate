/*
 * jQuery semanticTabs
 * @copyright Aleksej Romanovskij http://agat.in/jquery-semantictabs/
 * @version 0.1.1
 */

(function($){
$.fn['semanticTabs'] = function(options) {
	var defaults = {
		'tabSelector': '> dt',
		'bodySelector': '> dd',
		'tabsCSSClass': 'b-tabs',
		'tabsNavSuffix': '-nav',
		'tabsNavActiveSuffix': '-cur'
	};
	
	options = $.extend(defaults, options);
    
	return this.each(function() {
		var obj = $(this),
			sObjCSSClass = obj.attr('class').split(' ')[0],
			sObjNavCSSClass,
			sObjNavCSSClassActive;
		
		if(sObjCSSClass == '') {
			sObjCSSClass = options['tabsCSSClass'];
			obj.addClass(sObjCSSClass);
		}
		
		sObjNavCSSClass = sObjCSSClass + options['tabsNavSuffix'];
		sObjNavCSSClassActive = sObjCSSClass + options['tabsNavActiveSuffix'];
		
		// Tabs Navigation
		var strTabsNav = "<ul class='" + sObjNavCSSClass + "'>";
		obj.find(options['tabSelector']).each(function(){
			strTabsNav += '<li><span>' + $(this).html() + '</span></li>';
		});
		strTabsNav += '</ul>';
		
		// Change DOM
		obj.prepend(strTabsNav).find(options['tabSelector']).remove().end()
			.find('> .' + sObjNavCSSClass + ' li:first-child').addClass(sObjNavCSSClassActive).end()
			.find(options['bodySelector']).hide().filter(':eq(0)').show();
		
		// Navigation Events
		obj.find('> .' + sObjNavCSSClass).click(function(e) {
			var $nav = $(e.target).closest('li');
			
			if(!$nav.hasClass(sObjNavCSSClassActive)) {
				$nav.addClass(sObjNavCSSClassActive).siblings().removeClass(sObjNavCSSClassActive)
					.closest('.' + sObjCSSClass)
					.find(options['bodySelector']).hide().filter(':eq(' + $nav.index() + ')').show();
				
			}
		});
	});
};
})(jQuery);