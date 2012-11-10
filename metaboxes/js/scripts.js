jQuery(function($) {
	
	// the upload image button, saves the id and outputs a preview of the image
	$('.meta_box_upload_image_button').live('click', function() {
		formID = $(this).attr('rel');
		formfield = $(this).siblings('.meta_box_upload_image');
		preview = $(this).siblings('.meta_box_preview_image');
		tb_show('Choose Image', 'media-upload.php?post_id=' + formID + '&type=image&custom=simple&TB_iframe=1');
		window.orig_send_to_editor = window.send_to_editor;
		window.send_to_editor = function(html) {
			img = $('img', html);
			imgurl = img.attr('src');
			classes = img.attr('class');
			id = classes.replace(/(.*?)wp-image-/, '');
			formfield.val(id);
			preview.attr('src', imgurl);
			tb_remove();
			window.send_to_editor = window.orig_send_to_editor;
		}
		return false;
	});
	
	// the remove image link, removes the image id from the hidden field and replaces the image preview
	$('.meta_box_clear_image_button').live('click', function() {
		var defaultImage = $(this).parent().siblings('.meta_box_default_image').text();
		$(this).parent().siblings('.meta_box_upload_image').val('');
		$(this).parent().siblings('.meta_box_preview_image').attr('src', defaultImage);
		return false;
	});
	
	// the file image button, saves the id and outputs the file name
	$('.meta_box_upload_file_button').live('click', function() {
		formID = $(this).attr('rel');
		formfield = $(this).siblings('.meta_box_upload_file');
		preview = $(this).siblings('.meta_box_filename');
		icon = $(this).siblings('.meta_box_file');
		tb_show('Choose File', 'media-upload.php?post_id='+formID+'&type=file&custom=file&TB_iframe=1');
		window.orig_send_to_editor = window.send_to_editor;
		window.send_to_editor = function(html) {
			fileurl = $(html).attr('href');
			//filename = $(html).text();
			formfield.val(fileurl);
			preview.text(fileurl);
			icon.addClass('checked');
			tb_remove();
			window.send_to_editor = window.orig_send_to_editor;
		}
		return false;
	});
	
	// the remove image link, removes the image id from the hidden field and replaces the image preview
	$('.meta_box_clear_file_button').live('click', function() {
		$(this).parent().siblings('.meta_box_upload_file').val('');
		$(this).parent().siblings('.meta_box_filename').text('');
		$(this).parent().siblings('.meta_box_file').removeClass('checked');
		return false;
	});
	
	// function to create an array of input values
	function ids(inputs) {
		var a = [];
		for (var i = 0; i < inputs.length; i++) {
			a.push(inputs[i].val);
		}
		//$("span").text(a.join(" "));
    }
	// repeatable fields
	$('.meta_box_repeatable_add').live('click', function() {
		// clone
		var row = $(this).closest('.meta_box_repeatable').find('tbody tr:last-child');
		var clone = row.clone();
		clone.find('input.regular-text, input[type=hidden], textarea').val('');
		var defaultImage = $(this).parent().siblings('.meta_box_default_image').text();
		clone.find('.meta_box_preview_image').attr('src', defaultImage);
		row.after(clone);
		// increment name and id
		clone.find('input.regular-text, input[type=hidden], textarea')
			.attr('name', function(index, name) {
				return name.replace(/(\d+)/, function(fullMatch, n) {
					return Number(n) + 1;
				});
			});
		var arr = [];
		$('input.repeatable_id:text').each(function(){ arr.push($(this).val()); }); 
		clone.find('input.repeatable_id')
			.val(Number(Math.max.apply( Math, arr )) + 1);
		//
		return false;
	});
	
	$('.meta_box_repeatable_remove').live('click', function(){
		$(this).closest('tr').remove();
		return false;
	});
		
	$('.meta_box_repeatable tbody').sortable({
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.hndle'
	});
	
	// post_drop_sort	
	$('.sort_list').sortable({
		connectWith: '.sort_list',
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		cancel: '.post_drop_sort_area_name',
		items: 'li:not(.post_drop_sort_area_name)',
        update: function(event, ui) {
			var result = $(this).sortable('toArray');
			var thisID = $(this).attr('id');
			$('.store-' + thisID).val(result) 
		}
        //stop: function(){ $('.string').val($("ul.droptrue").sortable("serialize")) },
    });

	$('.sort_list').disableSelection();
	
	// chosen
	$('.chosen').chosen({
		allow_single_deselect: true	
	});
});