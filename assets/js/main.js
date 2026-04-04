(function () {
	'use strict';

	var themeKey = 'fbg-theme';
	var root = document.documentElement;

	function syncThemeToggles() {
		var isDark = root.getAttribute('data-theme') === 'dark';
		document.querySelectorAll('[data-fbg-theme-toggle]').forEach(function (btn) {
			btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
		});
	}

	function setTheme(dark) {
		if (dark) {
			root.setAttribute('data-theme', 'dark');
			try {
				localStorage.setItem(themeKey, 'dark');
			} catch (e) {}
		} else {
			root.removeAttribute('data-theme');
			try {
				localStorage.setItem(themeKey, 'light');
			} catch (e) {}
		}
		syncThemeToggles();
	}

	document.querySelectorAll('[data-fbg-theme-toggle]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var isDark = root.getAttribute('data-theme') === 'dark';
			setTheme(!isDark);
		});
	});
	syncThemeToggles();

	var nav = document.getElementById('main-nav');
	if (nav) {
		window.addEventListener(
			'scroll',
			function () {
				if (window.scrollY > 80) {
					nav.classList.add('is-scrolled');
				} else {
					nav.classList.remove('is-scrolled');
				}
			},
			{ passive: true }
		);
	}

	function setupDrawer(btn, drawer) {
		if (!btn || !drawer) return;

		var panel = drawer.querySelector('.fbg-nav-drawer__panel');
		var closers = drawer.querySelectorAll('[data-fbg-drawer-close]');
		var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var lastFocus = null;

		function openDrawer() {
			lastFocus = document.activeElement;
			drawer.removeAttribute('hidden');
			drawer.setAttribute('aria-hidden', 'false');
			document.body.classList.add('fbg-nav-open');
			btn.setAttribute('aria-expanded', 'true');
			btn.setAttribute('aria-label', btn.getAttribute('data-label-close') || 'Close menu');
			requestAnimationFrame(function () {
				drawer.classList.add('is-open');
			});
			var c = drawer.querySelector('.fbg-nav-drawer__close');
			if (c) {
				setTimeout(function () {
					c.focus();
				}, reduced ? 0 : 100);
			}
		}

		function finishClose() {
			drawer.setAttribute('hidden', 'hidden');
			drawer.setAttribute('aria-hidden', 'true');
			drawer.classList.remove('is-open');
			document.body.classList.remove('fbg-nav-open');
			btn.setAttribute('aria-expanded', 'false');
			btn.setAttribute('aria-label', btn.getAttribute('data-label-open') || 'Open menu');
			if (lastFocus && typeof lastFocus.focus === 'function') {
				lastFocus.focus();
			}
		}

		function closeDrawer() {
			if (drawer.hasAttribute('hidden')) {
				document.body.classList.remove('fbg-nav-open');
				btn.setAttribute('aria-expanded', 'false');
				return;
			}
			drawer.classList.remove('is-open');
			if (reduced || !panel) {
				finishClose();
				return;
			}
			var done = false;
			function onEnd(e) {
				if (e.propertyName !== 'transform' || done) return;
				done = true;
				panel.removeEventListener('transitionend', onEnd);
				finishClose();
			}
			panel.addEventListener('transitionend', onEnd);
			setTimeout(function () {
				if (!done) {
					done = true;
					panel.removeEventListener('transitionend', onEnd);
					finishClose();
				}
			}, 400);
		}

		btn.addEventListener('click', function () {
			if (drawer.hasAttribute('hidden')) {
				openDrawer();
			} else {
				closeDrawer();
			}
		});

		closers.forEach(function (el) {
			el.addEventListener('click', function (e) {
				e.preventDefault();
				closeDrawer();
			});
		});

		drawer.querySelectorAll('a[href]').forEach(function (link) {
			link.addEventListener('click', function () {
				if (window.matchMedia('(max-width: 1023px)').matches) {
					closeDrawer();
				}
			});
		});

		document.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape') return;
			if (drawer.hasAttribute('hidden')) return;
			closeDrawer();
		});
	}

	document.querySelectorAll('.nav-hamburger').forEach(function (btn) {
		var id = btn.getAttribute('aria-controls');
		var drawer = id ? document.getElementById(id) : null;
		setupDrawer(btn, drawer);
	});

	var stats = document.getElementById('fbg-stats-bar');
	if (stats && 'IntersectionObserver' in window) {
		var done = false;
		var obs = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (e) {
					if (!e.isIntersecting || done) return;
					done = true;
					stats.querySelectorAll('.fbg-stat__num').forEach(function (el) {
						var target = parseFloat(el.getAttribute('data-target'));
						if (isNaN(target)) target = 0;
						var suffix = el.getAttribute('data-suffix') || '';
						var dec = String(target).indexOf('.') >= 0;
						var start = 0;
						var dur = 1200;
						var t0 = null;
						function step(ts) {
							if (!t0) t0 = ts;
							var p = Math.min(1, (ts - t0) / dur);
							var val = start + (target - start) * p;
							el.textContent = dec ? val.toFixed(1) : Math.floor(val).toLocaleString();
							if (suffix) el.textContent += suffix;
							if (p < 1) requestAnimationFrame(step);
						}
						requestAnimationFrame(step);
					});
				});
			},
			{ threshold: 0.2 }
		);
		obs.observe(stats);
	}

	var grid = document.getElementById('fbg-post-grid');
	var loadBtn = document.getElementById('fbg-load-more');
	var endMsg = document.getElementById('fbg-end-results');
	if (grid && loadBtn && typeof fbgMain !== 'undefined') {
		var paged = 1;
		var maxPages = parseInt(grid.getAttribute('data-max') || '1', 10);
		var niche = 'all';
		var order = 'newest';
		var searchQ = '';

		function fetchPosts(append) {
			var fd = new FormData();
			fd.append('action', 'fbg_archive_posts');
			fd.append('nonce', fbgMain.nonce);
			fd.append('paged', String(paged));
			fd.append('niche', niche);
			fd.append('order', order);
			fd.append('s', searchQ);
			loadBtn.disabled = true;
			fetch(fbgMain.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function (r) {
					return r.json();
				})
				.then(function (json) {
					loadBtn.disabled = false;
					if (!json || !json.success) return;
					if (append) {
						var div = document.createElement('div');
						div.innerHTML = json.data.html;
						while (div.firstChild) {
							grid.appendChild(div.firstChild);
						}
					} else {
						grid.innerHTML = json.data.html;
					}
					maxPages = json.data.max_pages || 1;
					if (paged >= maxPages) {
						loadBtn.hidden = true;
						if (endMsg) endMsg.hidden = false;
					} else {
						loadBtn.hidden = false;
						if (endMsg) endMsg.hidden = true;
					}
				})
				.catch(function () {
					loadBtn.disabled = false;
				});
		}

		document.querySelectorAll('.fbg-pill').forEach(function (pill) {
			pill.addEventListener('click', function () {
				document.querySelectorAll('.fbg-pill').forEach(function (p) {
					p.classList.remove('is-active');
				});
				pill.classList.add('is-active');
				niche = pill.getAttribute('data-niche') || 'all';
				paged = 1;
				fetchPosts(false);
			});
		});

		var orderEl = document.getElementById('fbg-order');
		if (orderEl) {
			orderEl.addEventListener('change', function () {
				order = orderEl.value;
				paged = 1;
				fetchPosts(false);
			});
		}

		var searchEl = document.getElementById('fbg-search-archive');
		if (searchEl) {
			var t;
			searchEl.addEventListener('input', function () {
				clearTimeout(t);
				t = setTimeout(function () {
					searchQ = searchEl.value;
					paged = 1;
					fetchPosts(false);
				}, 300);
			});
		}

		loadBtn.addEventListener('click', function () {
			paged += 1;
			fetchPosts(true);
		});
	}
})();
