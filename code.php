<?php	##################
	#
	#	rah_autogrowing_textarea-plugin for Textpattern
	#	version 0.6
	#	by Jukka Svahn
	#	http://rahforum.biz
	#
	#	Copyright (C) 2011 Jukka Svahn <http://rahforum.biz>
	#	Licensed under GNU Genral Public License version 2
	#	http://www.gnu.org/licenses/gpl-2.0.html
	#
	###################

	if(@txpinterface == 'admin') {
		rah_autogrowing_textarea_install();
		add_privs('rah_autogrowing_textarea','1,2');
		add_privs('plugin_prefs.rah_autogrowing_textarea','1,2');
		register_tab('extensions','rah_autogrowing_textarea',gTxt('rah_autogrowing_textarea') == 'rah_autogrowing_textarea' ? 'Autogrowing Textarea' : gTxt('rah_autogrowing_textarea'));
		register_callback('rah_autogrowing_textarea','admin_side','head_end');
		register_callback('rah_autogrowing_textarea_head','admin_side','head_end');
		register_callback('rah_autogrowing_textarea_page','rah_autogrowing_textarea');
		register_callback('rah_autogrowing_textarea_prefs','plugin_prefs.rah_autogrowing_textarea');
		register_callback('rah_autogrowing_textarea_install','plugin_lifecycle.rah_autogrowing_textarea');
	}

