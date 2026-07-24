(function($) {
	$(document).ready(function() {
		$('input[name="sticklet_visibility_scope"]').on('change', function() {
      let val = $(this).val();
			if (val === 'specific') {
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

		$('input[name="sticklet_trigger_mode"]').on('change', function() {
			let val = $(this).val();
			$('.trigger-specific').slideUp(200);
			if (val === 'specific') {
				$('.trigger-specific').slideDown(200);
			}
		});

		$('input[name="sticklet_trigger_specific"]').on('change', function() {
			let val = $(this).val();
			$('.trigger-scroll-px, .trigger-scroll-element, .trigger-scroll-bottom').slideUp(200);

			if (val === 'scroll_px') {
				$('.trigger-scroll-px').slideDown(200);
			} else if (val === 'scroll_element') {
				$('.trigger-scroll-element').slideDown(200);
			} else if (val === 'scroll_bottom') {
				$('.trigger-scroll-bottom').slideDown(200);
			}
		});

    $('input[name="sticklet_size_mode"]').on('change', function() {
      let val = $(this).val();
      $('.size-cropped').slideUp(200);
      if (val === 'cropped') {
        $('.size-cropped').slideDown(200);
      }
    });
	});
})(jQuery);