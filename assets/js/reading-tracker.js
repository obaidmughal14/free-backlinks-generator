(function () {
	'use strict';
	if (typeof fbgReading === 'undefined' || !fbgReading.postId) return;

	var postId = parseInt(fbgReading.postId, 10);
	var intervalMs = parseInt(fbgReading.intervalMs, 10) || 25000;
	var root = document.getElementById('fbg-read-tracker');
	if (!root || root.getAttribute('data-completed') === '1') {
		return;
	}

	var pendingSeconds = 0;
	var lastMark = Date.now();
	var bar = root ? root.querySelector('.fbg-read-tracker__bar') : null;
	var label = root ? root.querySelector('.fbg-read-tracker__label') : null;
	var required = parseInt(fbgReading.requiredSeconds, 10) || 120;

	function visible() {
		return document.visibilityState === 'visible';
	}

	function tickAccumulate() {
		if (!visible()) {
			lastMark = Date.now();
			return;
		}
		var now = Date.now();
		var delta = Math.min(45, Math.round((now - lastMark) / 1000));
		lastMark = now;
		if (delta > 0) {
			pendingSeconds += delta;
		}
	}

	setInterval(tickAccumulate, 4000);

	function send() {
		if (pendingSeconds < 1) return;
		var sendSec = Math.min(40, pendingSeconds);
		pendingSeconds -= sendSec;
		var fd = new FormData();
		fd.append('action', 'fbg_read_progress');
		fd.append('nonce', fbgReading.nonce);
		fd.append('post_id', String(postId));
		fd.append('active_seconds', String(sendSec));
		fetch(fbgReading.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
			.then(function (r) {
				return r.json();
			})
			.then(function (json) {
				if (!json || !json.success || !json.data) return;
				if (json.data.ignored) return;
				if (json.data.wait) return;
				var s = json.data.seconds;
				if (typeof s === 'number' && root) {
					var pct = Math.min(100, Math.round((s / required) * 100));
					if (bar) bar.style.width = pct + '%';
					if (label) {
						if (s >= required) {
							label.textContent = fbgReading.strings.done || '';
						} else {
							var minLeft = Math.max(1, Math.ceil((required - s) / 60));
							label.textContent = (fbgReading.strings.progress || '').replace('%d', String(minLeft));
						}
					}
					if (s >= required) {
						root.setAttribute('data-completed', '1');
					}
				}
			})
			.catch(function () {});
	}

	setInterval(send, intervalMs);
	document.addEventListener('visibilitychange', function () {
		lastMark = Date.now();
	});
	send();
})();
