(function(){
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
	function startup() {
		var codeMirrorPath = ccm_t('codeMirrorPath');
		var textarea = document.getElementById('ccm-HtmlContent');
		if(!codeMirrorPath || !textarea || $.type(window.require) != 'function') {
			setTimeout(function() { startup(); }, 50);
			return;
		}
		loadCodeMirror(
			codeMirrorPath,
			['xml', 'javascript', 'css', 'htmlmixed', 'htmlembedded'],
			function() {
				var codeMirror = CodeMirror.fromTextArea(
					textarea,
					{
						lineNumbers: true,
						mode: 'application/x-ejs',
						indentUnit: 3,
						indentWithTabs: true,
						enterMode: 'keep',
						tabMode: 'shift'
					}
				);
				var dialog = $(textarea).closest('.ui-dialog');
				function resize() {
					var h = dialog.find('.ui-dialog-content').height();
					if(h > 10) {
						codeMirror.setSize(null, h);
					}
				}
				resize();
				dialog.on('dialogresize', function() {
					resize();
				});
			}
		);
	}
	startup();
})();