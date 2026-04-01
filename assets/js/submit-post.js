(function () {
	'use strict';

	if (typeof fbgSubmit === 'undefined') return;

	var maxLinks = parseInt(fbgSubmit.maxLinks, 10) || 3;
	var rows = document.getElementById('fbg-backlinks-rows');
	var addBtn = document.getElementById('fbg-add-link');
	var form = document.getElementById('fbg-submit-form');
	var alertEl = document.getElementById('fbg-submit-alert');

	function rowHtml(i) {
		return (
			'<div class="fbg-link-row" data-i="' +
			i +
			'">' +
			'<input type="text" name="backlinks[' +
			i +
			'][anchor]" placeholder="Anchor">' +
			'<input type="url" name="backlinks[' +
			i +
			'][url]" placeholder="https://">' +
			'<button type="button" class="fbg-remove-row">✕</button>' +
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
			e.target.closest('.fbg-link-row').remove();
		}
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
		var text = div.textContent || '';
		return text.trim().split(/\s+/).filter(Boolean).length;
	}

	var wcEl = document.getElementById('fbg-word-count');
	function updWc() {
		var n = wordCount(getEditorContent());
		if (wcEl) {
			wcEl.textContent = n + ' / 600 minimum';
			wcEl.className = 'fbg-char-count' + (n >= 800 ? ' is-good' : n >= 600 ? ' is-warn' : ' is-bad');
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
			var frame = wp.media({ title: 'Featured image', multiple: false });
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				featId.value = att.id;
				featPrev.innerHTML = '<img src="' + att.url + '" alt="" style="max-width:200px">';
			});
			frame.open();
		});
	}

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		alertEl.hidden = true;
		var submitter = e.submitter;
		var isDraft = submitter && submitter.getAttribute('value') === 'draft';
		var fd = new FormData(form);
		fd.append('action', 'fbg_submit_post');
		fd.append('nonce', fbgSubmit.nonce);
		fd.set('content', getEditorContent());
		fd.append('status', isDraft ? 'draft' : 'submit');
		fetch(fbgSubmit.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
			.then(function (r) {
				return r.json();
			})
			.then(function (json) {
				if (json.success && json.data.redirect) {
					window.location = json.data.redirect;
				} else if (json.data && json.data.message) {
					alertEl.textContent = json.data.message;
					alertEl.hidden = false;
				}
			});
	});
})();
