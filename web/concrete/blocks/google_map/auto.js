$(document).ready(function() {

	var pane = {
		set: function(key) {
			$('.ccm-googleMapBlock-tab').removeClass('active');
			$('.ccm-googleMapBlock-pane').hide();
			$('#ccm-googleMapBlock-tab-' + key).addClass('active');
			$('#ccm-googleMapBlock-pane-' + key).show();
		}
	}

	function updateBalloon() {
		if($('#balloonShow').is(':checked')) {
			$('.ccm-googleMapBlock-with-balloon').show();
		}
		else {
			$('.ccm-googleMapBlock-with-balloon').hide();
		}
	}
	$('#ccm-googleMapBlock-tabs a').on('click', function() {
		var $li = $(this).closest('li');
		pane.set($li.attr('id').substr('ccm-googleMapBlock-tab-'.length));
	});
	$('#balloonShow').on('change', function() {
		updateBalloon();
	});

	pane.set('general');
	updateBalloon();
});
