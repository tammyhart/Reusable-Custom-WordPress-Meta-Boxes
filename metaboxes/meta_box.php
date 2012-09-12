<?php

function meta_box_find_field_type( $needle, $haystack ) {
    foreach ( $haystack as $item )
        if ( $item['type'] == $needle )
            return true;
    return false;
}

class custom_add_meta_box {
	
	var $id; // string meta box id
	var $title; // string title
	var $fields; // array fields
	var $page; // string|array post type to add meta box to
	var $js; // bool including javascript or not
	
    public function __construct( $id, $title, $fields, $page, $js ) {
		$this->id = $id;
		$this->title = $title;
		$this->fields = $fields;
		$this->page = $page;
		$this->js = $js;
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_head',  array( $this, 'admin_head' ) );
		add_action( 'admin_menu', array( $this, 'add_box' ) );
		add_action( 'save_post',  array( $this, 'save_box' ));
    }
	
	function admin_enqueue_scripts() {
		// js
		if ( tcnmy_find_field_type( 'date', $this->fields ) )
			wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery', 'jquery-ui-core' ) );
		if ( tcnmy_find_field_type( 'slider', $this->fields ) )
			wp_enqueue_script( 'jquery-ui-slider', array( 'jquery', 'jquery-ui-core' ) );
		if ( tcnmy_find_field_type( 'image', $this->fields ) )
			wp_enqueue_script( 'meta_box', get_template_directory_uri() . '/metaboxes/js/scripts.js', array( 'jquery' ) );
		// css
		wp_register_style( 'jqueryui', get_template_directory_uri() . '/metaboxes/css/jqueryui.css' );
		$deps = tcnmy_find_field_type( 'date', $this->fields ) || tcnmy_find_field_type( 'slider', $this->fields ) ? array ( 'jqueryui' ) : null;
		wp_enqueue_style( 'meta_box', get_template_directory_uri() . '/metaboxes/css/meta_box.css', $deps);
	}
	
	// scripts
	function admin_head() {
		global $post, $post_type;
		
		if ( $post_type == $this->page && $this->js == true ) : 
		
			echo '<script type="text/javascript">
						jQuery(function() {';
			
			foreach ( $this->fields as $field ) {
				// date
				if( $field['type'] == 'date' )
					echo 'jQuery("#' . $field['id'] . '").datepicker({
							dateFormat: \'yy-mm-dd\'
						});';
				// slider
				if ( $field['type'] == 'slider' ) {
					$value = get_post_meta( $post->ID, $field['id'], true );
					if ( $value == '' ) $value = $field['min'];
					echo '
							jQuery( "#' . $field['id'] . '-slider" ).slider({
								value: ' . $value . ',
								min: ' . $field['min'] . ',
								max: ' . $field['max'] . ',
								step: ' . $field['step'] . ',
								slide: function( event, ui ) {
									jQuery( "#' . $field['id'] . '" ).val( ui.value );
								}
							});';
				}
			}
			
			echo '});
				</script>';
		
		endif;
	}
	
	function add_box() {
		add_meta_box( $this->id, $this->title, array( $this, 'meta_box_callback' ), $this->page, 'normal', 'high');
	}
	
