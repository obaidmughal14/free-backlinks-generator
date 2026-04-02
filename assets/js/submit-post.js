(function () {
	'use strict';

	if (typeof fbgSubmit === 'undefined') return;

	var maxLinks = parseInt(fbgSubmit.maxLinks, 10) || 3;
	var rows = document.getElementById('fbg-backlinks-rows');
	var addBtn = document.getElementById('fbg-add-link');
	var form = document.getElementById('fbg-submit-form');
	var alertEl = document.getElementById('fbg-submit-alert');
	var mainEl = document.getElementById('main-content');
	var lastSubmitMode = 'submit';

	function rowHtml(i) {
		return (
			'<div class="fbg-link-row" data-i="' +
			i +
			'">' +
			'<input type="text" name="backlinks[' +
			i +
			'][anchor]" placeholder="' +
			( fbgSubmit.anchorPh || 'Anchor text' ) +
			'">' +
			'<input type="url" name="backlinks[' +
			i +
			'][url]" placeholder="https://">' +
			'<button type="button" class="fbg-remove-row" aria-label="Remove">✕</button>' +
			'</div>'
		);
	}

	var idx = 0;
	function addRow() {
		if (rows.children.length >= maxLinks) return;
		rows.insertAdjacentHTML('beforeend', rowHtml(idx++));
	}
	addRow();
	addBtn.addEventListener('click', addRow);
	rows.addEventListener('click', function (e) {
		if (e.target.classList.contains('fbg-remove-row')) {
			var row = e.target.closest('.fbg-link-row');
			if (row) row.remove();
		}
	});

	document.getElementById('fbg-save-draft') &&
		document.getElementById('fbg-save-draft').addEventListener('click', function () {
			lastSubmitMode = 'draft';
		});
	document.getElementById('fbg-submit-review') &&
		document.getElementById('fbg-submit-review').addEventListener('click', function () {
			lastSubmitMode = 'submit';
		});

	var title = document.getElementById('fbg-post-title');
	var tp = document.getElementById('fbg-title-preview');
	var tc = document.getElementById('fbg-title-count');
	if (title) {
		title.addEventListener('input', function () {
			var blog = document.querySelector('meta[property="og:site_name"]');
			var name = blog ? blog.getAttribute('content') : '';
			if (tp) tp.textContent = title.value + (name ? ' | ' + name : '');
			if (tc) {
				var n = title.value.length;
				tc.textContent = n + '/70';
				tc.className = 'fbg-char-count' + (n >= 40 && n <= 70 ? ' is-good' : ' is-bad');
			}
		});
	}

	function getEditorContent() {
		if (typeof tinymce !== 'undefined' && tinymce.get('fbg_post_content')) {
			return tinymce.get('fbg_post_content').getContent();
		}
		var ta = document.getElementById('fbg_post_content');
		return ta ? ta.value : '';
	}

	function wordCount(html) {
		var div = document.createElement('div');
		div.innerHTML = html;
		var text = (div.textContent || '').replace(/\s+/g, ' ').trim();
		if (!text) return 0;
		return text.split(/\s+/).filter(Boolean).length;
	}

	var wcEl = document.getElementById('fbg-word-count');
	function updWc() {
		var n = wordCount(getEditorContent());
		if (wcEl) {
			wcEl.textContent = n + ' / 200 minimum';
			wcEl.className = 'fbg-char-count' + (n >= 500 ? ' is-good' : n >= 200 ? ' is-warn' : ' is-bad');
		}
	}
	setInterval(updWc, 800);

	var ex = document.getElementById('fbg-excerpt');
	var exc = document.getElementById('fbg-excerpt-count');
	if (ex && exc) {
		ex.addEventListener('input', function () {
			exc.textContent = ex.value.length + '/160';
		});
	}

	var featId = document.getElementById('fbg-featured-id');
	var featBtn = document.getElementById('fbg-featured-btn');
	var featPrev = document.getElementById('fbg-featured-preview');
	if (featBtn && typeof wp !== 'undefined' && wp.media) {
		featBtn.addEventListener('click', function (e) {
			e.preventDefault();
			var frame = wp.media({ title: 'Featured image', multiple: false, library: { type: 'image' } });
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				featId.value = att.id;
				featPrev.innerHTML =
					'<img src="' + att.url.replace(/"/g, '&quot;') + '" alt="" style="max-width:220px;height:auto;border-radius:8px">';
			});
			frame.open();
		});
	} else if (featBtn) {
		featBtn.addEventListener('click', function () {
			if (alertEl) {
				alertEl.textContent =
					fbgSubmit.mediaError ||
					'Media library could not load. Refresh the page or contact support.';
				alertEl.hidden = false;
			}
		});
	}

	function showError(msg) {
		if (!alertEl) {
			window.alert(msg);
			return;
		}
		alertEl.textContent = msg;
		alertEl.hidden = false;
		alertEl.focus();
	}

	function setLoading(loading) {
		if (mainEl) mainEl.classList.toggle('is-loading', loading);
		var btns = form ? form.querySelectorAll('button[type="submit"]') : [];
		for (var b = 0; b < btns.length; b++) {
			btns[b].disabled = loading;
		}
	}

	function parseJsonMessage(data) {
		if (!data) return fbgSubmit.genericError || 'Something went wrong. Please try again.';
		if (typeof data === 'string') return data;
		if (data.message) return data.message;
		return fbgSubmit.genericError || 'Something went wrong. Please try again.';
	}

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		if (alertEl) alertEl.hidden = true;

		var isDraft = false;
		if (typeof e.submitter !== 'undefined' && e.submitter) {
			isDraft = e.submitter.getAttribute('value') === 'draft';
		} else {
			isDraft = lastSubmitMode === 'draft';
		}

		var fd = new FormData(form);
		fd.append('action', 'fbg_submit_post');
		fd.append('nonce', fbgSubmit.nonce);
		fd.set('content', getEditorContent());
		fd.append('status', isDraft ? 'draft' : 'submit');

		setLoading(true);

		fetch(fbgSubmit.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
			.then(function (r) {
				return r.text().then(function (text) {
					var json = null;
					try {
						json = text ? JSON.parse(text) : null;
					} catch (err) {
						throw new Error(
							fbgSubmit.parseError ||
								'The server returned an unexpected response. If you are logged out, log in and try again.'
						);
					}
					if (!r.ok) {
						throw new Error(parseJsonMessage(json && json.data));
					}
					return json;
				});
			})
			.then(function (json) {
				if (json && json.success && json.data && json.data.redirect) {
					window.location.href = json.data.redirect;
					return;
				}
				if (json && json.success === false) {
					showError(parseJsonMessage(json.data));
					return;
				}
				showError(fbgSubmit.genericError || 'Could not save your post.');
			})
			.catch(function (err) {
				showError(err.message || (fbgSubmit.networkError || 'Network error. Check your connection and try again.'));
			})
			.finally(function () {
				setLoading(false);
			});
	});
})();