/**
	Does installing and uninstalling.
	@param $event string The admin-side event.
	@param $step string The admin-side / plugin-lifecycle step.
*/

	function rah_autogrowing_textarea_install($event='',$step='') {
		
		if($step == 'deleted') {
		
			@safe_query(
				'DROP TABLE IF EXISTS '.safe_pfx('rah_autogrowing_textarea')
			);
			
			safe_delete(
				'txp_prefs',
				"name='rah_autogrowing_textarea_version'"
			);
			
			return;
		}
		
		global $textarray, $prefs;
		
		/*
			Make sure language strings are set
		*/
		
		foreach(
			array(
				'title' => 'Autogrowing Textarea',
				'main' => 'Main',
				'create_new' => 'Create a new definition',
				'documentation' => 'Documentation',
				'name' => 'Name',
				'posted' => 'Updated',
				'status' => 'Active?',
				'page' => 'Page',
				'id' => '#ID',
				'yes' => 'Yes',
				'no' => 'No',
				'no_items' => 'No items created yet.',
				'with_selected' => 'With selected...',
				'delete' => 'Delete',
				'save' => 'Save',
				'unknown_item' => 'Unknown item.',
				'required_fields' => 'All fields are required.',
				'updated' => 'Item updated.',
				'saved' => 'Item saved.',
				'removed' => 'Selected items removed.',
				'error_saving' => 'Database error occured while saving. Please try again.',
				'error_deleting' => 'Database error occured while removing items. Please try again.',
				'id_of_textarea' => 'HTML #ID of the textarea',
				'styling' => 'Styling preferences and CSS rules',
				'active' => 'Active',
				'disabled' => 'Disabled',
				'select_status' => 'State, actve?',
				'select_something' => 'Nothing selected',
			) as $string => $translation
		)
			if(!isset($textarray['rah_autogrowing_textarea_'.$string]))
				$textarray['rah_autogrowing_textarea_'.$string] = $translation;
		
		$version = '0.6';
		
		$current = isset($prefs['rah_autogrowing_textarea_version']) ?
			$prefs['rah_autogrowing_textarea_version'] : 'base';
		
		if($current == $version)
			return;
		
		/*
			Store's the definitions.
			
			* id: The primary key. Used for updating and deleting.
			* posted: Last update time.
			* name: Field's ID.
			* min-height: HTML field's min height.
			* height: HTML field's height.
			* line_height: Line-height, it's line-height.
			* max_height: Maximum height of the field.
			* active: If the definition is active or not.
			* page: Admin-side page (event).
		*/
		
		safe_query(
			"CREATE TABLE IF NOT EXISTS ".safe_pfx('rah_autogrowing_textarea')." (
				`id` int(11) NOT NULL auto_increment,
				`posted` datetime NOT NULL default '0000-00-00 00:00:00',
				`name` varchar(255) NOT NULL,
				`min_height` varchar(12) NOT NULL,
				`height` varchar(12) NOT NULL,
				`line_height` varchar(12) NOT NULL,
				`max_height` varchar(12) NOT NULL,
				`active` varchar(3) NOT NULL,
				`page` varchar(255) NOT NULL,
				PRIMARY KEY(`id`)
			) PACK_KEYS=1 AUTO_INCREMENT=1 CHARSET=utf8"
		);
		
		/*
			Inserts the default rows
		*/
		
		if($current != 'base' && safe_count('rah_autogrowing_textarea',"name='excerpt'") == 0) {
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
		if($current != 'base' && safe_count('rah_autogrowing_textarea',"name='body'") == 0) {
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
		
		/*
			Set version
		*/
		
		set_pref('rah_autogrowing_textarea_version',$version,'rah_autext',2,'',0);
		$prefs['rah_autogrowing_textarea_version'] = $version;
	}

/**
	Adds the required scripts to the <head>
*/

	function rah_autogrowing_textarea() {
		
		global $event;
		
		$rs = 
			safe_rows(
				'name,min_height,height,line_height,max_height',
				'rah_autogrowing_textarea',
				"page='".doSlash($event)."' and active='Yes' order by id asc"
			);
		
		/*
			Nothing to do.
		*/
		
		if(!$rs)
			return;
		
		$css = $js = array();
		
		foreach($rs as $a){
			extract($a);
			
			$js[] = 'textarea#'.$name;
			$css[] = 
				'			textarea#'.$name.' {'.n.
				'				min-height: '.$min_height.'px;'.n.
				'				height: '.$height.'px;'.n.
				'				line-height: '.$line_height.'px;'.n.
				'				max-height: '.$max_height.'px;'.n.
				'			}';
		}
		
		$js = implode(',',$js);
		$css = implode(n,$css);
		$hu = hu;
		$jquery = rah_autogrowing_textarea_js();
		
		echo 
			<<<EOF

				<script type="text/javascript">
					<!--
					{$jquery}
					-->
				</script>
				<script type="text/javascript">
					<!--
					$(document).ready (function() {
						$('{$js}').autogrow();
					});
					-->
				</script>
				<style type="text/css">
					{$css}
				</style>
EOF;
		
	}

/**
	Delivers panes
*/

	function rah_autogrowing_textarea_page() {
		require_privs('rah_autogrowing_textarea');
		global $step;
		
		$steps = 
			array(
				'list' => false,
				'edit' => false,
				'save' => true,
				'delete' => true
			);
		
		if(!$step || !bouncer($step, $steps))
			$step = 'list';
		
		$func = 'rah_autogrowing_textarea_' . $step;
		$func();
	}

/**
	The main pane; the listing
	@param $message string Message shown at the header. Takes language strings.
*/

	function rah_autogrowing_textarea_list($message='') {
		global $event;
		
		$events = rah_autogrowing_textarea_events();
		
		$out[] =
				
				'	<table cellspacing="0" cellpadding="0" id="list">'.n.
				'		<thead>'.n.
				'			<tr>'.n.
				'				<th>'.gTxt('rah_autogrowing_textarea_name').'</th>'.n.
				'				<th>'.gTxt('rah_autogrowing_textarea_posted').'</th>'.n.
				'				<th>'.gTxt('rah_autogrowing_textarea_status').'</th>'.n.
				'				<th>'.gTxt('rah_autogrowing_textarea_page').'</th>'.n.
				'				<th>'.gTxt('rah_autogrowing_textarea_id').'</th>'.n.
				'				<th>&#160;</th>'.n.
				'			</tr>'.n.
				'		</thead>'.n.
				'		<tbody>'.n;

		$rs = 
		
			safe_rows(
				'id,name,posted,page,active',
				'rah_autogrowing_textarea',
				'1=1 order by name asc, id asc'
			);
		
		
		if($rs){
			foreach($rs as $a){
				extract($a);
				$out[] = 
					'			<tr>'.n.
					'				<td><a href="?event='.$event.'&amp;step=edit&amp;id='.$id.'">'.htmlspecialchars($name).'</a></td>'.n.
					'				<td>'.safe_strftime('%b %d %Y %H:%M',strtotime($posted)).'</td>'.n.
					'				<td>'.($active == 'Yes' ? gTxt('rah_autogrowing_textarea_yes') : gTxt('rah_autogrowing_textarea_no')).'</td>'.n.
					'				<td><a href="?event='.htmlspecialchars($page).'">'.(isset($events[$page]) && !empty($events[$page]) ? $events[$page] : htmlspecialchars($page)).'</a></td>'.n.
					'				<td><code>textarea#'.htmlspecialchars($name).'</code></td>'.n.
					'				<td><input type="checkbox" name="selected[]" value="'.$id.'" /></td>'.n.
					'			</tr>'.n;
			}
		} else $out[] = 
					'			<tr>'.n.
					'				<td colspan="6">'.gTxt('rah_autogrowing_textarea_no_items').'</td>'.n.
					'			</tr>'.n;
		
		$out[] =
			'		</tbody>'.n.
			'	</table>'.n.
			'	<p id="rah_autogrowing_textarea_step" class="rah_ui_step">'.n.
			'		<select name="step">'.n.
			'			<option value="">'.gTxt('rah_autogrowing_textarea_with_selected').'</option>'.n.
			'			<option value="delete">'.gTxt('rah_autogrowing_textarea_delete').'</option>'.n.
			'		</select>'.n.
			'		<input type="submit" class="smallerbox" value="'.gTxt('go').'" />'.n.
			'	</p>';
		
		rah_autogrowing_textarea_header($out,gTxt('rah_autogrowing_textarea_title'),$message);
		
	}

/**
	The editor
	@param $message string Message shown in the header. Takes language strings.
*/

	function rah_autogrowing_textarea_edit($message='') {
		global $event;
		
		extract(
			psa(
				array(
					'id',
					'name',
					'min_height',
					'height',
					'line_height',
					'max_height',
					'active',
					'page'
				)
			)
		);
		
		if(($id = gps('id')) && $id && !ps('id')) {
			$rs = 
				safe_row(
					'*',
					'rah_autogrowing_textarea',
					"id='".doSlash($id)."' LIMIT 0, 1"
				);
			
			if(!$rs) {
				rah_autogrowing_textarea_list('rah_autogrowing_textarea_unknown_item');
				return;
			}
			
			extract($rs);
		}
		
		$events = rah_autogrowing_textarea_events();
		
		$out[] = 
				
				'	<input type="hidden" name="step" value="save" />'.n.
				
				($id ? '	<input type="hidden" name="id" value="'.$id.'" />'.n : '').
				
				'	<h3>Pinpoint target field</h3>'.n.
				
				'	<p>'.n.
				'		<label>'.n.
				'			'.gTxt('rah_autogrowing_textarea_id_of_textarea').'<br />'.n.
				'			<input class="edit" type="text" name="name" value="'.htmlspecialchars($name).'" />'.n.
				'		</label>'.n.
				'	</p>'.n.
				
				'	<p>'.n.
				'		<label>'.n.
				'			'.gTxt('rah_autogrowing_textarea_page').'<br />'.n;
				
		if($events !== false && (empty($page) || isset($events[$page]))) {
				
			$out[] =
				
				'			<select name="page" class="rah_autogrowing_textarea_select">'.n.
				'				<option value="">&#160;</option>'.n;
					
			foreach($events as $key => $val)
				$out[] = 
					'				<option value="'.htmlspecialchars($key).'"'.(($page == $key) ? ' selected="selected"' : '').'>'.($val ? $val : $key).'</option>';
						
			$out[] =
				'			</select>'.n;
		} else	
			$out[] =
				'			<input class="edit" type="text" name="page" value="'.htmlspecialchars($page).'" /> px'.n;

		$out[] =
				'		</label>'.n.
				'	</p>'.n.
				
				'	<h3>'.gTxt('rah_autogrowing_textarea_styling').'</h3>'.n.
				
				'	<p id="rah_autogrowing_textarea_fields">'.n.
				'		<label>'.n.
				'			Min-height<br />'.n.
				'			<input class="edit" type="text" name="min_height" value="'.htmlspecialchars($min_height).'" /> px'.n.
				'		</label>'.n.
				
				'		<label>'.n.
				'			Max-height<br />'.n.
				'			<input class="edit" type="text" name="max_height" value="'.htmlspecialchars($max_height).'" /> px'.n.
				'		</label>'.n.
				
				'		<label>'.n.
				'			Line-height<br />'.n.
				'			<input class="edit" type="text" name="line_height" value="'.htmlspecialchars($line_height).'" /> px'.n.
				'		</label>'.n.
				
				'		<label>'.n.
				'			Height<br />'.n.
				'			<input class="edit" type="text" name="height" value="'.htmlspecialchars($height).'" /> px'.n.
				'		</label>'.n.
				
				'	</p>'.n.
				
				'	<h3>'.gTxt('rah_autogrowing_textarea_select_status').'</h3>'.n.
				
				'	<p>'.n.
				
				'		<label>'.n.
				'			<input type="radio" name="active" value="Yes"'.($active != 'No' ? ' checked="checked"' : '').' /> '.n.
				'			'.gTxt('rah_autogrowing_textarea_active').n.
				'		</label>'.n.
				'		<label>'.n.
				'			<input type="radio" name="active" value="No"'.($active == 'No' ? ' checked="checked"' : '').' /> '.n.
				'			'.gTxt('rah_autogrowing_textarea_disabled').n.
				'		</label>'.n.
					
				'	</p>'.n.
				
				'	<p><input type="submit" value="'.gTxt('rah_autogrowing_textarea_save').'" class="publish" /></p>';
			
		
		rah_autogrowing_textarea_header($out,gTxt('rah_autogrowing_textarea_title'),$message);
	}

/**
	Saves and updates items
*/

	function rah_autogrowing_textarea_save() {
		
		$fields = 
			array(
				'id',
				'name',
				'min_height',
				'height',
				'line_height',
				'max_height',
				'active',
				'page'
			);
		
		
		
		extract(
			doSlash(
				psa(
					$fields
				)
			)
		);
		
		foreach($fields as $field) {
			
			if($field != 'id' && !trim($$field)) {
				rah_autogrowing_textarea_edit('rah_autogrowing_textarea_required_fields');
				return;
			}
			
		}
		
		if($id) {
			
			if(
				!safe_row(
					'id',
					'rah_autogrowing_textarea',
					"id='".$id."' LIMIT 0, 1"
				)
			) {
				rah_autogrowing_textarea_list('rah_autogrowing_textarea_unknown_item');
				return;
			}
			
			if(
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
				) == false
			) {
				rah_autogrowing_textarea_edit('rah_autogrowing_textarea_error_saving');
				return;
			}
			
			rah_autogrowing_textarea_edit('rah_autogrowing_textarea_updated');
			return;
		}
		
		if(
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
			) == false
		) {
			rah_autogrowing_textarea_edit('rah_autogrowing_textarea_error_saving');
			return;	
		}
		
		rah_autogrowing_textarea_list('rah_autogrowing_textarea_saved');
		
	}

