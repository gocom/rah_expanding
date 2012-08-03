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
			 * TextAreaExpander plugin for jQuery
			 * v1.0
			 * Expands or contracts a textarea height depending on the
			 * quatity of content entered by the user in the box.
			 *
			 * By Craig Buckler, Optimalworks.net
			 *
			 * As featured on SitePoint.com:
			 * http://www.sitepoint.com/blogs/2009/07/29/build-auto-expanding-textarea-1/
			 *
			 * Please use as you wish at your own risk.
			 */
			
			(function($) {
			
				// jQuery plugin definition
				$.fn.rah_TextAreaExpander = function(minHeight, maxHeight) {
			
					var hCheck = !($.browser.msie || $.browser.opera);
			
					// resize a textarea
					function ResizeTextarea(e) {
			
						// event or initialize element?
						e = e.target || e;
			
						// find content length and box width
						var vlen = e.value.length, ewidth = e.offsetWidth;
						if (vlen != e.valLength || ewidth != e.boxWidth) {
			
							if (hCheck && (vlen < e.valLength || ewidth != e.boxWidth)) e.style.height = "0px";
							var h = Math.max(e.expandMin, Math.min(e.scrollHeight, e.expandMax));
			
							e.style.overflow = (e.scrollHeight > h ? "auto" : "hidden");
							e.style.height = h + "px";
			
							e.valLength = vlen;
							e.boxWidth = ewidth;
						}
			
						return true;
					};
			
					// initialize
					this.each(function() {
			
						// is a textarea?
						if (this.nodeName.toLowerCase() != "textarea") return;
			
						// set height restrictions
						var p = this.className.match(/expand(\d+)\-*(\d+)*/i);
						this.expandMin = minHeight || (p ? parseInt('0'+p[1], 10) : 0);
						this.expandMax = maxHeight || (p ? parseInt('0'+p[2], 10) : 99999);
			
						// initial resize
						ResizeTextarea(this);
			
						// zero vertical padding and add events
						if (!this.Initialized) {
							this.Initialized = true;
							$(this)
								.css({'padding-top' : 0, 'padding-bottom' : 0})
								.bind('keyup focus input', ResizeTextarea);
						}
					});
			
					return this;
				};
			
			})(jQuery);
EOF;

		echo script_js($js);
	}
}

?>