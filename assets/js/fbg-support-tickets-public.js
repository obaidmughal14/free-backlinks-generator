(function () {
	'use strict';

	if (typeof fbgSupportTicket === 'undefined') {
		return;
	}

	var form = document.getElementById('fbg-support-ticket-form');
	var msg = document.getElementById('fbg-support-ticket-feedback');
	var btn = document.getElementById('fbg-support-ticket-submit');
	if (!form || !msg || !btn) {
		return;
	}

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		msg.hidden = true;
		msg.className = 'fbg-ticket-form__feedback';
		var fd = new FormData(form);
		fd.append('action', 'fbg_support_ticket_create');
		fd.append('nonce', fbgSupportTicket.nonce);
		var orig = btn.textContent;
		btn.disabled = true;
		btn.textContent = fbgSupportTicket.strings.sending || '…';

		fetch(fbgSupportTicket.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
			.then(function (r) {
				return r.json();
			})
			.then(function (json) {
				msg.hidden = false;
				if (json.success) {
					msg.classList.add('fbg-ticket-form__feedback--ok');
					msg.textContent =
						(json.data && json.data.message) ||
						fbgSupportTicket.strings.sent ||
						'OK';
					form.reset();
				} else {
					msg.classList.add('fbg-ticket-form__feedback--err');
					msg.textContent =
						(json.data && json.data.message) ||
						fbgSupportTicket.strings.error ||
						'Error';
				}
			})
			.catch(function () {
				msg.hidden = false;
				msg.classList.add('fbg-ticket-form__feedback--err');
				msg.textContent = fbgSupportTicket.strings.error || 'Error';
			})
			.finally(function () {
				btn.disabled = false;
				btn.textContent = orig;
			});
	});
})();
