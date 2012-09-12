Reusable Custom WordPress Meta Boxes
====================================

Version: 0.2

Contributors: [tammyhart](http://github.com/tammyhart)

Original Tutorial Series: (http://wp.tutsplus.com/author/tammy/)


Description
-----------

This project was originally a tutorial for WPTuts+, but as I continued to use the code in my own 
projects, I kept improving it and making the resusable part even better. Rather than update the 
tutorial, I decided to make it an open source project here where developers can keep up with 
improvements as well as contribute their own.

This project creates a class which makes it easy to create a custom meta box for any 
post type using a master switch case for the meta box and fields HTML and an array containing 
the data for the fields you want to use.


### Fields Included

* Text Input
* Textarea
* Single checkbox
* Select box
* Radio group
* Checkbox group
* Taxonomy Select box
* Post ID select box
* jQuery UI Date input
* jQuery UI Slider
* Image ID field
* Repeatable & Sortable Text inputs


Usage
-----

These files are written from the perspecitve of being used in a theme. 

1. Add the metaboxes directory in your theme or plugin.
2. Include metaboxes/meta_box.php in your functions.php.
4. Use the class to create add meta boxes to any post type (see Examples section below or inc/sample.php)


Examples
--------

### Create an array that contains the field data (full examples of each filed type found in inc/sample.php)

	$prefix = 'sample_';
	
	$fields = array(
		array( // Text Input
			'label'	=> 'Text Input', // <label>
			'desc'	=> 'A description for the field.', // description
			'id'	=> $prefix.'text', // field id and name
			'type'	=> 'text' // type of field
		),
		array( // Textarea
			'label'	=> 'Textarea', // <label>
			'desc'	=> 'A description for the field.', // description
			'id'	=> $prefix.'textarea', // field id and name
			'type'	=> 'textarea' // type of field
		)
	);

### Instantiate the class with all required variables

	/**
	 * Instantiate the class with all variables to create a meta box
	 * var $id string meta box id
	 * var $title string title
	 * var $fields array fields
	 * var $page string|array post type to add meta box to
	 * var $js bool including javascript or not
	 */
	$sample_box = new custom_add_meta_box( 'sample_box', 'Sample Box', $fields, 'post', true );


Changelog
---------

### 0.2 (September 11, 2012)
* Functions combined into a class
* Major code cleanup and some docing added

### 0.1 (March 31, 2012)
* First release