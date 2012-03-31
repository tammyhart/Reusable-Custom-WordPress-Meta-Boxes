jQuery(function(jQuery) {
	
	// A hackish way to change the Button text to be more UX friendly
	jQuery('#media-items').bind('DOMNodeInserted',function(){
		jQuery('input[value="Insert into Post"]').each(function(){
				jQuery(this).attr('value','Use This Image');
		});
	});
	
	// the upload image button, saves the id and outputs a preview of the image
	jQuery('.thd_upload_image_button').click(function() {
		formID = jQuery(this).attr('rel');
		formfield = jQuery(this).siblings('.thd_upload_image');
		preview = jQuery(this).siblings('.thd_preview_image');
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
	jQuery('.thd_clear_image_button').click(function() {
		var defaultImage = jQuery(this).parent().siblings('.thd_default_image').text();
		jQuery(this).parent().siblings('.thd_upload_image').val('');
		jQuery(this).parent().siblings('.thd_preview_image').attr('src', defaultImage);
		return false;
	});
	
});