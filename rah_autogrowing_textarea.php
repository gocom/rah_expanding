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
		register_callback(array($this, 'head'), 'admin_side', 'head_end');
	}

	/**
	 * Adds the required scripts to the <head>
	 */

	public function head() {
		
		//global $event;
		
		/*$css = $js = array();
		
		foreach($rs as $a){
			extract($a);
			$js[] = 'textarea#'.escape_js($name);
			$css[] = 
				'	textarea#'.htmlspecialchars($name).' {'.n.
				'		min-height: '.$min_height.'px;'.n.
				'		height: '.$height.'px;'.n.
				'		line-height: '.$line_height.'px;'.n.
				'		max-height: '.$max_height.'px;'.n.
				'	}';
		}
		*/
		
		echo <<<EOF
			<style type="text/css">
				textarea#Body {
					min-height: 200px;
					height: 200px;
					line-height: 16px;
					max-height: 600px;
				}
			</style>
EOF;

		$js = <<<EOF
			$(document).ready(function(){
				$("textarea#Body").autogrow();
			});
EOF;

		echo script_js($js);
	}

	/**
 	 * Stores the jQuery plugin as base64 encoded string
 	 */

	public function jquery() {
		
		/*
			Auto Expanding Text Area (1.2.2)
			by Chrys Bader (www.chrysbader.com)
			chrysb@gmail.com
		
			Special thanks to:
			Jake Chapa - jake@hybridstudio.com
			John Resig - jeresig@gmail.com
		
			Copyright (c) 2008 Chrys Bader (www.chrysbader.com)
			Dual licensed under the MIT (MIT-LICENSE.txt)
			and GPL (GPL-LICENSE.txt) licenses.
		*/
		
		$js = 'LyogDQoqDQoqCUNvcHlyaWdodHMgZm9yIHRoZSBmb2xsb3dpbmcgSmF2YXNjcmlwdCBjb2RlOg0KKg0KKglBdXRvIEV4cGFuZGluZyBUZXh0IEFyZWEgKDEuMi4yKQ0KKglieSBDaHJ5cyBCYWRlciAod3d3LmNocnlzYmFkZXIuY29tKQ0KKgljaHJ5c2JAZ21haWwuY29tDQoqDQoqCVNwZWNpYWwgdGhhbmtzIHRvOg0KKglKYWtlIENoYXBhIC0gamFrZUBoeWJyaWRzdHVkaW8uY29tDQoqCUpvaG4gUmVzaWcgLSBqZXJlc2lnQGdtYWlsLmNvbQ0KKg0KKiAJQ29weXJpZ2h0IChjKSAyMDA4IENocnlzIEJhZGVyICh3d3cuY2hyeXNiYWRlci5jb20pDQoqIAlEdWFsIGxpY2Vuc2VkIHVuZGVyIHRoZSBNSVQgKE1JVC1MSUNFTlNFLnR4dCkNCiogCWFuZCBHUEwgKEdQTC1MSUNFTlNFLnR4dCkgbGljZW5zZXMuDQoqDQoqLw0KDQooZnVuY3Rpb24oalF1ZXJ5KSB7DQoJdmFyIHNlbGYgPSBudWxsOw0KCWpRdWVyeS5mbi5hdXRvZ3JvdyA9IGZ1bmN0aW9uKG8pew0KCQlyZXR1cm4gdGhpcy5lYWNoKGZ1bmN0aW9uKCkgew0KCQkJbmV3IGpRdWVyeS5hdXRvZ3Jvdyh0aGlzLCBvKTsNCgkJfSk7DQoJfTsNCglqUXVlcnkuYXV0b2dyb3cgPSBmdW5jdGlvbiAoZSwgbyl7DQoJCXRoaXMub3B0aW9ucwkJICAJPSBvIHx8IHt9Ow0KCQl0aGlzLmR1bW15CQkJICAJPSBudWxsOw0KCQl0aGlzLmludGVydmFsCSAJICAJPSBudWxsOw0KCQl0aGlzLmxpbmVfaGVpZ2h0CSAgCT0gdGhpcy5vcHRpb25zLmxpbmVIZWlnaHQgfHwgcGFyc2VJbnQoalF1ZXJ5KGUpLmNzcygnbGluZS1oZWlnaHQnKSk7DQoJCXRoaXMubWluX2hlaWdodAkJICAJPSB0aGlzLm9wdGlvbnMubWluSGVpZ2h0IHx8IHBhcnNlSW50KGpRdWVyeShlKS5jc3MoJ21pbi1oZWlnaHQnKSk7DQoJCXRoaXMubWF4X2hlaWdodAkJICAJPSB0aGlzLm9wdGlvbnMubWF4SGVpZ2h0IHx8IHBhcnNlSW50KGpRdWVyeShlKS5jc3MoJ21heC1oZWlnaHQnKSk7Ow0KCQl0aGlzLnRleHRhcmVhCQkgIAk9IGpRdWVyeShlKTsNCgkJaWYgKHRoaXMubGluZV9oZWlnaHQgPT0gTmFOKXsNCgkJCXRoaXMubGluZV9oZWlnaHQgPSAwOw0KCQl9DQoJCWlmICh0aGlzLm1pbl9oZWlnaHQgPT0gTmFOIHx8IHRoaXMubWluX2hlaWdodCA9PSAwKXsNCgkJCXRoaXMubWluX2hlaWdodCA9PSB0aGlzLnRleHRhcmVhLmhlaWdodCgpOwkNCgkJfQ0KCQl0aGlzLmluaXQoKTsNCgl9Ow0KCWpRdWVyeS5hdXRvZ3Jvdy5mbiA9IGpRdWVyeS5hdXRvZ3Jvdy5wcm90b3R5cGUgPSB7DQoJCWF1dG9ncm93OiAnMS4yLjInDQoJfTsNCiAJalF1ZXJ5LmF1dG9ncm93LmZuLmV4dGVuZCA9IGpRdWVyeS5hdXRvZ3Jvdy5leHRlbmQgPSBqUXVlcnkuZXh0ZW5kOw0KCWpRdWVyeS5hdXRvZ3Jvdy5mbi5leHRlbmQoew0KCQlpbml0OiBmdW5jdGlvbigpIHsJCQkNCgkJCXZhciBzZWxmID0gdGhpczsJCQkNCgkJCXRoaXMudGV4dGFyZWEuY3NzKHtvdmVyZmxvdzogJ2hpZGRlbicsIGRpc3BsYXk6ICdibG9jayd9KTsNCgkJCXRoaXMudGV4dGFyZWEuYmluZCgnZm9jdXMnLCBmdW5jdGlvbigpIHsgc2VsZi5zdGFydEV4cGFuZCgpIH0gKS5iaW5kKCdibHVyJywgZnVuY3Rpb24oKSB7IHNlbGYuc3RvcEV4cGFuZCgpIH0pOw0KCQkJdGhpcy5jaGVja0V4cGFuZCgpOwkNCgkJfSwNCgkJc3RhcnRFeHBhbmQ6IGZ1bmN0aW9uKCkgewkJCQkNCgkJICB2YXIgc2VsZiA9IHRoaXM7DQoJCQl0aGlzLmludGVydmFsID0gd2luZG93LnNldEludGVydmFsKGZ1bmN0aW9uKCkge3NlbGYuY2hlY2tFeHBhbmQoKX0sIDQwMCk7DQoJCX0sDQoJCXN0b3BFeHBhbmQ6IGZ1bmN0aW9uKCkgew0KCQkJY2xlYXJJbnRlcnZhbCh0aGlzLmludGVydmFsKTsJDQoJCX0sDQoJCWNoZWNrRXhwYW5kOiBmdW5jdGlvbigpIHsNCgkJCWlmICh0aGlzLmR1bW15ID09IG51bGwpew0KCQkJCXRoaXMuZHVtbXkgPSBqUXVlcnkoJzxkaXY+PC9kaXY+Jyk7DQoJCQkJdGhpcy5kdW1teS5jc3Moew0KCQkJCQknZm9udC1zaXplJyAgOiB0aGlzLnRleHRhcmVhLmNzcygnZm9udC1zaXplJyksDQoJCQkJCSdmb250LWZhbWlseSc6IHRoaXMudGV4dGFyZWEuY3NzKCdmb250LWZhbWlseScpLA0KCQkJCQknd2lkdGgnICAgICAgOiB0aGlzLnRleHRhcmVhLmNzcygnd2lkdGgnKSwNCgkJCQkJJ3BhZGRpbmcnICAgIDogdGhpcy50ZXh0YXJlYS5jc3MoJ3BhZGRpbmcnKSwNCgkJCQkJJ2xpbmUtaGVpZ2h0JzogdGhpcy5saW5lX2hlaWdodCArICdweCcsDQoJCQkJCSdvdmVyZmxvdy14JyA6ICdoaWRkZW4nLA0KCQkJCQkncG9zaXRpb24nICAgOiAnYWJzb2x1dGUnLA0KCQkJCQkndG9wJyAgICAgICAgOiAwLA0KCQkJCQknbGVmdCcJCSA6IC05OTk5DQoJCQkJfSkuYXBwZW5kVG8oJ2JvZHknKTsNCgkJCX0NCgkJCXZhciBodG1sID0gdGhpcy50ZXh0YXJlYS52YWwoKS5yZXBsYWNlKC8oPHw+KS9nLCAnJyk7DQoJCQlpZiAoJC5icm93c2VyLm1zaWUpew0KCQkJCWh0bWwgPSBodG1sLnJlcGxhY2UoL1xuL2csICc8QlI+bmV3Jyk7DQoJCQl9ZWxzZXsNCgkJCQlodG1sID0gaHRtbC5yZXBsYWNlKC9cbi9nLCAnPGJyPm5ldycpOw0KCQkJfQ0KCQkJaWYgKHRoaXMuZHVtbXkuaHRtbCgpICE9IGh0bWwpew0KCQkJCXRoaXMuZHVtbXkuaHRtbChodG1sKTsNCgkJCQlpZiAodGhpcy5tYXhfaGVpZ2h0ID4gMCAmJiAodGhpcy5kdW1teS5oZWlnaHQoKSArIHRoaXMubGluZV9oZWlnaHQgPiB0aGlzLm1heF9oZWlnaHQpKXsNCgkJCQkJdGhpcy50ZXh0YXJlYS5jc3MoJ292ZXJmbG93LXknLCAnYXV0bycpOwkNCgkJCQl9DQoJCQkJZWxzZXsNCgkJCQkJdGhpcy50ZXh0YXJlYS5jc3MoJ292ZXJmbG93LXknLCAnaGlkZGVuJyk7DQoJCQkJCWlmICh0aGlzLnRleHRhcmVhLmhlaWdodCgpIDwgdGhpcy5kdW1teS5oZWlnaHQoKSArIHRoaXMubGluZV9oZWlnaHQgfHwgKHRoaXMuZHVtbXkuaGVpZ2h0KCkgPCB0aGlzLnRleHRhcmVhLmhlaWdodCgpKSl7CQ0KCQkJCQkJdGhpcy50ZXh0YXJlYS5hbmltYXRlKHtoZWlnaHQ6ICh0aGlzLmR1bW15LmhlaWdodCgpICsgdGhpcy5saW5lX2hlaWdodCkgKyAncHgnfSwgMTAwKTsJDQoJCQkJCX0NCgkJCQl9DQoJCQl9DQoJCX0NCgl9KTsNCn0pKGpRdWVyeSk7';
		
		echo script_js(base64_decode($js));
	}
}

?>