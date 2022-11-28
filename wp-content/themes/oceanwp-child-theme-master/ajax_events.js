(function(window, $){
	var LOAD_MORE_WRAPPER_ATTR = 'data-load-more-wrapper';
	var LOAD_MORE_ATTR = 'data-load-more';
	var AJAX_EVENTS_WRAPPER = 'data-ajax-events';

	var LOADER_START = function() {
		var $load_more = $('[' + LOAD_MORE_WRAPPER_ATTR + ']');
        $load_more.addClass('loading');
	}
	
	var LOADER_END = function() {
		var $load_more = $('[' + LOAD_MORE_WRAPPER_ATTR + ']');
        $load_more.removeClass('loading');
	}

	$(document).on('ready', function(){
        $(document).on('click', '[data-event-id]', function(e){
            e.preventDefault();
            var $link = $(this).find('.fy-button--empty');
            if($link.length) {
                window.location.href = $link.attr('href');
            }
        });

		$(document).on('click', '['+ LOAD_MORE_ATTR +']', function(e){
			e.preventDefault();
			var offset = $(this).attr('data-next-offset');
			var params = $(this).attr('data-params');
			var parents_cache = $(this).attr('data-parents-cache');
			var URL = window.events_ajax.url;

			LOADER_START();

			$.ajax({
				type: 'POST',
				url: URL, 
				data : {
					action: 'load_more_events',
					next_offset: offset,
					params: params,
					parents_cache: parents_cache
				},
				success: function( data ) {
					console.log(data);
					var $events_list = $(data.markup);
					var $load_more_button;
					
					$('['+ LOAD_MORE_WRAPPER_ATTR +']').remove();
					
					if($events_list.length) {
						$events_list.each(function(index){
							console.log($(this));
							var event_id = $(this).attr('data-event-id');
							
							if($(this).attr(LOAD_MORE_WRAPPER_ATTR)){
								$load_more_button = $(this);
							}

							if(!event_id){
								return;
							}
							
							if(!$('[data-event-id="' + event_id + '"]').length) {
								$('['+ AJAX_EVENTS_WRAPPER +']').append($(this));
							}
						});
					}

					if($load_more_button && $load_more_button.length) {
						$('['+ AJAX_EVENTS_WRAPPER +']').append($load_more_button);
					} else {
                        $('[data-load-more-end]').addClass('done-loading');
                    }
					
					LOADER_END();
				},
				error: function( error ) {
					console.error(error);
					LOADER_END();
				}
			});
		});
	});
})(window, jQuery);