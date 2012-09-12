jQuery(function(jQuery) {
	
	// A hackish way to change the Button text to be more UX friendly
	jQuery('#media-items').bind('DOMNodeInserted',function(){
		jQuery('input[value="Insert into Post"]').each(function(){
				jQuery(this).attr('value','Use This Image');
		});
	});
	
	// the upload image button, saves the id and outputs a preview of the image
	jQuery('.meta_box_upload_image_button').click(function() {
		formID = jQuery(this).attr('rel');
		formfield = jQuery(this).siblings('.meta_box_upload_image');
		preview = jQuery(this).siblings('.meta_box_preview_image');
		tb_show('Choose Image', 'media-upload.php?post_id='+formID+'&type=image&TB_iframe=1');
		window.send_to_editor = function(html) {
			img = jQuery('img',html);
			imgurl = img.attr('src');
			classes = img.attr('class');
			id = classes.replace(/(.*?)wp-image-/, '');
			formfield.val(id);
			preview.attr('src', imgurl);
			tb_remove();
		}
		return false;
	});
	
	// the remove image link, removes the image id from the hidden field and replaces the image preview
	jQuery('.meta_box_clear_image_button').click(function() {
		var defaultImage = jQuery(this).parent().siblings('.meta_box_default_image').text();
		jQuery(this).parent().siblings('.meta_box_upload_image').val('');
		jQuery(this).parent().siblings('.meta_box_preview_image').attr('src', defaultImage);
		return false;
	});
	
	// repeatable fields
	jQuery('.meta_box_repeatable_add').live('click', function() {
		// clone
		var row = jQuery(this).siblings('.meta_box_repeatable').find('li:last');
		var clone = row.clone();
		clone.find('input').val('');
		row.after(clone);
		// increment name and id
		clone.find('input')
			.attr('name', function(index, name) {
				return name.replace(/(\d+)/, function(fullMatch, n) {
					return Number(n) + 1;
				});
			})
			.attr('id', function(index, name) {
				return name.replace(/(\d+)/, function(fullMatch, n) {
					return Number(n) + 1;
				});
			});
		//
		return false;
	});
	
	jQuery('.meta_box_repeatable_remove').live('click', function(){
		jQuery(this).closest('li').remove();
		return false;
	});
		
	jQuery('.meta_box_repeatable').sortable({
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.handle'
	});
	
});