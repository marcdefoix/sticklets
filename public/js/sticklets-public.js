(function() {
	function getStorageValue(key) {
		try {
			return localStorage.getItem(key);
		} catch (error) {
			return null;
		}
	}

	function setStorageValue(key, value) {
		try {
			localStorage.setItem(key, value);
		} catch (error) {
			// Continue without persistence when storage is unavailable.
		}
	}

	function getFrequency(sticklet) {
		var frequency = sticklet.getAttribute('data-frequency') || 'always';
		return frequency === 'times' ? 'times' : 'always';
	}

	function getFrequencyStorageKey(sticklet) {
		return 'sticklets_seen_' + sticklet.getAttribute('data-sticklet-id');
	}

	function checkFrequency(sticklet) {
		var frequency = getFrequency(sticklet);

		if (frequency === 'always') {
			return true;
		}

		var maxTimes = Math.max(1, parseInt(sticklet.getAttribute('data-frequency-times'), 10) || 1);
		var seen = parseInt(getStorageValue(getFrequencyStorageKey(sticklet)), 10) || 0;

		return seen < maxTimes;
	}

	function consumeFrequency(sticklet) {
		if (getFrequency(sticklet) === 'times') {
			var storageKey = getFrequencyStorageKey(sticklet);
			var seen = parseInt(getStorageValue(storageKey), 10) || 0;
			setStorageValue(storageKey, seen + 1);
		}
	}

	function debounce(fn, wait) {
		var timeout;
		return function() {
			var context = this;
			var args = arguments;
			clearTimeout(timeout);
			timeout = setTimeout(function() {
				fn.apply(context, args);
			}, wait);
		};
	}

	function handleAction(sticklet) {
		var action = sticklet.getAttribute('data-action') || 'none';

		if (action === 'scroll' || action === 'scrolltop') {
			var actionLink = sticklet.querySelector('.sticklet__action');
			if (!actionLink) {
				return;
			}

			actionLink.addEventListener('click', function(e) {
				e.preventDefault();

				if (action === 'scroll') {
					var selector = sticklet.getAttribute('data-action-scroll-to');
					var offset = parseInt(sticklet.getAttribute('data-action-scroll-offset')) || 0;

					if (selector) {
						var target = getQueryElement(selector);
						if (target) {
							var top = target.getBoundingClientRect().top + window.scrollY - offset;
							window.scrollTo({ top: top, behavior: 'smooth' });
						}
					}
				} else if (action === 'scrolltop') {
					window.scrollTo({ top: 0, behavior: 'smooth' });
				}
			});
		}
	}

	function calculateSizeAndPosition(sticklet) {
		var positionY = sticklet.getAttribute('data-position-y');
		var positionX = sticklet.getAttribute('data-position-x');
		var offsetX = parseInt(sticklet.getAttribute('data-position-offset-x')) || 0;
		var offsetY = parseInt(sticklet.getAttribute('data-position-offset-y')) || 0;
		var sizeWidth = parseInt(sticklet.getAttribute('data-size-width')) || 0;
		var sizeHeight = parseInt(sticklet.getAttribute('data-size-height')) || 0;
		var sizeMobile = parseInt(sticklet.getAttribute('data-size-mobile')) || 0;
		var sizeMobileWidth = parseInt(sticklet.getAttribute('data-size-mobile-width')) || 0;
		var sizeMobileHeight = parseInt(sticklet.getAttribute('data-size-mobile-height')) || 0;

		var windowWidth = window.innerWidth || document.documentElement.clientWidth;
		var windowHeight = window.innerHeight || document.documentElement.clientHeight;
		var isMobile = windowWidth < 768;

		var activeWidth, activeHeight;

		if (isMobile && sizeMobile) {
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

  function getQueryElement(selector) {
    try {
      return selector ? document.querySelector(selector) : null;
    } catch (error) {
      return null;
    }
  }

	function getAnimationValue(value, type) {
		var allowed = type === 'appear'
			? ['none', 'fade-in', 'slide-up', 'slide-down', 'slide-left', 'slide-right']
			: ['none', 'fade-out', 'slide-up-out', 'slide-down-out', 'slide-left-out', 'slide-right-out'];

		return allowed.indexOf(value) !== -1 ? value : 'none';
	}

	function getAnimationName(animation) {
		return 'sticklet' + animation.split('-').map(function(part) {
			return part.charAt(0).toUpperCase() + part.slice(1);
		}).join('');
	}

	function showSticklet(sticklet) {
		var duration = parseInt(sticklet.getAttribute('data-timing-duration')) || 0;
		var delay = parseInt(sticklet.getAttribute('data-timing-delay')) || 0;
		var animationAppear = getAnimationValue(sticklet.getAttribute('data-animation-appear') || 'none', 'appear');
		var animationExit = getAnimationValue(sticklet.getAttribute('data-animation-exit') || 'none', 'exit');
		var durationTimer = null;
		sticklet._sticklets_closed = false;

		if (!checkFrequency(sticklet)) {
			sticklet.parentNode.removeChild(sticklet);
			return;
		}

		calculateSizeAndPosition(sticklet);
		handleAction(sticklet);
		handleEarlyExit(sticklet);

		// Recalculate size/position on window resize while the sticklet is visible (debounced).
		var resizeHandler = debounce(function() {
			calculateSizeAndPosition(sticklet);
		}, 150);
		window.addEventListener('resize', resizeHandler);
		// store reference for other handlers to remove when the sticklet is hidden
		sticklet._sticklets_resizeHandler = resizeHandler;

		function appear() {
			consumeFrequency(sticklet);
			sticklet.classList.remove('sticklet--hidden');

			if (animationAppear !== 'none') {
				sticklet.classList.add('sticklet--animate-' + animationAppear);

				sticklet.addEventListener('animationend', function handler(event) {
					if (event.target !== sticklet || event.animationName !== getAnimationName(animationAppear)) {
						return;
					}
					sticklet.classList.remove('sticklet--animate-' + animationAppear);
					sticklet.removeEventListener('animationend', handler);
				});
			}

			if (duration > 0) {

				durationTimer = setTimeout(function() {
					durationTimer = null;
					sticklet._sticklets_durationTimer = null;
					if (sticklet._sticklets_closed) {
						return;
					}
					if (!sticklet || !sticklet.parentNode) {
            // ensure resize handler cleaned up
            if (sticklet && sticklet._sticklets_resizeHandler) {
              window.removeEventListener('resize', sticklet._sticklets_resizeHandler);
              delete sticklet._sticklets_resizeHandler;
            }
            return;
					}

					if (animationExit !== 'none') {
						sticklet.classList.add('sticklet--animate-' + animationExit);

						sticklet.addEventListener('animationend', function handler(event) {
							if (event.target !== sticklet || event.animationName !== getAnimationName(animationExit)) {
								return;
							}
							if (sticklet) {
								sticklet.classList.remove('sticklet--animate-' + animationExit);
								sticklet.classList.add('sticklet--hidden');
							}
							sticklet.removeEventListener('animationend', handler);
								// cleanup after animation completes
								if (sticklet && sticklet._sticklets_resizeHandler) {
									window.removeEventListener('resize', sticklet._sticklets_resizeHandler);
									delete sticklet._sticklets_resizeHandler;
								}
							});
					} else if (sticklet) {
						sticklet.classList.add('sticklet--hidden');
							// cleanup when hidden without animation
							if (sticklet && sticklet._sticklets_resizeHandler) {
								window.removeEventListener('resize', sticklet._sticklets_resizeHandler);
								delete sticklet._sticklets_resizeHandler;
							}
					}
				}, duration);
				sticklet._sticklets_durationTimer = durationTimer;
			}
		}

		if (delay > 0) {
			setTimeout(appear, delay);
		} else {
			appear();
		}
	}

	function handleEarlyExit(sticklet) {
		var animationExit = getAnimationValue(sticklet.getAttribute('data-animation-exit') || 'none', 'exit');

		sticklet.addEventListener('click', function(e) {
			if (!sticklet || !sticklet.parentNode) {
				return;
			}

			sticklet._sticklets_closed = true;
			if (sticklet._sticklets_durationTimer) {
				clearTimeout(sticklet._sticklets_durationTimer);
				sticklet._sticklets_durationTimer = null;
			}

			if (animationExit !== 'none') {
				sticklet.classList.add('sticklet--animate-' + animationExit);

				sticklet.addEventListener('animationend', function handler(event) {
					if (event.target !== sticklet || event.animationName !== getAnimationName(animationExit)) {
						return;
					}
					if (sticklet) {
						sticklet.classList.remove('sticklet--animate-' + animationExit);
						sticklet.classList.add('sticklet--hidden');
					}
					sticklet.removeEventListener('animationend', handler);
					// cleanup resize handler
					if (sticklet && sticklet._sticklets_resizeHandler) {
						window.removeEventListener('resize', sticklet._sticklets_resizeHandler);
						delete sticklet._sticklets_resizeHandler;
					}
				});
			} else if (sticklet) {
				sticklet.classList.add('sticklet--hidden');
				if (sticklet._sticklets_resizeHandler) {
					window.removeEventListener('resize', sticklet._sticklets_resizeHandler);
					delete sticklet._sticklets_resizeHandler;
				}
			}
		});
	}

  function isElementInViewport(el, offset) {
    var rect = el.getBoundingClientRect();
    var viewportHeight = window.innerHeight || document.documentElement.clientHeight;
    offset = offset || 0;

    return (
      rect.top <= viewportHeight + offset &&
      rect.bottom >= -offset
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
      var offset = parseInt(sticklet.getAttribute('data-trigger-scroll-element-offset')) || 0;

      if (selector) {
        var targetEl = getQueryElement(selector);

        if (targetEl) {
          var checkVisibility = function() {
            if (isElementInViewport(targetEl, offset)) {
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

		if (trigger === 'click_element') {
			var clickSelector = sticklet.getAttribute('data-trigger-click-element');
			if (clickSelector) {
				var clickTargetEl = getQueryElement(clickSelector);
				if (clickTargetEl) {
					var clickHandler = function() {
						showSticklet(sticklet);
						clickTargetEl.removeEventListener('click', clickHandler);
					};
					clickTargetEl.addEventListener('click', clickHandler);
				}
			}
		}

		if (trigger === 'scroll_bottom') {
			var bottomOffset = parseInt(sticklet.getAttribute('data-trigger-scroll-bottom-offset')) || 0;
			var bottomHandler = function() {
        var scrollBottom = window.scrollY + window.innerHeight;
        var pageHeight = Math.max(
          document.body.scrollHeight,
          document.documentElement.scrollHeight
        );
        var pageBottom = pageHeight - bottomOffset;

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