/**
	Removes selected items
*/

	function rah_autogrowing_textarea_delete() {
		$selected = ps('selected');
	
		if(!is_array($selected) || empty($selected)) {
			rah_autogrowing_textarea_list('rah_autogrowing_textarea_select_something');
			return;
		}
		
		foreach($selected as $id)
			$ids[] = "'".doSlash($id)."'";
		
		if(
			safe_delete(
				'rah_autogrowing_textarea',
				'id in('.implode(',',$ids).')'
			) == false
		) {
			rah_autogrowing_textarea_list('rah_autogrowing_textarea_error_deleting');
			return;	
		}
		
		rah_autogrowing_textarea_list('rah_autogrowing_textarea_removed');
	}

/**
	Pagetop, outputs the pane markup and sets page title.
	@param $out mixed The page HTML markup. Accepts strings and arrays.
	@param $pagetop string Shown in the browser's title.
	@param $message string Message shown in the header. Takes language strings.
*/

	function rah_autogrowing_textarea_header($out,$pagetop,$message) {
		
		global $event;
		
		$message = $message ? gTxt($message) : '';
		
		pagetop($pagetop,$message);
		
		if(is_array($out))
			$out = implode('',$out);
		
		echo 
			n.
			'<form method="post" action="index.php" id="rah_autogrowing_textarea_container" class="rah_ui_container">'.n.
			'	<input type="hidden" name="event" value="'.$event.'" />'.n.
			'	<input type="hidden" name="_txp_token" value="'.form_token().'" />'.n.
			'	<p class="rah_ui_nav">'.
				'<span class="rah_ui_sep">&#187;</span> <a href="?event='.$event.'">'.gTxt('rah_autogrowing_textarea_main').'</a> '.
				'<span class="rah_ui_sep">&#187;</span> <strong><a href="?event='.$event.'&amp;step=edit">'.gTxt('rah_autogrowing_textarea_create_new').'</a></strong> '.
				'<span class="rah_ui_sep">&#187;</span> <a href="?event=plugin&amp;step=plugin_help&amp;name=rah_autogrowing_textarea">'.gTxt('rah_autogrowing_textarea_documentation').'</a>'.
			'</p>'.n.
			
			$out.n.
			
			'</form>'.n;
		
	}
	
