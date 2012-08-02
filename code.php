<?php	##################
	#
	#	rah_autogrowing_textarea-plugin for Textpattern
	#	version 0.3.3
	#	by Jukka Svahn
	#	http://rahforum.biz
	#
	###################

	if (@txpinterface == 'admin') {
		global $event;
		rah_autogrowing_textarea_install();
		register_callback('rah_autogrowing_textarea','admin_side','head_end');
		add_privs('rah_autogrowing_textarea_page', '1,2');
		register_tab("extensions", "rah_autogrowing_textarea_page", "Autogrowing Textarea");
		register_callback("rah_autogrowing_textarea_page", "rah_autogrowing_textarea_page");
	} else if(gps('rah_autogrowing_js')) rah_autogrowing_textarea_js();

	function rah_autogrowing_textarea() {
		global $event;
		$rs = safe_rows_start('name,min_height,height,line_height,max_height','rah_autogrowing_textarea', "page='".doSlash($event)."' and active='Yes' order by id asc");
		if($rs && numRows($rs) > 0) {
			$css = array();
			$out = array();
			while ($a = nextRow($rs)){
				extract($a);
				$out[] = 'textarea#'.$name;
				$css[] = 
					'			textarea#'.$name.' {'.n.
					'				min-height: '.$min_height.'px;'.n.
					'				height: '.$height.'px;'.n.
					'				line-height: '.$line_height.'px;'.n.
					'				max-height: '.$max_height.'px;'.n.
					'			}'.n;
			}
			echo n.n.
				'	<!-- Start of code: rah_autogrowing_textarea -->'.n.
				'		<script type="text/javascript" src="'.hu.'?rah_autogrowing_js=1"></script>'.n.
				'		<script language="javascript" type="text/javascript">'.n.
				'			$(document).ready (function() {'.n.
				'				$("'.trim(implode(',',$out),',').'").autogrow();'.n.
				'			});'.n.
				'		</script>'.n.
				'		<style type="text/css">'.n.
				implode('',$css).
				'		</style>'.n.
				'	<!-- End of code: rah_autogrowing_textarea -->'.n.n;
		}
	}

	function rah_autogrowing_textarea_install() {
		safe_query(
			"CREATE TABLE IF NOT EXISTS ".safe_pfx('rah_autogrowing_textarea')." (
				`id` int(11) NOT NULL auto_increment,
				`posted` datetime NOT NULL default '0000-00-00 00:00:00',
				`name` varchar(255) NOT NULL default '',
				`min_height` varchar(12) NOT NULL default '',
				`height` varchar(12) NOT NULL default '',
				`line_height` varchar(12) NOT NULL default '',
				`max_height` varchar(12) NOT NULL default '',
				`active` varchar(3) NOT NULL default '',
				`page` varchar(255) NOT NULL default '',
				PRIMARY KEY(`id`)
			) PACK_KEYS=1 AUTO_INCREMENT=1"
		);
		if(safe_count('rah_autogrowing_textarea',"name='excerpt'") == 0) {
			safe_insert(
				"rah_autogrowing_textarea",
				"name='excerpt',
				posted=now(),
				min_height='105',
				height='105',
				line_height='16',
				max_height='3000',
				active='Yes',
				page='article'"
			);
		}
		if(safe_count('rah_autogrowing_textarea',"name='body'") == 0) {
			safe_insert(
				"rah_autogrowing_textarea",
				"name='body',
				posted=now(),
				min_height='425',
				height='425',
				line_height='16',
				max_height='3000',
				active='Yes',
				page='article'"
			);
		}
	}

	function rah_autogrowing_textarea_list($message='') {
		pagetop('Autogrowing Textarea',$message);
		global $event;
		echo 
			n.n.
				'	<form method="post" action="index.php" style="width:950px;margin:0 auto;position:relative;">'.n.
				'		<h1><strong>rah_autogrowing_textarea</strong> | Create new Textarea callback</h1>'.n.
				rah_autogrowing_textarea_header().
				'		<table cellspacing="0" cellpadding="0" id="list" class="list" style="width:100%;">'.n.
				'			<tr>'.n.
				'				<th>Name</th>'.n.
				'				<th>Updated</th>'.n.
				'				<th>Active?</th>'.n.
				'				<th>Page</th>'.n.
				'				<th>#ID</th>'.n.
				'				<th>&#160;</th>'.n.
				'			</tr>'.n;

		$rs = safe_rows_start('id,name,posted,page,active','rah_autogrowing_textarea',"1=1 order by name asc, id asc");
		if ($rs and numRows($rs) > 0){
			while ($a = nextRow($rs)){
				extract($a);
				echo 
					'			<tr>'.n.
					'				<td><a href="?event='.$event.'&amp;step=rah_autogrowing_textarea_form&amp;id='.$id.'">'.(($name) ? htmlspecialchars($name) : gTxt('untitled')).'</a></td>'.n.
					'				<td>'.safe_strftime('%b %d %Y %H:%M',strtotime($posted)).'</td>'.n.
					'				<td>'.$active.'</td>'.n.
					'				<td><a href="?event='.htmlspecialchars($page).'">'.htmlspecialchars($page).'</a></td>'.n.
					'				<td>'.(($name) ? '<code>textarea#'.htmlspecialchars($name).'</code>' : '&#160;').'</td>'.n.
					'				<td><input type="checkbox" name="delete[]" value="'.$id.'" /></td>'.n.
					'			</tr>'.n;
			}
		} else echo 
					'			<tr>'.n.
					'				<td colspan="6" style="text-align:center;">No rules done yet.</td>'.n.
					'			</tr>'.n;
		
		echo 
			'		</table>'.n.
			'		<p style="text-align: right;padding-top:10px;">'.n.
			'			<label for="rah_autogrowing_textarea_step">With selected:</label>'.n.
			'			<select name="step" id="rah_autogrowing_textarea_step">'.n.
			'				<option value="">Select...</option>'.n.
			'				<option value="rah_autogrowing_textarea_delete">Delete</option>'.n.
			'			</select>'.n.
			'			<input type="submit" class="smallerbox" value="Go" />'.n.
			'		</p>'.n.
			'		<input type="hidden" name="event" value="'.$event.'" />'.n.
			'	</form>'.n.n;
	}

	function rah_autogrowing_textarea_events($page='') {
		global $privs, $plugin_areas;
		$areas['content'] = array(
			gTxt('tab_organise') => 'category',
			gTxt('tab_write') => 'article',
			gTxt('tab_list') =>  'list',
			gTxt('tab_image') => 'image',
			gTxt('tab_file') => 'file',					 
			gTxt('tab_link') => 'link',
			gTxt('tab_comments') => 'discuss'
		);
		$areas['presentation'] = array(
			gTxt('tab_sections') => 'section',
			gTxt('tab_pages') => 'page',
			gTxt('tab_forms') => 'form',
			gTxt('tab_style') => 'css'
		);
		$areas['admin'] = array(
			gTxt('tab_diagnostics') => 'diag',
			gTxt('tab_preferences') => 'prefs',
			gTxt('tab_site_admin')  => 'admin',
			gTxt('tab_logs') => 'log',
			gTxt('tab_import') => 'import'
		);
		$areas['extensions'] = array();
		if(is_array($plugin_areas)) $areas = array_merge_recursive($areas, $plugin_areas);
		$out = array();
		foreach ($areas as $a => $b) {
			if (!has_privs('tab.'.$a)) {
				continue;
			}
			if (count($b) > 0) {
				$out[] = '							<optgroup label="'.gTxt('tab_'.$a).'">'.n;
				foreach ($b as $c => $d) {
					if (has_privs($d)) {
						$out[] = '								<option value="'.$d.'"'.(($d == $page) ? ' selected="selected"' : '').'>'.$c.'</option>'.n;
					}
				}
				$out[] = '							</optgroup>'.n;
			}
		}
		if($out) return implode('',$out);
	}

	function rah_autogrowing_textarea_page() {
		global $step;
		require_privs('rah_autogrowing_textarea_page');
		if(in_array($step,array(
			'rah_autogrowing_textarea_save',
			'rah_autogrowing_textarea_delete',
			'rah_autogrowing_textarea_form'
		))) $step();
		else rah_autogrowing_textarea_list();
	}

	function rah_autogrowing_textarea_header() {
		return '		<p>'.
			'&#187; <a href="?event=rah_autogrowing_textarea_page&amp;step=rah_autogrowing_textarea_form">Create a new callback</a>'.
			' &#187; <a href="?event=plugin&amp;step=plugin_help&amp;name=rah_autogrowing_textarea">Documentation</a>'.
			'</p>'.n;
	}

	function rah_autogrowing_textarea_form($message='') {
		global $event;
		pagetop('Autogrowing Textarea',$message);
		if(gps('id')) {
			$rs = safe_row('*','rah_autogrowing_textarea',"id='".doSlash(gps('id'))."'");
			extract($rs);
		} else {
			$name = '';
			$min_height = '425';
			$height = '425';
			$line_height = '16';
			$max_height = '3000';
			$active = '';
			$page = '';
			$id = '';
		}
		echo 
			n.n.
				'	<form method="post" action="index.php" style="width:950px;margin:0 auto;position:relative;">'.n.
				'		<h1><strong>rah_autogrowing_textarea</strong> | Create a new Textarea callback</h1>'.n.
				rah_autogrowing_textarea_header().
				'		<p><label for="name"><code>ID</code> of the textarea:</label><br /><input class="edit" type="text" size="60" name="name" id="name" value="'.htmlspecialchars($name).'" /></p>'.n.
				'		<fieldset style="padding:20px;margin:20px 0;">'.n.
				'			<legend>Styling preferences and <acronym title="Cascading Style Sheets">CSS</acronym> rules</legend>'.n.
				'			<table style="width:100%;" cellspacing="2" cellpadding="0" border="0">'.n.
				'				<tr>'.n.
				'					<td><label for="min_height">Min-height:</label></td>'.n.
				'					<td><input class="edit" id="min_height" type="text" size="5" name="min_height" value="'.htmlspecialchars($min_height).'" /> px</td>'.n.
				'					<td><label for="max_height">Max-height:</label></td>'.n.
				'					<td><input class="edit" id="max_height" type="text" size="5" name="max_height" value="'.htmlspecialchars($max_height).'" /> px</td>'.n.
				'				</tr>'.n.
				'				<tr>'.n.
				'					<td><label for="line_height">Line-height:</label></td>'.n.
				'					<td><input class="edit" id="line_height" type="text" size="5" name="line_height" value="'.htmlspecialchars($line_height).'" /> px</td>'.n.
				'					<td><label for="height">Height:</label></td>'.n.
				'					<td><input class="edit" id="height" type="text" size="5" name="height" value="'.htmlspecialchars($height).'" /> px</td>'.n.
				'				</tr>'.n.
				'			</table>'.n.
				'		</fieldset>'.n.
				'		<fieldset style="padding:20px;margin:20px 0;">'.n.
				'			<legend>Callback settings</legend>'.n.
				'			<table cellspacing="2" cellpadding="0" border="0">'.n.
				'				<tr>'.n.
				'					<td><label for="rah_page">Event:</label></td>'.n.
				'					<td>'.n.
				'						<select name="page" class="edit" id="rah_page">'.n.
				rah_autogrowing_textarea_events($page).
				'						</select>'.n.
				'					</td>'.n.
				'					<td><label for="rah_active">Active?</label></td>'.n.
				'					<td>'.n.
				'						<select name="active" id="rah_active" class="edit">'.n.
				'							<option value="Yes"'.(($active == 'Yes') ? ' selected="selected"' : '').'>Yes</option>'.n.
				'							<option value="No"'.(($active == 'No') ? ' selected="selected"' : '').'>No</option>'.n.
				'						</select>'.n.
				'					</td>'.n.
				'				</tr>'.n.
				'			</table>'.n.
				'			<input type="hidden" name="event" value="'.$event.'" />'.n.
				'			<input type="hidden" name="step" value="rah_autogrowing_textarea_save" />'.n.
				(($id) ? '			<input type="hidden" name="id" value="'.$id.'" />'.n : '').
				'		</fieldset>'.n.
				'		<p><input type="submit" value="Save" class="publish" /></p>'.n.
				'	</form>'.n.n;
	}

	function rah_autogrowing_textarea_js() {
		ob_start();
		ob_end_clean();
		header('Content-type: application/x-javascript');
		$js = 'LyogDQoqDQoqCUNvcHlyaWdodHMgZm9yIHRoZSBmb2xsb3dpbmcgSmF2YXNjcmlwdCBjb2RlOg0KKg0KKglBdXRvIEV4cGFuZGluZyBUZXh0IEFyZWEgKDEuMi4yKQ0KKglieSBDaHJ5cyBCYWRlciAod3d3LmNocnlzYmFkZXIuY29tKQ0KKgljaHJ5c2JAZ21haWwuY29tDQoqDQoqCVNwZWNpYWwgdGhhbmtzIHRvOg0KKglKYWtlIENoYXBhIC0gamFrZUBoeWJyaWRzdHVkaW8uY29tDQoqCUpvaG4gUmVzaWcgLSBqZXJlc2lnQGdtYWlsLmNvbQ0KKg0KKiAJQ29weXJpZ2h0IChjKSAyMDA4IENocnlzIEJhZGVyICh3d3cuY2hyeXNiYWRlci5jb20pDQoqIAlEdWFsIGxpY2Vuc2VkIHVuZGVyIHRoZSBNSVQgKE1JVC1MSUNFTlNFLnR4dCkNCiogCWFuZCBHUEwgKEdQTC1MSUNFTlNFLnR4dCkgbGljZW5zZXMuDQoqDQoqLw0KDQooZnVuY3Rpb24oalF1ZXJ5KSB7DQoJdmFyIHNlbGYgPSBudWxsOw0KCWpRdWVyeS5mbi5hdXRvZ3JvdyA9IGZ1bmN0aW9uKG8pew0KCQlyZXR1cm4gdGhpcy5lYWNoKGZ1bmN0aW9uKCkgew0KCQkJbmV3IGpRdWVyeS5hdXRvZ3Jvdyh0aGlzLCBvKTsNCgkJfSk7DQoJfTsNCglqUXVlcnkuYXV0b2dyb3cgPSBmdW5jdGlvbiAoZSwgbyl7DQoJCXRoaXMub3B0aW9ucwkJICAJPSBvIHx8IHt9Ow0KCQl0aGlzLmR1bW15CQkJICAJPSBudWxsOw0KCQl0aGlzLmludGVydmFsCSAJICAJPSBudWxsOw0KCQl0aGlzLmxpbmVfaGVpZ2h0CSAgCT0gdGhpcy5vcHRpb25zLmxpbmVIZWlnaHQgfHwgcGFyc2VJbnQoalF1ZXJ5KGUpLmNzcygnbGluZS1oZWlnaHQnKSk7DQoJCXRoaXMubWluX2hlaWdodAkJICAJPSB0aGlzLm9wdGlvbnMubWluSGVpZ2h0IHx8IHBhcnNlSW50KGpRdWVyeShlKS5jc3MoJ21pbi1oZWlnaHQnKSk7DQoJCXRoaXMubWF4X2hlaWdodAkJICAJPSB0aGlzLm9wdGlvbnMubWF4SGVpZ2h0IHx8IHBhcnNlSW50KGpRdWVyeShlKS5jc3MoJ21heC1oZWlnaHQnKSk7Ow0KCQl0aGlzLnRleHRhcmVhCQkgIAk9IGpRdWVyeShlKTsNCgkJaWYgKHRoaXMubGluZV9oZWlnaHQgPT0gTmFOKXsNCgkJCXRoaXMubGluZV9oZWlnaHQgPSAwOw0KCQl9DQoJCWlmICh0aGlzLm1pbl9oZWlnaHQgPT0gTmFOIHx8IHRoaXMubWluX2hlaWdodCA9PSAwKXsNCgkJCXRoaXMubWluX2hlaWdodCA9PSB0aGlzLnRleHRhcmVhLmhlaWdodCgpOwkNCgkJfQ0KCQl0aGlzLmluaXQoKTsNCgl9Ow0KCWpRdWVyeS5hdXRvZ3Jvdy5mbiA9IGpRdWVyeS5hdXRvZ3Jvdy5wcm90b3R5cGUgPSB7DQoJCWF1dG9ncm93OiAnMS4yLjInDQoJfTsNCiAJalF1ZXJ5LmF1dG9ncm93LmZuLmV4dGVuZCA9IGpRdWVyeS5hdXRvZ3Jvdy5leHRlbmQgPSBqUXVlcnkuZXh0ZW5kOw0KCWpRdWVyeS5hdXRvZ3Jvdy5mbi5leHRlbmQoew0KCQlpbml0OiBmdW5jdGlvbigpIHsJCQkNCgkJCXZhciBzZWxmID0gdGhpczsJCQkNCgkJCXRoaXMudGV4dGFyZWEuY3NzKHtvdmVyZmxvdzogJ2hpZGRlbicsIGRpc3BsYXk6ICdibG9jayd9KTsNCgkJCXRoaXMudGV4dGFyZWEuYmluZCgnZm9jdXMnLCBmdW5jdGlvbigpIHsgc2VsZi5zdGFydEV4cGFuZCgpIH0gKS5iaW5kKCdibHVyJywgZnVuY3Rpb24oKSB7IHNlbGYuc3RvcEV4cGFuZCgpIH0pOw0KCQkJdGhpcy5jaGVja0V4cGFuZCgpOwkNCgkJfSwNCgkJc3RhcnRFeHBhbmQ6IGZ1bmN0aW9uKCkgewkJCQkNCgkJICB2YXIgc2VsZiA9IHRoaXM7DQoJCQl0aGlzLmludGVydmFsID0gd2luZG93LnNldEludGVydmFsKGZ1bmN0aW9uKCkge3NlbGYuY2hlY2tFeHBhbmQoKX0sIDQwMCk7DQoJCX0sDQoJCXN0b3BFeHBhbmQ6IGZ1bmN0aW9uKCkgew0KCQkJY2xlYXJJbnRlcnZhbCh0aGlzLmludGVydmFsKTsJDQoJCX0sDQoJCWNoZWNrRXhwYW5kOiBmdW5jdGlvbigpIHsNCgkJCWlmICh0aGlzLmR1bW15ID09IG51bGwpew0KCQkJCXRoaXMuZHVtbXkgPSBqUXVlcnkoJzxkaXY+PC9kaXY+Jyk7DQoJCQkJdGhpcy5kdW1teS5jc3Moew0KCQkJCQknZm9udC1zaXplJyAgOiB0aGlzLnRleHRhcmVhLmNzcygnZm9udC1zaXplJyksDQoJCQkJCSdmb250LWZhbWlseSc6IHRoaXMudGV4dGFyZWEuY3NzKCdmb250LWZhbWlseScpLA0KCQkJCQknd2lkdGgnICAgICAgOiB0aGlzLnRleHRhcmVhLmNzcygnd2lkdGgnKSwNCgkJCQkJJ3BhZGRpbmcnICAgIDogdGhpcy50ZXh0YXJlYS5jc3MoJ3BhZGRpbmcnKSwNCgkJCQkJJ2xpbmUtaGVpZ2h0JzogdGhpcy5saW5lX2hlaWdodCArICdweCcsDQoJCQkJCSdvdmVyZmxvdy14JyA6ICdoaWRkZW4nLA0KCQkJCQkncG9zaXRpb24nICAgOiAnYWJzb2x1dGUnLA0KCQkJCQkndG9wJyAgICAgICAgOiAwLA0KCQkJCQknbGVmdCcJCSA6IC05OTk5DQoJCQkJfSkuYXBwZW5kVG8oJ2JvZHknKTsNCgkJCX0NCgkJCXZhciBodG1sID0gdGhpcy50ZXh0YXJlYS52YWwoKS5yZXBsYWNlKC8oPHw+KS9nLCAnJyk7DQoJCQlpZiAoJC5icm93c2VyLm1zaWUpew0KCQkJCWh0bWwgPSBodG1sLnJlcGxhY2UoL1xuL2csICc8QlI+bmV3Jyk7DQoJCQl9ZWxzZXsNCgkJCQlodG1sID0gaHRtbC5yZXBsYWNlKC9cbi9nLCAnPGJyPm5ldycpOw0KCQkJfQ0KCQkJaWYgKHRoaXMuZHVtbXkuaHRtbCgpICE9IGh0bWwpew0KCQkJCXRoaXMuZHVtbXkuaHRtbChodG1sKTsNCgkJCQlpZiAodGhpcy5tYXhfaGVpZ2h0ID4gMCAmJiAodGhpcy5kdW1teS5oZWlnaHQoKSArIHRoaXMubGluZV9oZWlnaHQgPiB0aGlzLm1heF9oZWlnaHQpKXsNCgkJCQkJdGhpcy50ZXh0YXJlYS5jc3MoJ292ZXJmbG93LXknLCAnYXV0bycpOwkNCgkJCQl9DQoJCQkJZWxzZXsNCgkJCQkJdGhpcy50ZXh0YXJlYS5jc3MoJ292ZXJmbG93LXknLCAnaGlkZGVuJyk7DQoJCQkJCWlmICh0aGlzLnRleHRhcmVhLmhlaWdodCgpIDwgdGhpcy5kdW1teS5oZWlnaHQoKSArIHRoaXMubGluZV9oZWlnaHQgfHwgKHRoaXMuZHVtbXkuaGVpZ2h0KCkgPCB0aGlzLnRleHRhcmVhLmhlaWdodCgpKSl7CQ0KCQkJCQkJdGhpcy50ZXh0YXJlYS5hbmltYXRlKHtoZWlnaHQ6ICh0aGlzLmR1bW15LmhlaWdodCgpICsgdGhpcy5saW5lX2hlaWdodCkgKyAncHgnfSwgMTAwKTsJDQoJCQkJCX0NCgkJCQl9DQoJCQl9DQoJCX0NCgl9KTsNCn0pKGpRdWVyeSk7';
		echo base64_decode($js);
		exit();
	}

	function rah_autogrowing_textarea_save() {
		extract(doSlash(gpsa(array('id','name','min_height','height','line_height','max_height','active','page'))));
		if($id && safe_count('rah_autogrowing_textarea',"id='".$id."'") == 1) {
			safe_update(
				'rah_autogrowing_textarea',
				"name = '$name',
				posted=now(),
				min_height = '$min_height',
				height = '$height',
				line_height = '$line_height',
				max_height = '$max_height',
				active = '$active',
				page = '$page'",
				"id='".$id."'"
			);
			rah_autogrowing_textarea_form('Updated.');
		} else {
			safe_insert(
				'rah_autogrowing_textarea',
				"name = '$name',
				posted=now(),
				min_height = '$min_height',
				height = '$height',
				line_height = '$line_height',
				max_height = '$max_height',
				active = '$active',
				page = '$page'"
			);
			rah_autogrowing_textarea_list('Saved.');
		}
	}

	function rah_autogrowing_textarea_delete() {
		$selected = ps('delete');
		$i = 0;
		if(!is_array($selected)) $selected = explode(',',$selected);
		foreach($selected as $id) {
			safe_delete('rah_autogrowing_textarea',"id='".doSlash($id)."'");
			$i++;
		}
		rah_autogrowing_textarea_list((($i > 0) ? 'Deleted.' : ''));
	}