(function () {
	'use strict';

	var form = document.getElementById('fbg-sidebar-contact-form');
	var fb = document.getElementById('fbg-sc-feedback');
	if (form && fb && typeof fbgSingleSidebar !== 'undefined') {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			fb.hidden = true;
			fb.className = 'fbg-sidebar-form__feedback';
			var btn = form.querySelector('.fbg-sidebar-form__submit');
			var fd = new FormData(form);
			fd.append('action', 'fbg_sidebar_contact');
			fd.append('nonce', fbgSingleSidebar.nonce);
			btn.disabled = true;
			fetch(fbgSingleSidebar.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function (r) {
					return r.json();
				})
				.then(function (json) {
					fb.hidden = false;
					if (json.success) {
						fb.classList.add('is-ok');
						fb.textContent = fbgSingleSidebar.strings.sent;
						form.reset();
					} else {
						fb.classList.add('is-err');
						fb.textContent =
							(json.data && json.data.message) || fbgSingleSidebar.strings.error;
					}
				})
				.catch(function () {
					fb.hidden = false;
					fb.classList.add('is-err');
					fb.textContent = fbgSingleSidebar.strings.error;
				})
				.finally(function () {
					btn.disabled = false;
				});
		});
	}

	var slider = document.querySelector('.fbg-ad-slider');
	if (!slider) return;
	var viewport = slider.querySelector('.fbg-ad-slider__viewport');
	var slides = slider.querySelectorAll('.fbg-ad-slider__slide');
	if (!viewport || slides.length === 0) return;

	function slideWidth() {
		return viewport.clientWidth;
	}

	function go(delta) {
		viewport.scrollBy({ left: delta * slideWidth(), behavior: 'smooth' });
	}

	var prev = slider.querySelector('.fbg-ad-slider__btn--prev');
	var next = slider.querySelector('.fbg-ad-slider__btn--next');
	if (prev) prev.addEventListener('click', function () { go(-1); });
	if (next) next.addEventListener('click', function () { go(1); });

	var autoplay = parseInt(slider.getAttribute('data-autoplay'), 10) || 0;
	if (autoplay > 0 && slides.length > 1) {
		setInterval(function () {
			var maxScroll = viewport.scrollWidth - viewport.clientWidth;
			if (maxScroll <= 0) return;
			if (viewport.scrollLeft >= maxScroll - 2) {
				viewport.scrollTo({ left: 0, behavior: 'smooth' });
			} else {
				go(1);
			}
		}, autoplay);
	}
})();
