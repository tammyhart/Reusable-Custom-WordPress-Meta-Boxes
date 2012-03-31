<?php

add_action('admin_enqueue_scripts', 'thd_admin_enqueue_scripts');
function thd_admin_enqueue_scripts() {
	// js
	wp_enqueue_script('jquery-ui-core', array('jquery') );
	wp_enqueue_script('jquery-ui-datepicker', array('jquery','jquery-ui-core'));
	wp_enqueue_script('jquery-ui-slider', array('jquery','jquery-ui-core'));
	wp_enqueue_script('back', get_bloginfo('template_directory').'/js/back.js', array('jquery'));
	// css
	wp_enqueue_style('admincss', get_bloginfo('template_directory').'/css/back.css');
	wp_enqueue_style('jqueryui', get_bloginfo('template_directory').'/css/jqueryui.css');
}

// script head
function thd_meta_box_admin_head($fields, $page) {
	global $post, $post_type;
	
	if ($post_type == $page) : 
	
		$output = '<script type="text/javascript">
					jQuery(function() {';
		
		foreach ($fields as $field) { // loop through the fields looking for certain types
			// date
			if($field['type'] == 'date')
				$output .= 'jQuery(".datepicker").datepicker();';
			// slider
			if ($field['type'] == 'slider') {
				$value = get_post_meta($post->ID, $field['id'], true);
				if ($value == '') $value = $field['min'];
				$output .= '
						jQuery( "#'.$field['id'].'-slider" ).slider({
							value: '.$value.',
							min: '.$field['min'].',
							max: '.$field['max'].',
							step: '.$field['step'].',
							slide: function( event, ui ) {
								jQuery( "#'.$field['id'].'" ).val( ui.value );
							}
						});';
			}
		}
		
		$output .= '});
			</script>';
			
		return $output;
	
	endif;
}

// The Callback
function thd_meta_box_callback($fields, $page) {
	global $post;
	// Use nonce for verification
	echo '<input type="hidden" name="'.$page.'_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
	
	// Begin the field table and loop
	echo '<table class="form-table sil-metabox">';
	foreach ($fields as $field) {
		// get value of this field if it exists for this post
		if ($field['label'])	$label 		= $field['label'];
		if ($field['desc']) 	$desc 		= '<span class="description">'.$field['desc'].'</span>';
		if ($field['id']) 		$id 		= $field['id'];
		if ($field['type']) 	$type 		= $field['type'];
		if ($field['value']) 	$value 		= $field['value'];
		if ($field['options']) 	$options	= $field['options'];
		
        $meta 	= get_post_meta($post->ID, $id, true);
		// begin a table row with
		echo '<tr>
				<th><label for="'.$id.'">'.$label.'</label></th>
				<td>';
				switch($field['type']) {
					// text
					case 'text':
						echo '<input type="text" name="'.$id.'" id="'.$id.'" value="'.$meta.'" size="30" />
								<br />'.$desc;
					break;
					// textarea
					case 'textarea':
						echo '<textarea name="'.$id.'" id="'.$id.'" cols="60" rows="4">'.$meta.'</textarea>
								<br />'.$desc;
					break;
					// checkbox
					case 'checkbox':
						echo '<input type="checkbox" name="'.$id.'" id="'.$id.'" ',$meta ? ' checked="checked"' : '','/>
								<label for="'.$id.'">'.$desc.'</label>';
					break;
					// select
					case 'select':
						echo '<select name="'.$id.'" id="'.$id.'">';
						foreach ($options as $option) {
							echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
						}
						echo '</select><br />'.$desc;
					break;
					// radio
					case 'radio':
						foreach ( $options as $option ) {
							echo '<input type="radio" name="'.$id.'" id="'.$option['value'].'" value="'.$option['value'].'" ',$meta == $option['value'] ? ' checked="checked"' : '',' />
									<label for="'.$option['value'].'">'.$option['label'].'</label><br />';
						}
						echo ''.$desc;
					break;
					// checkbox_group
					case 'checkbox_group':
						foreach ($options as $option) {
							echo '<input type="checkbox" value="'.$option['value'].'" name="'.$id.'[]" id="'.$option['value'].'"',$meta && in_array($option['value'], $meta) ? ' checked="checked"' : '',' /> 
									<label for="'.$option['value'].'">'.$option['label'].'</label><br />';
						}
						echo ''.$desc;
					break;
					// tax_select
					case 'tax_select':
						echo '<select name="'.$id.'" id="'.$id.'">
								<option value="">Select One</option>'; // Select One
						$terms = get_terms($id, 'get=all');
						$selected = wp_get_object_terms($post->ID, $id);
						foreach ($terms as $term) {
							if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug)) 
								echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>'; 
							else
								echo '<option value="'.$term->slug.'">'.$term->name.'</option>'; 
						}
						$taxonomy = get_taxonomy($id);
						echo '</select> &nbsp;<span class="description"><a href="'.get_bloginfo('url').'/wp-admin/edit-tags.php?taxonomy='.$id.'&post_type='.$page.'">Manage '.$taxonomy->label.'</a></span>
							<br />'.$desc;
					break;
					// tax_checkboxes
					case 'tax_checkboxes':
						$terms = get_terms($id, 'get=all');
						$selected = wp_get_object_terms($post->ID, $id);
						foreach ($terms as $term) {
							if (!empty($selected) && !strcmp($term->slug, $selected[0]->slug))
								echo '<input type="checkbox" value="'.$term->slug.'" name="'.$id.'[]" id="'.$term->slug.'" checked="checked" /> 
										<label for="'.$term->slug.'">'.$term->name.'</label><br />';
							else
								echo '<input type="checkbox" value="'.$term->slug.'" name="'.$id.'[]" id="'.$term->slug.'" /> 
										<label for="'.$term->slug.'">'.$term->name.'</label><br />';
						}
						$taxonomy = get_taxonomy($id);
						echo '<span class="description">'.$field['desc'].' <a href="'.get_bloginfo('url').'/wp-admin/edit-tags.php?taxonomy='.$id.'&post_type='.$page.'">Manage '.$taxonomy->label.'</a></span>';
					break;
					// date
					case 'date':
						echo '<input type="text" class="datepicker" name="'.$id.'" id="'.$id.'" value="'.$meta.'" size="30" />
								<br />'.$desc;
					break;
					// slider
					case 'slider':
					$value = $meta != '' ? $meta : '0';
						echo '<div id="'.$id.'-slider"></div>
								<input type="text" name="'.$id.'" id="'.$id.'" value="'.$value.'" size="5" />
								<br />'.$desc;
					break;
					// image
					case 'image':
						$image = get_template_directory_uri().'/images/gravatar.png';	
						echo '<span class="thd_default_image" style="display:none">'.$image.'</span>';
						if ($meta) { $image = wp_get_attachment_image_src($meta, 'medium');	$image = $image[0]; }				
						echo	'<input name="'.$id.'" type="hidden" class="thd_upload_image" value="'.$meta.'" />
									<img src="'.$image.'" class="thd_preview_image" alt="" /><br />
										<input class="thd_upload_image_button button" type="button" rel="'.$post->ID.'" value="Choose Image" />
										<small>&nbsp;<a href="#" class="thd_clear_image_button">Remove Image</a></small>
										<br clear="all" />'.$desc;
					break;
				} //end switch
		echo '</td></tr>';
	} // end foreach
	echo '</table>'; // end table
}

// Save the Data
function thd_meta_box_save($post_id, $fields, $page) {
	
	// verify nonce
	if (!wp_verify_nonce($_POST[$page.'_meta_box_nonce'], basename(__FILE__))) 
		return $post_id;
	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $post_id;
	// check permissions
	if ($page != $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id))
			return $post_id;
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
	}
	
	// loop through fields and save the data
	foreach ($fields as $field) {
		if($field['type'] == 'tax_select') {
			// save taxonomies
			$term = $_POST[$field['id']];
			wp_set_object_terms( $post_id, $term, $field['id'] );
		}
		else {
			// save the rest
			$old = get_post_meta($post_id, $field['id'], true);
			$new = $_POST[$field['id']];
			if ($new && $new != $old) {
				update_post_meta($post_id, $field['id'], $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $field['id'], $old);
			}
		}
	} // end foreach
}

?>