/**
	The styles and JavaScript for the preferences pane
*/

	function rah_autogrowing_textarea_head() {
		global $event;
		
		if($event != 'rah_autogrowing_textarea')
			return;
		
		$msg = gTxt('are_you_sure');
			
		echo <<<EOF
			<script type="text/javascript">
				<!--
				function rah_autogrowing_textarea_stepper() {
					if($('#rah_autogrowing_textarea_step').length < 1)
						return;
					
					$('#rah_autogrowing_textarea_step .smallerbox').hide();

					if($('#rah_autogrowing_textarea_container input[type=checkbox]:checked').val() == null)
						$('#rah_autogrowing_textarea_step').hide();

					/*
						Reset the value
					*/

					$('#rah_autogrowing_textarea_container select[name="step"]').val('');

					/*
						Every time something is checked, check if
						the dropdown should be shown
					*/

					$('#rah_autogrowing_textarea_container input[type=checkbox], #rah_autogrowing_textarea_container td').click(
						function(){
							$('#rah_autogrowing_textarea_container select[name="step"]').val('');
							if($('table#list input[type=checkbox]:checked').val() != null)	
								$('#rah_autogrowing_textarea_step').slideDown();
							else
								$('#rah_autogrowing_textarea_step').slideUp();
						}
					);

					/*
						If value is changed, send the form
					*/

					$('#rah_autogrowing_textarea_container select[name="step"]').change(
						function(){
							$('#rah_autogrowing_textarea_container').submit();
						}
					);

					/*
						Verify if the sent is allowed
					*/

					$('form#rah_autogrowing_textarea_container').submit(
						function() {
							if(!verify('{$msg}')) {
								$('#rah_autogrowing_textarea_container select[name="step"]').val('');
								return false;
							}
						}
					);
				}
			
				$(document).ready(function(){
					rah_autogrowing_textarea_stepper();
				});
				-->
			</script>
			<style type="text/css">
				#rah_autogrowing_textarea_container {
					width: 950px;
					margin: 0 auto;
				}
				#rah_autogrowing_textarea_container table {
					width: 100%;
				}
				#rah_autogrowing_textarea_container #rah_autogrowing_textarea_step {
					text-align: right;
				}
				#rah_autogrowing_textarea_container input.edit {
					width: 940px;
				}
				#rah_autogrowing_textarea_container .rah_autogrowing_textarea_select {
					width: 640px;
				}
				#rah_autogrowing_textarea_fields {
					overflow: hidden;
				}
				#rah_autogrowing_textarea_container #rah_autogrowing_textarea_fields input.edit {
					width: 100px;
				}
				#rah_autogrowing_textarea_fields label {
					float: left;
					margin: 0 15px 0 0;
				}
			</style>	
EOF;
	}

/**
	Lists available events
*/

	function rah_autogrowing_textarea_events() {
	
		/*
			Someone called us before areas() was defined.
			Fallback to the advanced editor.
		*/
		
		if(!function_exists('areas') || !is_array(areas()))
			return false;
		
		$out = array();
		
		foreach(areas() as $key => $group)
			foreach ($group as $title => $name) 
				$out[$name] = $title;
		
		/*
			These events are all over the place.
			Let's do some cleaning.
		*/
		
		$out = array_unique($out);
		asort($out);
		
		return $out;
	}

/**
	Stores the jQuery plugin as base64 encoded string
*/

	function rah_autogrowing_textarea_js() {
		
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
		return base64_decode($js);
	}

/**
	Redirect to the admin-side interface
*/

	function rah_autogrowing_textarea_prefs() {
		header('Location: ?event=rah_autogrowing_textarea');
		echo 
			'<p>'.n.
			'	<a href="?event=rah_autogrowing_textarea">'.gTxt('continue').'</a>'.n.
			'</p>';
	}
?>