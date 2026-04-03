(function () {
	'use strict';

	if (typeof fbgLiveChat === 'undefined') {
		return;
	}

	function el(id) {
		return document.getElementById(id);
	}

	function guestKey() {
		var k = 'fbg_chat_guest_key';
		try {
			var v = localStorage.getItem(k);
			if (v && v.length >= 12) {
				return v;
			}
			v = 'g' + Math.random().toString(36).slice(2) + Date.now().toString(36);
			localStorage.setItem(k, v);
			return v;
		} catch (e) {
			return 'g' + Math.random().toString(36).slice(2) + Date.now().toString(36);
		}
	}

	var root = el('fbg-live-chat-root');
	if (!root) {
		return;
	}

	var toggle = el('fbg-live-chat-toggle');
	var panel = el('fbg-live-chat-panel');
	var closeBtn = el('fbg-live-chat-close');
	var preform = el('fbg-live-chat-preform');
	var thread = el('fbg-live-chat-thread');
	var composer = el('fbg-live-chat-composer');
	var input = el('fbg-live-chat-input');
	var sendBtn = el('fbg-live-chat-send');
	var startBtn = el('fbg-live-chat-start');
	var statusEl = el('fbg-live-chat-status');
	var nameIn = el('fbg-live-chat-name');
	var emailIn = el('fbg-live-chat-email');

	var sessionId = 0;
	var suggested = root.getAttribute('data-user-key') || '';
	var gkey = suggested.length >= 2 ? suggested : guestKey();
	var afterId = 0;
	var pollTimer = null;
	var loggedIn = root.getAttribute('data-logged-in') === '1';

	function str(key, fb) {
		return (fbgLiveChat.strings && fbgLiveChat.strings[key]) || fb || '';
	}

	function setStatus(t) {
		if (statusEl) {
			statusEl.textContent = t || '';
		}
	}

	function esc(s) {
		var d = document.createElement('div');
		d.textContent = s;
		return d.innerHTML;
	}

	function appendMsg(m) {
		if (!thread) {
			return;
		}
		var div = document.createElement('div');
		div.className = 'fbg-live-chat__msg fbg-live-chat__msg--' + (m.sender || 'visitor');
		var label = str('you', 'You');
		if (m.sender === 'agent') {
			label = str('support', 'Support');
		} else if (m.sender === 'system') {
			label = str('system', 'Notice');
		} else {
			label = str('you', 'You');
		}
		div.innerHTML = '<span class="fbg-live-chat__msg-meta">' + esc(label) + '</span><div class="fbg-live-chat__msg-body">' + m.body + '</div>';
		thread.appendChild(div);
		thread.scrollTop = thread.scrollHeight;
		afterId = Math.max(afterId, m.id);
	}

	function post(action, data) {
		var fd = new FormData();
		fd.append('action', action);
		fd.append('nonce', fbgLiveChat.nonce);
		Object.keys(data || {}).forEach(function (k) {
			fd.append(k, data[k]);
		});
		return fetch(fbgLiveChat.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' }).then(function (r) {
			return r.json();
		});
	}

	function poll() {
		if (!sessionId || !gkey) {
			return;
		}
		post('fbg_chat_poll', { session_id: String(sessionId), guest_key: gkey, after_id: String(afterId) }).then(function (json) {
			if (!json || !json.success || !json.data) {
				return;
			}
			var d = json.data;
			if (d.messages && d.messages.length) {
				d.messages.forEach(appendMsg);
			}
			if (!d.agent_online && d.messages && d.messages.length) {
				setStatus(str('offline', ''));
			} else if (d.agent_online) {
				setStatus('');
			}
			if (d.status === 'closed') {
				setStatus(str('closed', ''));
				if (composer) {
					composer.hidden = true;
				}
				stopPoll();
			}
		}).catch(function () {});
	}

	function stopPoll() {
		if (pollTimer) {
			clearInterval(pollTimer);
			pollTimer = null;
		}
	}

	function startPoll() {
		stopPoll();
		pollTimer = setInterval(poll, 4000);
	}

	function openPanel() {
		panel.hidden = false;
		toggle.setAttribute('aria-expanded', 'true');
	}

	function closePanel() {
		panel.hidden = true;
		toggle.setAttribute('aria-expanded', 'false');
	}

	toggle.addEventListener('click', function () {
		if (panel.hidden) {
			openPanel();
		} else {
			closePanel();
		}
	});
	closeBtn.addEventListener('click', closePanel);

	startBtn.addEventListener('click', function () {
		var name = loggedIn ? '' : (nameIn && nameIn.value ? nameIn.value.trim() : '');
		var email = loggedIn ? '' : (emailIn && emailIn.value ? emailIn.value.trim() : '');
		if (!loggedIn && name.length < 2) {
			setStatus(str('name', 'Name') + ' required');
			return;
		}
		setStatus(str('connecting', '…'));
		startBtn.disabled = true;
		post('fbg_chat_init', {
			guest_key: gkey,
			visitor_name: name,
			visitor_email: email,
		})
			.then(function (json) {
				startBtn.disabled = false;
				if (!json || !json.success) {
					setStatus((json && json.data && json.data.message) || str('error', 'Error'));
					return;
				}
				var d = json.data;
				sessionId = d.session_id;
				gkey = d.guest_key || gkey;
				preform.hidden = true;
				thread.hidden = false;
				composer.hidden = false;
				thread.innerHTML = '';
				afterId = 0;
				if (d.messages && d.messages.length) {
					d.messages.forEach(appendMsg);
				}
				if (!d.agent_online) {
					setStatus(str('offline', ''));
				} else {
					setStatus('');
				}
				startPoll();
				poll();
			})
			.catch(function () {
				startBtn.disabled = false;
				setStatus(str('error', 'Error'));
			});
	});

	sendBtn.addEventListener('click', function () {
		var text = input.value.trim();
		if (!text || !sessionId) {
			return;
		}
		sendBtn.disabled = true;
		post('fbg_chat_send_visitor', {
			session_id: String(sessionId),
			guest_key: gkey,
			message: text,
		})
			.then(function (json) {
				sendBtn.disabled = false;
				if (json && json.success && json.data && json.data.message) {
					appendMsg(json.data.message);
					input.value = '';
				}
			})
			.catch(function () {
				sendBtn.disabled = false;
			});
	});
})();
