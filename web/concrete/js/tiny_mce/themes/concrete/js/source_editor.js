tinyMCEPopup.requireLangPack();
tinyMCEPopup.onInit.add(onLoadInit);

function saveContent() {
	if(codeMirror) {
		codeMirror.save();
	}
	tinyMCEPopup.editor.setContent(document.getElementById('htmlSource').value, {source_view : true});
	tinyMCEPopup.close();
}

var codeMirror = null;
function onLoadInit() {
	tinyMCEPopup.resizeToInnerSize();

	// Remove Gecko spellchecking
	if (tinymce.isGecko)
		document.body.spellcheck = tinyMCEPopup.editor.getParam("gecko_spellcheck");

	document.getElementById('htmlSource').value = tinyMCEPopup.editor.getContent({source_view : true});

	if (tinyMCEPopup.editor.getParam("theme_advanced_source_editor_wrap", true)) {
		setWrap('soft');
		document.getElementById('wraped').checked = true;
	}

	resizeInputs();
	loadCodeMirror(
		'../../../codemirror',
		['xml', 'javascript', 'css', 'htmlmixed', 'htmlembedded'],
		function() {
			codeMirror = CodeMirror.fromTextArea(
				document.getElementById('htmlSource'),
				{
					lineNumbers: true,
					mode: 'application/x-ejs',
					indentUnit: 4,
					indentWithTabs: true,
					enterMode: 'keep',
					tabMode: 'shift'
				}
			);
			resizeInputs();
			toggleWordWrap()
		}
	);
}

function setWrap(val) {
	if(codeMirror) {
		codeMirror.setOption('lineWrapping', val != 'off');
	}
	else {
		var v, n, s = document.getElementById('htmlSource');
	
		s.wrap = val;
	
		if (!tinymce.isIE) {
			v = s.value;
			n = s.cloneNode(false);
			n.setAttribute("wrap", val);
			s.parentNode.replaceChild(n, s);
			n.value = v;
		}
	}
}

function toggleWordWrap() {
	if (document.getElementById('wraped').checked)
		setWrap('soft');
	else
		setWrap('off');
}

function resizeInputs() {
	var vp = tinyMCEPopup.dom.getViewPort(window), width = Math.max(50, vp.w - 20), height = Math.max(50, vp.h - 100);

	if(codeMirror) {
		codeMirror.setSize(null, height);
	}
	else {
		var el = document.getElementById('htmlSource');
		el.style.width =  + 'px';
		el.style.height =  + 'px';
	}
}

function loadCodeMirror(codeMirrorPath, modes, callback) {
	function loadModes() {
		var scripts = [];
		if(modes) {
			$.each(modes, function(_, mode) {
				if(!(mode in CodeMirror.modes)) {
					switch(mode) {
						default:
							scripts.push(codeMirrorPath + '/mode/' + mode + '/' + mode + '.js');
							break;
					}
				}
			});
		}
		if(!scripts.length) {
			callback();
			return;
		}
		require(scripts, function() {
			callback();
		});
	}
	function loadMain() {
		$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', codeMirrorPath + '/lib/codemirror.css'));
		require([codeMirrorPath + '/lib/codemirror.js'], function() {
			loadModes();
		});
	}
	if($.type(window.CoreMirror) != 'function') {
		loadMain();
	}
	else {
		loadModes();
	}
}
