(function() {
	function handleAction(sticklet) {
		var actionType = sticklet.getAttribute('data-action-type') || 'none';

		if (actionType === 'scroll' || actionType === 'scrolltop') {
			var actionLink = sticklet.querySelector('.sticklet__action');
			if (!actionLink) {
				return;
			}

			actionLink.addEventListener('click', function(e) {
				e.preventDefault();

				if (actionType === 'scroll') {
					var selector = sticklet.getAttribute('data-action-scroll-to');
					var offset = parseInt(sticklet.getAttribute('data-action-scroll-offset')) || 0;

					if (selector) {
						var target = document.querySelector(selector);
						if (target) {
							var top = target.getBoundingClientRect().top + window.scrollY - offset;
							window.scrollTo({ top: top, behavior: 'smooth' });
						}
					}
				} else if (actionType === 'scrolltop') {
					window.scrollTo({ top: 0, behavior: 'smooth' });
				}
			});
		}
	}

	function calculateCenterPosition(sticklet) {
    var positionY = sticklet.getAttribute('data-position-y');
    var positionX = sticklet.getAttribute('data-position-x');
    var offsetX = parseInt(sticklet.getAttribute('data-position-offset-x')) || 0;
    var offsetY = parseInt(sticklet.getAttribute('data-position-offset-y')) || 0;
    var sizeWidth = parseInt(sticklet.getAttribute('data-size-width')) || 0;
    var sizeHeight = parseInt(sticklet.getAttribute('data-size-height')) || 0;
    var sizeMobileWidth = parseInt(sticklet.getAttribute('data-size-mobile-width')) || 0;
    var sizeMobileHeight = parseInt(sticklet.getAttribute('data-size-mobile-height')) || 0;

    var windowWidth = window.innerWidth || document.documentElement.clientWidth;
    var windowHeight = window.innerHeight || document.documentElement.clientHeight;
    var isMobile = windowWidth < 768;

    var activeWidth, activeHeight;

    if (isMobile) {
      activeWidth = sizeMobileWidth > 0 ? sizeMobileWidth : sizeWidth;
      activeHeight = sizeMobileHeight > 0 ? sizeMobileHeight : sizeHeight;
    } else {
      activeWidth = sizeWidth;
      activeHeight = sizeHeight;
    }

    if (activeWidth > 0 || activeHeight > 0) {
      sticklet.style.width = activeWidth > 0 ? activeWidth + 'px' : 'auto';
      sticklet.style.height = activeHeight > 0 ? activeHeight + 'px' : 'auto';
    }

    var imgWidth, imgHeight;

    if (activeWidth > 0 && activeHeight > 0) {
      imgWidth = activeWidth;
      imgHeight = activeHeight;
    } else {
      var img = sticklet.querySelector('img');
      if (img && img.naturalWidth > 0 && img.naturalHeight > 0) {
        imgWidth = img.naturalWidth;
        imgHeight = img.naturalHeight;
      } else if (img) {
        imgWidth = img.offsetWidth || img.clientWidth || 0;
        imgHeight = img.offsetHeight || img.clientHeight || 0;
      } else {
        imgWidth = 0;
        imgHeight = 0;
      }
    }

    if (positionY === 'y-center') {
      var top = Math.round(windowHeight / 2 - imgHeight / 2 + offsetY);
      sticklet.style.top = top + 'px';
      sticklet.style.bottom = 'auto';
    }

    if (positionX === 'x-center') {
      var left = Math.round(windowWidth / 2 - imgWidth / 2 + offsetX);
      sticklet.style.left = left + 'px';
      sticklet.style.right = 'auto';
    }
  }

	function showSticklet(sticklet) {
		var duration = parseInt(sticklet.getAttribute('data-timing-duration')) || 0;
		var delay = parseInt(sticklet.getAttribute('data-timing-delay')) || 0;
		var animationAppear = sticklet.getAttribute('data-animation-appear') || 'none';
		var animationExit = sticklet.getAttribute('data-animation-exit') || 'none';

		calculateCenterPosition(sticklet);
		handleAction(sticklet);
    handleEarlyExit(sticklet);

		function appear() {
			sticklet.classList.remove('sticklet--hidden');

			if (animationAppear !== 'none') {
				sticklet.classList.add('sticklet--animate-' + animationAppear);

				sticklet.addEventListener('animationend', function handler() {
					sticklet.classList.remove('sticklet--animate-' + animationAppear);
					sticklet.removeEventListener('animationend', handler);
				});
			}

			if (duration > 0) {
				var exitDelay = delay + duration;

				setTimeout(function() {
					if (!sticklet || !sticklet.parentNode) {
						return;
					}

					if (animationExit !== 'none') {
						sticklet.classList.add('sticklet--animate-' + animationExit);

						sticklet.addEventListener('animationend', function handler() {
							if (sticklet && sticklet.parentNode) {
								sticklet.parentNode.removeChild(sticklet);
							}
							sticklet.removeEventListener('animationend', handler);
						});
					} else {
						sticklet.parentNode.removeChild(sticklet);
					}
				}, exitDelay);
			}
		}

		if (delay > 0) {
			setTimeout(appear, delay);
		} else {
			appear();
		}
	}

  function handleEarlyExit(sticklet) {
    var animationExit = sticklet.getAttribute('data-animation-exit') || 'none';

    sticklet.addEventListener('click', function(e) {
      if (!sticklet || !sticklet.parentNode) {
        return;
      }

      if (animationExit !== 'none') {
        sticklet.classList.add('sticklet--animate-' + animationExit);

        sticklet.addEventListener('animationend', function handler() {
          if (sticklet && sticklet.parentNode) {
            sticklet.parentNode.removeChild(sticklet);
          }
          sticklet.removeEventListener('animationend', handler);
        });
      } else {
        sticklet.parentNode.removeChild(sticklet);
      }
    });
  }

	function isElementInViewport(el) {
		var rect = el.getBoundingClientRect();
		return (
			rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
			rect.bottom >= 0
		);
	}

	function initSticklet(sticklet) {
    var trigger = sticklet.getAttribute('data-trigger');

    if (trigger === 'load') {
      showSticklet(sticklet);
      return;
    }

    if (trigger === 'scroll_px') {
      var px = parseInt(sticklet.getAttribute('data-trigger-scroll-px')) || 0;
      var scrollHandler = function() {
        if (window.scrollY >= px) {
          showSticklet(sticklet);
          window.removeEventListener('scroll', scrollHandler);
        }
      };
      window.addEventListener('scroll', scrollHandler);
      scrollHandler();
    }

    if (trigger === 'scroll_element') {
      var selector = sticklet.getAttribute('data-trigger-scroll-element');
      if (selector) {
        var targetEl = document.querySelector(selector);
        if (targetEl) {
          var checkVisibility = function() {
            if (isElementInViewport(targetEl)) {
              showSticklet(sticklet);
              window.removeEventListener('scroll', checkVisibility);
              window.removeEventListener('resize', checkVisibility);
            }
          };
          window.addEventListener('scroll', checkVisibility);
          window.addEventListener('resize', checkVisibility);
          checkVisibility();
        }
      }
    }

    if (trigger === 'scroll_bottom') {
      var offset = parseInt(sticklet.getAttribute('data-trigger-scroll-bottom-offset')) || 0;
      var bottomHandler = function() {
        var scrollBottom = window.scrollY + window.innerHeight;
        var pageBottom = document.body.scrollHeight - offset;
        if (scrollBottom >= pageBottom) {
          showSticklet(sticklet);
          window.removeEventListener('scroll', bottomHandler);
        }
      };
      window.addEventListener('scroll', bottomHandler);
      bottomHandler();
    }
  }

	function initAllSticklets() {
		var sticklets = document.querySelectorAll('.sticklet');
		for (var i = 0; i < sticklets.length; i++) {
			initSticklet(sticklets[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAllSticklets);
	} else {
		initAllSticklets();
	}
})();