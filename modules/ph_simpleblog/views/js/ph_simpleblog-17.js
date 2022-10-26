function markAlreadyLikedPost(guest, id_simpleblog_post)
{
	if ($.cookie('guest_' + guest + '_' + id_simpleblog_post) == "voted") 
	{
		$('.simpleblog-like-button').addClass('voted');
	}
};

$(window).on('load', initProductPageGallery);

$(window).on('load', initMasonryList);

$(function() {
	if($('#module-ph_simpleblog-single').length > 0)
	{
		if (typeof fancybox !== 'undefined') {
			$('.simpleblog__post a.fancybox').fancybox();
		}

		$(".simpleblog__post").fitVids();

		markAlreadyLikedPost($('#module-ph_simpleblog-single .simpleblog-like-button').data('guest'), $('.simpleblog-single .simpleblog-like-button').data('post'));

		$(document).on('click', '.simpleblog__share .btn', function(){
			type = $(this).attr('data-type');
			if (type.length)
			{
				switch(type)
				{
					case 'twitter':
						window.open('https://twitter.com/intent/tweet?text=' + ph_sharing_name + ' ' + encodeURIComponent(ph_sharing_url), 'sharertwt', 'toolbar=0,status=0,width=640,height=445');
						break;
					case 'facebook':
						window.open('http://www.facebook.com/sharer.php?u=' + ph_sharing_url, 'sharer', 'toolbar=0,status=0,width=660,height=445');
						break;
					case 'pinterest':
						window.open('http://www.pinterest.com/pin/create/button/?media=' + ph_sharing_img + '&url=' + ph_sharing_url, 'sharerpinterest', 'toolbar=0,status=0,width=660,height=445');
						break;
				}
			}
		});

		$(document).on('click', '.simpleblog-like-button', function(e)
		{
			e.preventDefault();
			var id_simpleblog_post = $(this).data('post');
			var id_guest = $(this).data('guest');
			var element = $(this);

			if ($.cookie('guest_' + id_guest + '_' + id_simpleblog_post) == "voted") 
			{
				$.cookie('guest_' + id_guest + '_' + id_simpleblog_post, '');
				var request = $.ajax({
				  	type: "POST",
				  	url: ph_simpleblog_ajax,
				  	data: { 
					  	action: 'removeRating',
						id_simpleblog_post : id_simpleblog_post,
						secure_key: ph_simpleblog_token,
						ajax: true
					},
					success: function(result)
					{             
				    	var data = $.parseJSON(result);
						if (data.status == 'success') 
						{		
							element.removeClass('voted').find('span.likes-count').text(data.message);
						} 
						else 
						{
							alert(data.message);
						}
					}
				});
			}
			else
			{
				$.cookie('guest_' + id_guest + '_' + id_simpleblog_post, 'voted');
				var request = $.ajax({
				  	type: "POST",
				  	url: ph_simpleblog_ajax,
				  	data: { 
					  	action: 'addRating',
						id_simpleblog_post : id_simpleblog_post,
						secure_key: ph_simpleblog_token,
						ajax: true
					},
					success: function(result)
					{         
				    	var data = $.parseJSON(result);
						if (data.status == 'success') 
						{		
							element.addClass('voted').find('span.likes-count').text(data.message);
						} 
						else 
						{
							alert(data.message);
						}
					}
				});
			}
		});
	}

	$('.simpleblog__listing__post__wrapper').fitVids();
	
	if (typeof fancybox !== 'undefined') {
		$('.simpleblog-post-item a.post-gallery-link').fancybox();

		$("a.simpleblog__post-featured").fancybox({
		    maxWidth    : 1440,
		    fitToView   : false,
		    autoSize    : false,
		    closeClick  : false,
		    width		: 640,
			height		: 385,
		    openEffect  : 'none',
		    closeEffect : 'none',
		    iframe: {
				preload: false
			},
		    helpers : {
		        overlay : {
		            css : {
		                'background' : 'rgba(0, 0, 0, 0.90)'
		            }
		        }
		    }
		});
	}

	$(document).on('click', '.reply-simpleblog-button', function() {
	    $([document.documentElement, document.body]).animate({
	        scrollTop: $('.simpleblog__addComment').offset().top
	    }, 1000);
	    
	    $('.simpleblog_answer_info').addClass('d-block');
	    $('.simpleblog__cancelReplay').addClass('d-block');
	    $('input[name="id_parent"]').val($(this).data('id-comment'));
	});
	$(document).on('click', '.simpleblog__cancelReplay', function(e) {
			e.preventDefault();
			$('.simpleblog_answer_info').removeClass('d-block');
	    $('.simpleblog__cancelReplay').removeClass('d-block');
	    
	    $('.simpleblog_answer_info').addClass('simpleblog_answer_info_active');
	    $('input[name="id_parent"]').val(0);
	});
});


function initProductPageGallery() {
	$(".blog-fancybox").fancybox({
		helpers : {
	        overlay : {
	            locked: false
	        }
	    }
	});

	var $gallery = $('.post-gallery__gallery-js');


	$gallery.one( 'layoutComplete', function() {
		$gallery.addClass('initialized');
	});

	$gallery.masonry({
		itemSelector: '.gallery-js__elem'
	});

}

function initMasonryList() {

	var $list = $('.blog-masonry-list');

	$list.one('layoutComplete', function() {
		$list.addClass('initialized');
	});

	$list.masonry({
		itemSelector: '.simpleblog__listing__post'
	});
}