	function meta_box_callback() {
		global $post;
		// Use nonce for verification
		echo '<input type="hidden" name="' . $this->page . '_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__) ) . '" />';
		
		// Begin the field table and loop
		echo '<table class="form-table meta_box">';
		foreach ( $this->fields as $field) {
			
			// get data for this field
			extract( $field );
			if ( !empty( $desc ) )
				$desc = '<span class="description">' . $desc . '</span>';
				
			// get value of this field if it exists for this post
			$meta = get_post_meta( $post->ID, $id, true);
			
			// begin a table row with
			echo '<tr>
					<th><label for="' . $id . '">' . $label . '</label></th>
					<td>';
					switch( $type ) {
						// text
						case 'text':
							echo '<input type="text" name="' . $id . '" id="' . $id . '" value="' . esc_attr( $meta ) . '" size="30" />
									<br />' . $desc;
						break;
						// textarea
						case 'textarea':
							echo '<textarea name="' . $id . '" id="' . $id . '" cols="60" rows="4">' . esc_attr( $meta ) . '</textarea>
									<br />' . $desc;
						break;
						// checkbox
						case 'checkbox':
							echo '<input type="checkbox" name="' . $id . '" id="' . $id . '" ' . checked( esc_attr( $meta ), true, false ) . ' value="1" />
									<label for="' . $id . '">' . $desc . '</label>';
						break;
						// select
						case 'select':
							echo '<select name="' . $id . '" id="' . $id . '">';
							foreach ( $options as $option )
								echo '<option' . selected( esc_attr( $meta ), $option['value'], false ) . ' value="' . $option['value'] . '">' . $option['label'] . '</option>';
							echo '</select><br />' . $desc;
						break;
						// radio
						case 'radio':
							foreach ( $options as $option )
								echo '<input type="radio" name="' . $id . '" id="' . $id . '-' . $option['value'] . '" value="' . $option['value'] . '" ' . checked( esc_attr( $meta ), $option['value'], false ) . ' />
										<label for="' . $id . '-' . $option['value'] . '">' . $option['label'] . '</label><br />';
							echo '' . $desc;
						break;
						// checkbox_group
						case 'checkbox_group':
							foreach ( $options as $option )
								echo '<input type="checkbox" value="' . $option['value'] . '" name="' . $id . '[]" id="' . $id . '-' . $option['value'] . '"' , is_array( $meta ) && in_array( $option['value'], $meta ) ? ' checked="checked"' : '' , ' /> 
										<label for="' . $id . '-' . $option['value'] . '">' . $option['label'] . '</label><br />';
							echo '' . $desc;
						break;
						// tax_select
						case 'tax_select':
							echo '<select name="' . $id . '" id="' . $id . '">
									<option value="">Select One</option>'; // Select One
							$terms = get_terms( $id, 'get=all' );
							$selected = wp_get_object_terms( $post->ID, $id );
							foreach ( $terms as $term ) 
									echo '<option value="' . $term->slug . '"' . selected( $selected[0]->slug, $term->slug, false ) . '>' . $term->name . '</option>'; 
							$taxonomy = get_taxonomy( $id);
							echo '</select> &nbsp;<span class="description"><a href="' . get_bloginfo( 'url' ) . '/wp-admin/edit-tags.php?taxonomy=' . $id . '&post_type=' . $this->page . '">Manage ' . $taxonomy->label . '</a></span>
								<br />' . $desc;
						break;
						// post_list
						case 'post_list':
							echo '<select name="' . $id . '" id="' . $id . '">
									<option value="">Select One</option>'; // Select One
							$posts = get_posts( array( 'post-type' => $post_type, 'posts_per_page' => 9999 ) );
							foreach ( $posts as $item ) 
									echo '<option value="' . $item->ID . '"' . selected( intval( $meta ), $item->ID, false ) . '>' . $item->post_title . '</option>';
							echo '<br />' . $desc;
						break;
						// date
						case 'date':
							echo '<input type="text" class="datepicker" name="' . $id . '" id="' . $id . '" value="' . esc_attr( $meta ) . '" size="30" />
									<br />' . $desc;
						break;
						// slider
						case 'slider':
						$value = $meta != '' ? intval( $meta ) : '0';
							echo '<div id="' . $id . '-slider"></div>
									<input type="text" name="' . $id . '" id="' . $id . '" value="' . $value . '" size="5" />
									<br />' . $desc;
						break;
						// image
						case 'image':
							$image = get_template_directory_uri() . '/metaboxes/images/image.png';	
							echo '<span class="meta_box_default_image" style="display:none">' . $image . '</span>';
							if ( $meta ) {
								$image = wp_get_attachment_image_src( intval( $meta ), 'medium' );
								$image = $image[0];
							}				
							echo	'<input name="' . $id . '" type="hidden" class="meta_box_upload_image" value="' . intval( $meta ) . '" />
										<img src="' . $image . '" class="meta_box_preview_image" alt="" /><br />
											<input class="meta_box_upload_image_button button" type="button" rel="' . $post->ID . '" value="Choose Image" />
											<small>&nbsp;<a href="#" class="meta_box_clear_image_button">Remove Image</a></small>
											<br clear="all" />' . $desc;
						break;
						// repeatable
						case 'repeatable':
							echo '<a class="meta_box_repeatable_add button" href="#">+</a>
									<ul id="' . $field['id'] . '-repeatable" class="meta_box_repeatable">';
							$i = 0;
							if ( $meta ) {
								foreach( $meta as $row ) {
									echo '<li><span class="sort handle">|||</span>
												<input type="text" name="' . $field['id'] . '[' . $i . ']" id="' . $field['id'] . '" value="' . esc_attr( $row ) . '" size="30" />
												<a class="meta_box_repeatable_remove button" href="#">-</a></li>';
									$i++;
								}
							} else {
								echo '<li><span class="sort handle">|||</span>
											<input type="text" name="' . $field['id'] . '[' . $i . ']" id="' . $field['id'] . '" value="" size="30" />
											<a class="meta_box_repeatable_remove button" href="#">-</a></li>';
							}
							echo '</ul>
								<span class="description">' . $field['desc'] . '</span>';
						break;
					} //end switch
			echo '</td></tr>';
		} // end foreach
		echo '</table>'; // end table
	}
	
	// Save the Data
	function save_box( $post_id ) {
		global $post, $post_type;
		
		// verify nonce
		if ( ! ( $post_type == $this->page && @wp_verify_nonce( $_POST[$this->page . '_meta_box_nonce'],  basename( __FILE__ ) ) ) )
			return $post_id;
		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		// check permissions
		if ( !current_user_can( 'edit_page', $post_id ) )
			return $post_id;
		
		// loop through fields and save the data
		foreach ( $this->fields as $field ) {
			if( $field['type'] == 'tax_select' ) {
				// save taxonomies
				if ( isset( $_POST[$field['id']] ) )
					$term = $_POST[$field['id']];
				wp_set_object_terms( $post_id, $term, $field['id'] );
			}
			else {
				// save the rest
				$old = get_post_meta( $post_id, $field['id'], true );
				if ( isset( $_POST[$field['id']] ) )
					$new = $_POST[$field['id']];
				if ( $new && $new != $old ) {
					if ( is_array( $new ) ) {
						foreach ( $new as &$item ) {
							$item = esc_attr( $item );
						}
						unset( $item );
					} else {
						$new = esc_attr( $new );
					}
					update_post_meta( $post_id, $field['id'], $new );
				} elseif ( '' == $new && $old ) {
					delete_post_meta( $post_id, $field['id'], $old );
				}
			}
		} // end foreach
	}
}

?>