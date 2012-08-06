<?php

/**
 * rah_expanding plugin for Textpattern CMS.
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
		new rah_expanding();
	}

class rah_expanding {

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
				$('textarea:not(.rah_expanding_disable)').rah_expanding();
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
			 * Forked from Autosize project written by Jack Moore:
			 *
			 * Autosize 1.10 - jQuery plugin for textareas
			 * by Jack Moore
			 * <http://www.jacklmoore.com/autosize>
			 *
			 * (c) 2012 Jack Moore - jacklmoore.com
			 * license: www.opensource.org/licenses/mit-license.php
			 */

			(function($) {

				var test = $('<textarea/>').attr('oninput', 'return').css('line-height', '99px');

				if(
					$.isFunction(test.prop('oninput')) === false ||
					test.css('line-height') !== '99px'
				) {
					$.fn.rah_expanding = function () {
						return this;
					};
					return;
				}

				$.fn.rah_expanding = function() {

					var copy = '<textarea tabindex="-1" style="position:absolute; top:-9999px; left:-9999px; right:auto; bottom:auto; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; min-width:0 !important; overflow:hidden">',

					copyStyle = [
						'font-family',
						'font-size',
						'font-weight',
						'font-style',
						'font-variant',
						'letter-spacing',
						'text-transform',
						'word-spacing',
						'text-indent',
						'line-height',
						'tab-size',
						'text-align',
						'text-rendering'
					];

					return this.each(function () {

						var textarea = $(this);

						if(textarea.data('rah_expanding_mirror') || textarea.data('rah_expanding_is_mirror')) {
							return;
						}

						var opt = {
							min : textarea.height(),
							max : parseInt(textarea.css('max-height'), 10),
							offset : 0,
							mirror : $(copy).data('rah_expanding_is_mirror', true),
							active : false
						};

						if(!opt.max || opt.max < 0 || opt.max > 99999) {
							opt.max = 99999;
						}

						if(opt.max <= opt.min) {
							return;
						}

						if(
							textarea.css('box-sizing') === 'border-box' || 
							textarea.css('-moz-box-sizing') === 'border-box'
						) {
							opt.offset = textarea.outerHeight() - textarea.height();
						}

						textarea.data('rah_expanding_mirror', opt.mirror).css({
							'overflow' : 'hidden',
							'overflow-x' : 'hidden',
							'overflow-y' : 'hidden',
							'word-wrap' : 'break-word',
							'resize' : 'none'
						});

						var methods = {
							resize : function() {

								if(opt.active) {
									return;
								}

								opt.active = true;

								var height = Math.max(opt.min, Math.min(opt.max, 
									opt.mirror
									.val(textarea.val())
									.css({
										'overflow-y' : textarea.css('overflow-y'),
										'width' : textarea.css('width'),
									})
									.scrollTop(0)
									.scrollTop(99999)
									.scrollTop()
								));

								textarea.css({
									'overflow-y' : height < opt.max ? 'hidden' : 'scroll',
									'height' : (height + opt.offset) + 'px',
									'max-height' : (height + opt.offset) + 'px',
									'min-height' : (height + opt.offset) + 'px'
								});

								setTimeout(function () {
									opt.active = false;
								}, 1);
							},
							copyStyles : function() {
								$.each(copyStyle, function(key, value) {
									opt.mirror.css(value, textarea.css(value));
								});
							}
						};

						methods.copyStyles();
						$('body').append(opt.mirror);
						textarea.bind('input keyup blur focus resize rah_expanding_resize', methods.resize);
						$(window).bind('orientationchange resize', function(){
							methods.copyStyles();
							methods.resize();
						});
						methods.resize();
					});
				};
			}(jQuery));
EOF;

		echo script_js($js);
	}
}

?>