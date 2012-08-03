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
				$('textarea:not(.rah_autogrowing_textarea_disable)').rah_TextAreaExpander();
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
				$.fn.rah_TextAreaExpander = function() {

					var hCheck = !($.browser.msie || $.browser.opera);
					var defaults = {content : 0, outer : 0, h : 0, min : 0, max : 0, offset : 0};
					
					ResizeTextarea = function(e) {
						e = $( e.target || e );
							
						var dim = {
							content : e.val().length,
							outer : e.outerWidth(),
							h : 0,
							offset : 0
						};
						
						var opt = $.extend(defaults, e.data('rah_agwt'));
						
						if(dim.content == opt.content && dim.outer == opt.outer) {
							return;
						}
						
						var range = e.data('rah_agwt_range');
						dim.offset = e.height() - e.innerHeight();
						
						if(
							e.css('box-sizing') === 'border-box' || 
							e.css('-moz-box-sizing') === 'border-box' || 
							e.css('-webkit-box-sizing') === 'border-box'
						){
							dim.offset = e.outerHeight() - e.innerHeight();
						}
			
						if(hCheck && (dim.content < opt.content || dim.outer != opt.outer)) {
							e.height(0);
						}

						dim.h = Math.max(range.min, Math.min(e.prop('scrollHeight'), range.max))+dim.offset;

						e
							.css('overflow', e.prop('scrollHeight') > dim.h ? 'auto' : 'hidden')
							.height(dim.h)
							.data('rah_agwt_opt', $.extend(opt, dim));
					};

					this.each(function() {
						
						var obj = $(this);
					
						if(!obj.is('textarea') || obj.data('rah_agwt_range')) {
							return;
						}
						
						var range = {
							min : obj.height() || 0,
							max : parseInt(obj.css('max-height'), 10) || 99999
						};
						
						if(Math.max(range.min, range.max) === range.min) {
							return;
						}
						
						obj
							.data('rah_agwt_range', range)
							.css({'overflow' : 'hidden', 'box-sizing' : 'border-box'})
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