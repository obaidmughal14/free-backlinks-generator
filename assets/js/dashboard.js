(function () {
	'use strict';

	if (typeof fbgDash === 'undefined') return;

	var dash = document.getElementById('fbg-dashboard');
	if (!dash) return;

	function post(action, data) {
		var fd = new FormData();
		fd.append('action', action);
		fd.append('nonce', fbgDash.nonce);
		Object.keys(data || {}).forEach(function (k) {
			fd.append(k, data[k]);
		});
		return fetch(fbgDash.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' }).then(function (r) {
			return r.json();
		});
	}

	document.querySelectorAll('.fbg-dash-nav a[data-tab]').forEach(function (a) {
		a.addEventListener('click', function (e) {
			e.preventDefault();
			var tab = a.getAttribute('data-tab');
			document.querySelectorAll('.fbg-dash-nav a').forEach(function (x) {
				x.classList.remove('is-active');
			});
			a.classList.add('is-active');
			document.querySelectorAll('.fbg-dash-panel').forEach(function (p) {
				var match = p.getAttribute('data-panel') === tab;
				p.classList.toggle('is-active', match);
				p.hidden = !match;
			});
			history.replaceState(null, '', '#' + tab);
		});
	});

	document.querySelectorAll('[data-tab-trigger]').forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			var tab = btn.getAttribute('data-tab-trigger');
			var link = document.querySelector('.fbg-dash-nav a[data-tab="' + tab + '"]');
			if (link) link.click();
		});
	});

	var hash = (location.hash || '#overview').replace('#', '');
	var start = document.querySelector('.fbg-dash-nav a[data-tab="' + hash + '"]');
	if (start) start.click();

	var toggle = document.querySelector('.fbg-dash-toggle');
	var side = document.getElementById('fbg-dash-sidebar');
	if (toggle && side) {
		toggle.addEventListener('click', function () {
			var open = side.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}

	var table = document.getElementById('fbg-posts-table');
	if (table) {
		var rows = table.querySelectorAll('tbody tr[data-status]');
		var cAll = document.getElementById('fbg-count-all');
		var cAp = document.getElementById('fbg-count-approved');
		var cPe = document.getElementById('fbg-count-pending');
		var cRe = document.getElementById('fbg-count-rejected');
		function count() {
			var a = 0,
				ap = 0,
				pe = 0,
				re = 0;
			rows.forEach(function (r) {
				if (r.classList.contains('fbg-reject-row')) return;
				a++;
				var st = r.getAttribute('data-status');
				if (st === 'approved') ap++;
				if (st === 'pending') pe++;
				if (st === 'rejected') re++;
			});
			if (cAll) cAll.textContent = String(a);
			if (cAp) cAp.textContent = String(ap);
			if (cPe) cPe.textContent = String(pe);
			if (cRe) cRe.textContent = String(re);
		}
		count();
		var search = document.getElementById('fbg-posts-search');
		if (search) {
			search.addEventListener('input', function () {
				var q = search.value.toLowerCase();
				rows.forEach(function (r) {
					if (r.classList.contains('fbg-reject-row')) return;
					var t = (r.getAttribute('data-title') || '').toLowerCase();
					r.style.display = !q || t.indexOf(q) >= 0 ? '' : 'none';
				});
			});
		}
		document.querySelectorAll('.fbg-filter-tabs button').forEach(function (btn) {
			btn.addEventListener('click', function () {
				document.querySelectorAll('.fbg-filter-tabs button').forEach(function (b) {
					b.classList.remove('is-active');
				});
				btn.classList.add('is-active');
				var f = btn.getAttribute('data-filter');
				rows.forEach(function (r) {
					if (r.classList.contains('fbg-reject-row')) {
						r.style.display = f === 'rejected' || f === 'all' ? '' : 'none';
						return;
					}
					var st = r.getAttribute('data-status');
					r.style.display = f === 'all' || st === f ? '' : 'none';
				});
			});
		});
		document.querySelectorAll('.fbg-link-delete').forEach(function (b) {
			b.addEventListener('click', function () {
				if (!confirm('Delete this post?')) return;
				post('fbg_delete_my_post', { post_id: b.getAttribute('data-id') }).then(function (j) {
					if (j.success) location.reload();
				});
			});
		});
		var bulk = document.getElementById('fbg-bulk-delete');
		if (bulk) {
			bulk.addEventListener('click', function () {
				var ids = Array.from(table.querySelectorAll('input[name="ids[]"]:checked')).map(function (x) {
					return x.value;
				});
				if (!ids.length || !confirm('Delete selected?')) return;
				var fd = new FormData();
				fd.append('action', 'fbg_bulk_delete_posts');
				fd.append('nonce', fbgDash.nonce);
				ids.forEach(function (id) {
					fd.append('post_ids[]', id);
				});
				fetch(fbgDash.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
					.then(function (r) {
						return r.json();
					})
					.then(function (j) {
						if (j.success) location.reload();
					});
			});
		}
	}

	var pf = document.getElementById('fbg-profile-form');
	if (pf) {
		var bio = document.getElementById('fbg-prof-bio');
		var bc = document.getElementById('fbg-bio-count');
		function updBio() {
			if (bio && bc) bc.textContent = bio.value.length + '/280';
		}
		if (bio) bio.addEventListener('input', updBio);
		updBio();
		pf.addEventListener('submit', function (e) {
			e.preventDefault();
			var fd = new FormData(pf);
			fd.append('action', 'fbg_save_profile');
			fd.append('nonce', fbgDash.nonce);
			fetch(fbgDash.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function (r) {
					return r.json();
				})
				.then(function (j) {
					var t = document.getElementById('fbg-profile-toast');
					if (t) {
						t.textContent = j.data && j.data.message ? j.data.message : '';
						t.hidden = false;
					}
				});
		});
	}

	var sf = document.getElementById('fbg-settings-form');
	if (sf) {
		sf.addEventListener('submit', function (e) {
			e.preventDefault();
			var fd = new FormData(sf);
			fd.append('action', 'fbg_save_settings');
			fd.append('nonce', fbgDash.nonce);
			fetch(fbgDash.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function (r) {
					return r.json();
				})
				.then(function (j) {
					var t = document.getElementById('fbg-settings-toast');
					if (t) {
						t.textContent = j.data && j.data.message ? j.data.message : '';
						t.hidden = false;
					}
				});
		});
	}

	document.getElementById('fbg-notify-read-all')?.addEventListener('click', function () {
		post('fbg_notifications_read_all', {}).then(function () {
			location.reload();
		});
	});

	document.querySelectorAll('.fbg-notify-item').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var id = btn.getAttribute('data-id');
			var url = btn.getAttribute('data-url');
			post('fbg_notification_read', { id: id }).then(function () {
				if (url) window.location = url;
			});
		});
	});

	document.getElementById('fbg-toggle-pass')?.addEventListener('click', function () {
		document.getElementById('fbg-pass-panel').hidden = false;
	});
	document.getElementById('fbg-save-pass')?.addEventListener('click', function () {
		post('fbg_change_password', {
			current: document.getElementById('fbg-cur-pass').value,
			new_password: document.getElementById('fbg-new-pass').value,
			confirm: document.getElementById('fbg-new-pass2').value,
		}).then(function (j) {
			alert(j.data && j.data.message ? j.data.message : 'OK');
		});
	});
	document.getElementById('fbg-delete-open')?.addEventListener('click', function () {
		document.getElementById('fbg-delete-modal').hidden = false;
	});
	document.getElementById('fbg-delete-cancel')?.addEventListener('click', function () {
		document.getElementById('fbg-delete-modal').hidden = true;
	});
	document.getElementById('fbg-delete-do')?.addEventListener('click', function () {
		post('fbg_delete_account', { confirm: document.getElementById('fbg-delete-confirm').value }).then(function (j) {
			if (j.success && j.data.redirect) window.location = j.data.redirect;
		});
	});

	function copyStrings() {
		var s = fbgDash && fbgDash.strings ? fbgDash.strings : {};
		return {
			copied: s.copied || 'Copied!',
			copyFailed: s.copyFailed || 'Could not copy',
		};
	}

	var payoutForm = document.getElementById('fbg-earn-payout-form');
	if (payoutForm) {
		payoutForm.addEventListener('submit', function (e) {
			e.preventDefault();
			var amtEl = document.getElementById('fbg-earn-payout-amount');
			var noteEl = document.getElementById('fbg-earn-payout-note');
			var toast = document.getElementById('fbg-earn-payout-toast');
			if (!amtEl) return;
			post('fbg_earn_request_payout', {
				amount: amtEl.value,
				note: noteEl ? noteEl.value : '',
			}).then(function (j) {
				var msg = '';
				if (j && j.data) {
					msg = typeof j.data === 'string' ? j.data : j.data.message || '';
				}
				if (!msg) {
					msg = j && j.success ? 'OK' : 'Error';
				}
				if (toast) {
					toast.textContent = msg;
					toast.hidden = false;
					toast.className =
						'fbg-alert ' + (j && j.success ? 'fbg-alert--success' : 'fbg-alert--error');
				}
				if (j && j.success) {
					payoutForm.reset();
					setTimeout(function () {
						location.reload();
					}, 900);
				}
			});
		});
	}

	document.querySelectorAll('[data-fbg-copy]').forEach(function (btn) {
		var sel = btn.getAttribute('data-fbg-copy');
		var orig = btn.textContent;
		btn.addEventListener('click', function () {
			var el = sel ? document.querySelector(sel) : null;
			var text = el && (el.value || el.textContent) ? String(el.value || el.textContent).trim() : '';
			if (!text) return;
			var str = copyStrings();
			function done(ok) {
				btn.textContent = ok ? str.copied : str.copyFailed;
				setTimeout(function () {
					btn.textContent = orig;
				}, 2200);
			}
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(text).then(function () {
					done(true);
				}).catch(function () {
					done(false);
				});
			} else {
				try {
					el.select();
					document.execCommand('copy');
					done(true);
				} catch (e) {
					done(false);
				}
			}
		});
	});
})();
