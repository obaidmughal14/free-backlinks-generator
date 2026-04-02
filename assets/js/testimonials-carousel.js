(function () {
	'use strict';

	var root = document.querySelector('.fbg-testimonials-carousel');
	if (!root) return;

	var viewport = root.querySelector('.fbg-testimonials-carousel__viewport');
	var track = root.querySelector('.fbg-testimonials-carousel__track');
	var slides = track ? track.querySelectorAll('.fbg-testimonial-slide') : [];
	var prevBtn = root.querySelector('.fbg-tc-prev');
	var nextBtn = root.querySelector('.fbg-tc-next');
	var dotsWrap = root.querySelector('.fbg-tc-dots');
	if (!viewport || !track || slides.length === 0) return;

	var index = 0;
	var autoplayMs = parseInt(root.getAttribute('data-autoplay') || '7000', 10);
	var timer = null;
	var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function perView() {
		if (window.matchMedia('(min-width: 1024px)').matches) return 3;
		if (window.matchMedia('(min-width: 600px)').matches) return 2;
		return 1;
	}

	function maxIndex() {
		var pv = perView();
		return Math.max(0, slides.length - pv);
	}

	function slideWidth() {
		return slides[0] ? slides[0].offsetWidth : 0;
	}

	function goTo(i) {
		var max = maxIndex();
		index = Math.max(0, Math.min(i, max));
		var w = slideWidth();
		track.style.transform = 'translateX(' + -index * w + 'px)';
		if (prevBtn) prevBtn.disabled = index <= 0;
		if (nextBtn) nextBtn.disabled = index >= max;
		if (dotsWrap) {
			var dots = dotsWrap.querySelectorAll('.fbg-tc-dot');
			dots.forEach(function (d, di) {
				d.setAttribute('aria-current', di === index ? 'true' : 'false');
			});
		}
	}

	function buildDots() {
		if (!dotsWrap) return;
		dotsWrap.innerHTML = '';
		var n = maxIndex() + 1;
		for (var d = 0; d < n; d++) {
			(function (j) {
				var b = document.createElement('button');
				b.type = 'button';
				b.className = 'fbg-tc-dot';
				b.setAttribute('aria-label', 'Go to slide ' + (j + 1));
				b.addEventListener('click', function () {
					goTo(j);
					resetAutoplay();
				});
				dotsWrap.appendChild(b);
			})(d);
		}
	}

	function resetAutoplay() {
		if (timer) clearInterval(timer);
		if (reduced || autoplayMs < 2000) return;
		timer = setInterval(function () {
			var max = maxIndex();
			if (index >= max) goTo(0);
			else goTo(index + 1);
		}, autoplayMs);
	}

	if (prevBtn) {
		prevBtn.addEventListener('click', function () {
			goTo(index - 1);
			resetAutoplay();
		});
	}
	if (nextBtn) {
		nextBtn.addEventListener('click', function () {
			goTo(index + 1);
			resetAutoplay();
		});
	}

	function syncLayout() {
		var keep = index;
		buildDots();
		goTo(Math.min(keep, maxIndex()));
	}

	var ro = new ResizeObserver(function () {
		syncLayout();
	});
	ro.observe(viewport);

	window.addEventListener('load', function () {
		syncLayout();
		resetAutoplay();
	});

	root.addEventListener('mouseenter', function () {
		if (timer) clearInterval(timer);
	});
	root.addEventListener('mouseleave', function () {
		resetAutoplay();
	});
})();
