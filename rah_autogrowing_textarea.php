<?php

/**
 * Rah_autogrowing_textarea plugin for Textpattern CMS.
 *
 * @author Jukka Svahn
 * @date 2008-
 * @license GNU GPLv2
 * @link http://rahforum.biz/plugins/rah_autogrowing_textarea
 * 
 * Copyright (C) 2008 Jukka Svahn <http://rahforum.biz>
 * Licensed under GNU Genral Public License version 2
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

	if(@txpinterface == 'admin') {
		new rah_autogrowing_textearea();
	}

class rah_autogrowing_textearea {

	/**
	 * Constructor
	 */

	public function __construct() {
		register_callback(array($this, 'jquery'), 'admin_side', 'head_end');
		register_callback(array($this, 'initialize'), 'admin_side', 'head_end');
	}

	/**
	 * Adds the required scripts to the <head>
	 */

	public function initialize() {

		$js = <<<EOF
			$(document).ready(function(){
				$('textarea:not(.rah_autogrowing_textarea_disable)').each(function() {
					$(this).rah_TextAreaExpander($(this).height(), 99999);
				});
			});
EOF;

		echo script_js($js);
	}

	/**
 	 * Stores the TextAreaExpander jQuery plugin
 	 */

	public function jquery() {

		$js = <<<EOF
			/**
			 * TextAreaExpander plugin for jQuery v1.0
			 *
			 * Expands or contracts a textarea height depending on the
			 * quatity of content entered by the user in the box.
			 *
			 * By Craig Buckler, Optimalworks.net
			 *
			 * As featured on SitePoint.com:
			 * http://www.sitepoint.com/build-auto-expanding-textarea-1/
			 *
			 * Please use as you wish at your own risk.
			 */
			
			(function($) {
				$.fn.rah_TextAreaExpander = function(minHeight, maxHeight) {

					var hCheck = !($.browser.msie || $.browser.opera);
					
					ResizeTextarea = function(e) {
						e = e.target || e;
						
						var vlen = e.value.length, ewidth = e.offsetWidth;
						
						if(vlen != e.valLength || ewidth != e.boxWidth) {
			
							if(hCheck && (vlen < e.valLength || ewidth != e.boxWidth)) {
								$(e).css('height', '0px');
							}
							
							var h = Math.max(e.expandMin, Math.min(e.scrollHeight, e.expandMax));
			
							$(e).css({
								'overflow' : (e.scrollHeight > h ? 'auto' : 'hidden'),
								'height' : h+'px'
							});
			
							e.valLength = vlen;
							e.boxWidth = ewidth;
						}
			
						return true;
					};
			
					this.each(function() {
					
						if(!$(this).is('textarea') || this.Initialized === true) {
							return;
						}
						
						this.expandMin = minHeight || 0;
						this.expandMax = maxHeight || 99999;
						this.Initialized = true;
						
						$(this)
							.css({'padding-top' : 0, 'padding-bottom' : 0, 'overflow' : 'hidden'})
							.bind('keyup focus input', ResizeTextarea);
						
						ResizeTextarea(this);
					});
			
					return this;
				};
			})(jQuery);
EOF;

		echo script_js($js);
	}
}

?>