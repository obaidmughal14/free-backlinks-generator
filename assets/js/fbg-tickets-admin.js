(function () {
	'use strict';

	if (typeof fbgTicketsAdmin === 'undefined') {
		return;
	}

	function post(action, data) {
		var fd = new FormData();
		fd.append('action', action);
		fd.append('nonce', fbgTicketsAdmin.nonce);
		Object.keys(data || {}).forEach(function (k) {
			fd.append(k, data[k]);
		});
		return fetch(fbgTicketsAdmin.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' }).then(function (r) {
			return r.json();
		});
	}

	var replyBtn = document.getElementById('fbg-ticket-send-reply');
	var replyTa = document.getElementById('fbg-ticket-reply-body');
	var replyMsg = document.getElementById('fbg-ticket-reply-msg');
	if (replyBtn && replyTa) {
		replyBtn.addEventListener('click', function () {
			var id = replyBtn.getAttribute('data-ticket-id');
			var text = replyTa.value.trim();
			if (!id || text.length < 5) {
				return;
			}
			replyBtn.disabled = true;
			post('fbg_support_ticket_reply', { ticket_id: id, reply: text }).then(function (json) {
				replyBtn.disabled = false;
				if (json && json.success) {
					replyMsg.textContent = json.data && json.data.message ? json.data.message : 'OK';
					replyTa.value = '';
					window.location.reload();
				} else {
					replyMsg.textContent = 'Error';
				}
			});
		});
	}

	var saveSt = document.getElementById('fbg-ticket-save-status');
	if (saveSt) {
		saveSt.addEventListener('click', function () {
			var id = saveSt.getAttribute('data-ticket-id');
			var st = document.getElementById('fbg-ticket-status');
			if (!id || !st) {
				return;
			}
			post('fbg_support_ticket_update', { ticket_id: id, status: st.value }).then(function () {
				window.location.reload();
			});
		});
	}

	var saveNotes = document.getElementById('fbg-ticket-save-notes');
	if (saveNotes) {
		saveNotes.addEventListener('click', function () {
			var id = saveNotes.getAttribute('data-ticket-id');
			var notes = document.getElementById('fbg-ticket-admin-notes');
			if (!id || !notes) {
				return;
			}
			post('fbg_support_ticket_notes', { ticket_id: id, admin_notes: notes.value }).then(function () {
				saveNotes.textContent = 'Saved';
				setTimeout(function () {
					saveNotes.textContent = 'Save notes';
				}, 1500);
			});
		});
	}
})();
