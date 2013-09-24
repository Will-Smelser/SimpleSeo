$(document).ready(function(){
	$('#run-report').button();
	
	$('.addComment').click(function(){
		editOn();
		
		var $parent = $(this).parent();
		
		var $el = $parent.next();

		//already have a comment
		if($el.hasClass('comment')){
			$el.remove();
			$(this).html('add comment').removeClass('removeComment');
			return;
		}

		//create the comment element
		var $div = $(document.createElement('div')).addClass('comment');
		var $txt = $(document.createElement('textarea'));
		var $h4 = $(document.createElement('h4')).html('Comments:');

		$div.append($h4).append($txt);
		$parent.after($div);

		$(this).html('remove comment').addClass('removeComment');
	});

	
	$('#report-title:first').click(function(){
		$('#form-run-report').toggleClass('hide');
		$('#save-edit-wrap').toggleClass('hide');
	});


	var editing = !(document.location.href.indexOf('save') > 0);
	var editOff = function(){
		$('textarea:not(#save-form-data)').each(function(){
			var $p = $(document.createElement('p')).html($(this).val()).attr('class','recommendation');
			$(this).before($p).detach();
		});
		editing = false;
	};

	var editOn = function(){
		$('.recommendation').each(function(){
			var $txt = $(document.createElement('textarea')).val($(this).html());
			$(this).before($txt).detach();
		});
		editing = true;
	};
		
	
	$('#save').button({icons:{primary:"ui-icon-disk"}}).click(function(evt){
		editOff();
		
		var content = '<!DOCTYPE html><html><head>' + $('head').html() + '</head><body><h1>SEO Report <span class="by-author">by <a href="http://simple-seo-api.com">simple-seo-api.com</a></span></h1><div id="all-content">' + $('#all-content').html() + '</div></body></html>';
		$('#save-form textarea:first').val(content).parent().submit();
		
	});

	$('#edit').button({icons:{primary:"ui-icon-pencil"}}).click(function(){
		(editing) ? editOff() : editOn();
	});
	
});