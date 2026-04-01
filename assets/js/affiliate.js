(function () {
	'use strict';

	if (typeof fbgAffiliate === 'undefined') return;

	var form = document.getElementById('fbg-affiliate-form');
	var msg = document.getElementById('fbg-aff-message');
	var btn = document.getElementById('fbg-aff-submit');
	if (!form || !msg || !btn) return;

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		msg.hidden = true;
		msg.className = 'fbg-aff-alert';
		var fd = new FormData(form);
		fd.append('action', 'fbg_affiliate_apply');
		fd.append('nonce', fbgAffiliate.nonce);
		var orig = btn.textContent;
		btn.disabled = true;
		btn.textContent = fbgAffiliate.strings.sending || '…';

		fetch(fbgAffiliate.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
			.then(function (r) {
				return r.text().then(function (text) {
					try {
						return text ? JSON.parse(text) : {};
					} catch (err) {
						throw new Error('parse');
					}
				});
			})
			.then(function (json) {
				msg.hidden = false;
				if (json.success) {
					msg.classList.add('fbg-aff-alert--ok');
					msg.textContent = fbgAffiliate.strings.sent;
					form.reset();
				} else {
					msg.classList.add('fbg-aff-alert--err');
					msg.textContent =
						(json.data && json.data.message) ||
						fbgAffiliate.strings.error ||
						'Error';
				}
			})
			.catch(function () {
				msg.hidden = false;
				msg.classList.add('fbg-aff-alert--err');
				msg.textContent = fbgAffiliate.strings.error;
			})
			.finally(function () {
				btn.disabled = false;
				btn.textContent = orig;
			});
	});
})();
