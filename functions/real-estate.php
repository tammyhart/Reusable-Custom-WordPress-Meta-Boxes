<?php


// Register CPT
add_action( 'init', 'register_cpt_real_estate_listing' );

function register_cpt_real_estate_listing() {

    $labels = array( 
        'name' => 'Real Estate Listings',
        'singular_name' => 'Real Estate Listing',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Real Estate Listing',
        'edit_item' => 'Edit Real Estate Listing',
        'new_item' => 'New Real Estate Listing',
        'view_item' => 'View Real Estate Listing',
        'search_items' => 'Search Real Estate Listings',
        'not_found' => 'No real estate listings found',
        'not_found_in_trash' => 'No real estate listings found in Trash',
        'parent_item_colon' => 'Parent Real Estate Listing:',
        'menu_name' => 'Real Estate',
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => false,
        
        'supports' => array( 'title', 'editor', 'thumbnail' ),
        'taxonomies' => array( 'town' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        
        'show_in_nav_menus' => false,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => false,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'real-estate', $args );
}


/* Custom Meta Boxes
   ------------------------------------------------------------------------- */
function thd_re_meta_box_fields() {
	$prefix = 're_';

	$fields = array(
		array(
			'label'	=> 'Town',
			'desc'	=> 'What town is this listing in or nearby?',
			'id'		=> 'town',
			'type'	=> 'tax_select'
		),
		array(
			'label'	=> 'Address',
			'desc'	=> 'Street, town, state, and zip code.',
			'id'		=> $prefix.'address',
			'type'	=> 'textarea'
		),
		array(
			'label'	=> 'Size',
			'desc'	=> 'Square footage.',
			'id'		=> $prefix.'size',
			'type' 	=> 'text'
		),
		array(
			'label' => 'Bedrooms',
			'desc'	=> '',
			'id'		=> $prefix.'beds',
			'type'	=> 'slider',
			'min'		=> '1',
			'max'		=> '8',
			'step'	=> '1'
		),
		array(
			'label'	=> 'Bathrooms',
			'desc'	=> '',
			'id'		=> $prefix.'baths',
			'type'	=> 'slider',
			'min'		=> '0',
			'max'		=> '5',
			'step'	=> '.5'
		),
		array(
			'label'	=> 'Acreage',
			'desc'	=> 'Lot size',
			'id'		=> $prefix.'acres',
			'type'	=> 'text'
		),
		array(
			'label'	=> 'Price',
			'desc'	=> '',
			'id'		=> $prefix.'price',
			'type'	=> 'text',
			'value'	=> '$'
		),
		array(
			'label'	=> 'Contact Name',
			'desc'	=> '',
			'id'		=> $prefix.'name',
			'type'	=> 'text'
		),
		array(
			'label'	=> 'Contact Number',
			'desc'	=> '',
			'id'		=> $prefix.'phone',
			'type'	=> 'text'
		),
		array(
			'label'	=> 'Contact Email',
			'desc'	=> '',
			'id'		=> $prefix.'email',
			'type'	=> 'text'
		)
	);
	
	return $fields;
}

// Header JS
add_action('admin_head', 'thd_re_listing_header_js');
function thd_re_listing_header_js() {
	echo thd_meta_box_admin_head(thd_re_meta_box_fields(), 'real-estate');
}

// Add meta box
add_action('admin_menu', 'thd_re_listing_add_box');
function thd_re_listing_add_box() {
    add_meta_box('re_info', 'Listing Info', 'thd_re_listing_show_box', 'real-estate', 'normal', 'high');
}
// Callback function to show fields in meta box
function thd_re_listing_show_box() {
	thd_meta_box_callback(thd_re_meta_box_fields(), 'real-estate');
}


// Save data from meta box
add_action('save_post', 'thd_re_listing_save_data');
function thd_re_listing_save_data($post_id) {
	thd_meta_box_save($post_id, thd_re_meta_box_fields(), 'real-estate');
}

// Remove the Town Taxonomy box from the write post page
add_action( 'admin_menu' , 'thd_re_remove_towndiv' );
function thd_re_remove_towndiv() {
	remove_meta_box('towndiv', 'real-estate', 'side');
}


?>