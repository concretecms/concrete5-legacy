loadColumnsPane = function(searchType) {
	url = $('input[name=bricksToolsURL_'+searchType+']').val() + 'columns.php?';
	url += 'akCategoryHandle=' + $('select[name=akCategoryHandle]').val();
	if($('select[name=akCategoryHandle]').val() == $('input[name=originalAKC_'+searchType+']').val()) {
		if($('input[name=defaults_'+searchType+']').val()) {
			url += '&defaults=' + $('input[name=defaults_'+searchType+']').val();
		}
	}
	url += '&persistantBID=' + $('input[name=persistantBID_'+searchType+']').val();
	$('#ccm-bricksPane-columns').load(url);
}

loadColumnsPane($('input[name=fakeInstance]').val());

$('select[name=akCategoryHandle]').change(function() {
	loadColumnsPane($('input[name=fakeInstance]').val());
});

ccm_setupAdvancedSearch($('input[name=fakeInstance]').val());