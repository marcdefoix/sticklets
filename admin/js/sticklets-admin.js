(function($) {
	$(document).ready(function() {
		$('input[name="sticklet_visibility"]').on('change', function() {
			if ($(this).val() === 'specific') {
				$('.visibility-specific').slideDown(200);
			} else {
				$('.visibility-specific').slideUp(200);
			}
		});

		$('.sticklets-toggle-ids').on('change', function() {
			if ($(this).is(':checked')) {
				$('.visibility-ids-wrap').slideDown(200);
			} else {
				$('.visibility-ids-wrap').slideUp(200);
			}
		});

		$('input[name="sticklet_trigger"]').on('change', function() {
			var val = $(this).val();
			$('.trigger-scroll-px, .trigger-scroll-element, .trigger-click-element, .trigger-scroll-bottom').slideUp(200);
			if (val === 'scroll_px') {
				$('.trigger-scroll-px').slideDown(200);
			} else if (val === 'scroll_element') {
				$('.trigger-scroll-element').slideDown(200);
			} else if (val === 'click_element') {
				$('.trigger-click-element').slideDown(200);
			} else if (val === 'scroll_bottom') {
				$('.trigger-scroll-bottom').slideDown(200);
			}
		});

		$('input[name="sticklet_action"]').on('change', function() {
			var val = $(this).val();
			$('.action-url, .action-scroll').slideUp(200);
			if (val === 'url') {
				$('.action-url').slideDown(200);
			} else if (val === 'scroll') {
				$('.action-scroll').slideDown(200);
			}
		});

    $('#sticklet_size_mobile_custom').on('change', function() {
      if ($(this).is(':checked')) {
        $('.mobile-size-fields').slideDown(200);
      } else {
        $('.mobile-size-fields').slideUp(200);
      }
    });

    $('input[name="sticklet_frequency"]').on('change', function() {
      if ($(this).val() === 'times') {
        $('.frequency-times').slideDown(200);
      } else {
        $('.frequency-times').slideUp(200);
      }
    });
	});
})(jQuery);