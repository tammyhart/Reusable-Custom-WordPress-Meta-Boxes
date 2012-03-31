<?php

add_action( 'init', 'thd_register_taxonomies' );

function thd_register_taxonomies() {

	// Towns
    $labels = array( 
        'name' => 'Towns',
        'singular_name' => 'Town',
        'search_items' => 'Search Towns',
        'popular_items' => 'Popular Towns',
        'all_items' => 'All Towns',
        'parent_item' => 'Parent Town',
        'parent_item_colon' => 'Parent Town:',
        'edit_item' => 'Edit Town',
        'update_item' => 'Update Town',
        'add_new_item' => 'Add New Town',
        'new_item_name' => 'New Town Name',
        'separate_items_with_commas' => 'Separate towns with commas',
        'add_or_remove_items' => 'Add or remove towns',
        'choose_from_most_used' => 'Choose from the most used towns',
        'menu_name' => 'Towns',
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_tagcloud' => false,
        'hierarchical' => true,

        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'town', array('post', 'coupon', 'announcement', 'sponsor', 'town-news', 'town-photo', 'event'), $args );
}

?>