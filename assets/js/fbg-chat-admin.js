(function () {
	'use strict';

	var desk = document.getElementById('fbg-chat-desk');
	if (!desk || typeof fbgChatAdmin === 'undefined') {
		return;
	}

	var listEl = document.getElementById('fbg-chat-desk-list');
	var threadEl = document.getElementById('fbg-chat-desk-thread');
	var actionsEl = document.getElementById('fbg-chat-desk-actions');
	var composerEl = document.getElementById('fbg-chat-desk-composer');
	var inputEl = document.getElementById('fbg-chat-agent-input');
	var sendBtn = document.getElementById('fbg-chat-agent-send');
	var claimBtn = document.getElementById('fbg-chat-claim');
	var closeBtn = document.getElementById('fbg-chat-close');

	var tab = desk.getAttribute('data-tab') || 'active';
	var preselect = parseInt(desk.getAttribute('data-preselect') || '0', 10) || 0;
	var currentId = 0;
	var afterId = 0;
	var pollTimer = null;
	var listTimer = null;

	function str(k, fb) {
		return (fbgChatAdmin.strings && fbgChatAdmin.strings[k]) || fb || '';
	}

	function post(action, data) {
		var fd = new FormData();
		fd.append('action', action);
		fd.append('nonce', fbgChatAdmin.nonce);
		Object.keys(data || {}).forEach(function (key) {
			fd.append(key, data[key]);
		});
		return fetch(fbgChatAdmin.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' }).then(function (r) {
			return r.json();
		});
	}

	function esc(s) {
		var d = document.createElement('div');
		d.textContent = s;
		return d.innerHTML;
	}

	function renderList(rows) {
		listEl.innerHTML = '';
		if (!rows || !rows.length) {
			listEl.innerHTML = '<p class="description">' + esc(str('empty', 'None')) + '</p>';
			return;
		}
		rows.forEach(function (s) {
			var a = document.createElement('button');
			a.type = 'button';
			a.className = 'fbg-chat-desk__item' + (currentId === s.id ? ' is-active' : '');
			a.textContent = (s.visitor_name || s.visitor_email || 'ID ' + s.id) + ' · ' + s.updated_at;
			a.addEventListener('click', function () {
				selectSession(s.id);
			});
			listEl.appendChild(a);
		});
	}

	function loadList() {
		post('fbg_chat_agent_sessions', { list: tab }).then(function (json) {
			if (json && json.success && json.data && json.data.sessions) {
				renderList(json.data.sessions);
				if (preselect && !currentId) {
					var found = json.data.sessions.some(function (s) {
						return s.id === preselect;
					});
					if (found) {
						selectSession(preselect);
					}
				}
			}
		});
	}

	function appendLine(m) {
		var div = document.createElement('div');
		div.className = 'fbg-chat-desk__line fbg-chat-desk__line--' + m.sender;
		var who = m.sender === 'agent' ? str('you', 'You') : m.sender === 'system' ? str('system', 'System') : str('visitor', 'Visitor');
		div.innerHTML = '<strong>' + esc(who) + '</strong> <span class="description">' + esc(m.created_at) + '</span><div>' + m.body + '</div>';
		threadEl.appendChild(div);
		threadEl.scrollTop = threadEl.scrollHeight;
		afterId = Math.max(afterId, m.id);
	}

	function selectSession(id) {
		currentId = id;
		afterId = 0;
		threadEl.innerHTML = '';
		actionsEl.hidden = false;
		composerEl.hidden = false;
		loadList();
		pollSession();
		if (pollTimer) {
			clearInterval(pollTimer);
		}
		pollTimer = setInterval(pollSession, 3000);
	}

	function pollSession() {
		if (!currentId) {
			return;
		}
		post('fbg_chat_agent_poll', { session_id: String(currentId), after_id: String(afterId) }).then(function (json) {
			if (!json || !json.success || !json.data) {
				return;
			}
			var msgs = json.data.messages || [];
			msgs.forEach(appendLine);
		});
	}

	claimBtn.addEventListener('click', function () {
		if (!currentId) {
			return;
		}
		post('fbg_chat_agent_claim', { session_id: String(currentId) }).then(function () {
			pollSession();
			loadList();
		});
	});

	closeBtn.addEventListener('click', function () {
		if (!currentId || !window.confirm('Close this chat for the visitor?')) {
			return;
		}
		post('fbg_chat_agent_close', { session_id: String(currentId) }).then(function () {
			currentId = 0;
			threadEl.innerHTML = '';
			actionsEl.hidden = true;
			composerEl.hidden = true;
			if (pollTimer) {
				clearInterval(pollTimer);
				pollTimer = null;
			}
			loadList();
		});
	});

	sendBtn.addEventListener('click', function () {
		var t = inputEl.value.trim();
		if (!t || !currentId) {
			return;
		}
		sendBtn.disabled = true;
		post('fbg_chat_agent_send', { session_id: String(currentId), message: t }).then(function (json) {
			sendBtn.disabled = false;
			if (json && json.success && json.data && json.data.message) {
				appendLine(json.data.message);
				inputEl.value = '';
			}
		}).catch(function () {
			sendBtn.disabled = false;
		});
	});

	function pulse() {
		post('fbg_chat_agent_pulse', {});
	}

	pulse();
	setInterval(pulse, 30000);
	loadList();
	setInterval(loadList, 8000);
})();
