(function () {
	'use strict';

	if (typeof fbgAuthData === 'undefined') return;

	function show(el, msg) {
		if (!el) return;
		el.textContent = msg || '';
		el.hidden = !msg;
	}

	var regForm = document.getElementById('fbg-register-form');
	if (regForm) {
		var alertEl = document.getElementById('fbg-register-alert');
		var meter = document.getElementById('fbg-pass-meter');
		var pass = document.getElementById('fbg-reg-pass');
		var label = document.getElementById('fbg-pass-label');
		pass.addEventListener('input', function () {
			var s = pass.value;
			var score = 0;
			if (s.length >= 8) score++;
			if (/[A-Z]/.test(s)) score++;
			if (/[0-9]/.test(s)) score++;
			if (/[^A-Za-z0-9]/.test(s)) score++;
			if (meter) meter.value = score;
			var txt = ['Weak', 'Fair', 'Strong', 'Very Strong'][Math.min(3, Math.max(0, score - 1))] || 'Weak';
			if (label) label.textContent = txt;
		});
		regForm.addEventListener('submit', function (e) {
			e.preventDefault();
			show(alertEl, '');
			var fd = new FormData(regForm);
			fd.append('action', 'fbg_register');
			fd.append('nonce', fbgAuthData.registerNonce);
			fetch(fbgAuthData.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function (r) {
					return r.json();
				})
				.then(function (json) {
					if (json.success && json.data.redirect) {
						window.location = json.data.redirect;
					} else if (json.data && json.data.message) {
						show(alertEl, json.data.message);
					} else if (json.data && json.data.field) {
						show(alertEl, json.data.message || 'Error');
					}
				});
		});
	}

	var loginForm = document.getElementById('fbg-login-form');
	if (loginForm) {
		var la = document.getElementById('fbg-login-alert');
		loginForm.addEventListener('submit', function (e) {
			e.preventDefault();
			show(la, '');
			var fd = new FormData(loginForm);
			fd.append('action', 'fbg_login');
			fd.append('nonce', fbgAuthData.loginNonce);
			fetch(fbgAuthData.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function (r) {
					return r.json();
				})
				.then(function (json) {
					if (json.success && json.data.redirect) {
						window.location = json.data.redirect;
					} else if (json.data && json.data.message) {
						show(la, json.data.message);
					}
				});
		});
	}

	var forgotForm = document.getElementById('fbg-forgot-form');
	if (forgotForm) {
		var fa = document.getElementById('fbg-forgot-alert');
		forgotForm.addEventListener('submit', function (e) {
			e.preventDefault();
			show(fa, '');
			var fd = new FormData(forgotForm);
			fd.append('action', 'fbg_lost_password');
			fd.append('nonce', fbgAuthData.forgotNonce);
			var email = fd.get('email');
			fetch(fbgAuthData.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function (r) {
					return r.json();
				})
				.then(function () {
					window.location =
						window.location.pathname + '?step=sent&email=' + encodeURIComponent(email || '');
				});
		});
	}

	var resetForm = document.getElementById('fbg-reset-form');
	if (resetForm) {
		var ra = document.getElementById('fbg-reset-alert');
		resetForm.addEventListener('submit', function (e) {
			e.preventDefault();
			show(ra, '');
			var fd = new FormData(resetForm);
			fd.append('action', 'fbg_reset_password');
			fd.append('nonce', fbgAuthData.resetNonce);
			fetch(fbgAuthData.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function (r) {
					return r.json();
				})
				.then(function (json) {
					if (json.success && json.data.redirect) {
						window.location = json.data.redirect;
					} else if (json.data && json.data.message) {
						show(ra, json.data.message);
					}
				});
		});
	}
})();
