bricksTabSetup = function(searchType) {
	$('ul#ccm-bricks-tabs li a').each( function(num,el){ 
		el.onclick=function(){
			var pane=this.id.replace('ccm-bricks-tab-','');
			bricksShowPane(pane);
		}
	});	
	loadColumnsPane(searchType);
	loadSearchFormAdvanced(searchType);
}

bricksShowPane = function (pane){
	$('ul#ccm-bricks-tabs li').each(function(num,el){ $(el).removeClass('ccm-nav-active') });
	$(document.getElementById('ccm-bricks-tab-'+pane).parentNode).addClass('ccm-nav-active');
	$('div.ccm-bricksPane').each(function(num,el){ el.style.display='none'; });
	$('#ccm-bricksPane-'+pane).css('display','block');
}

loadColumnsPane = function(searchType) {
	url = $('input[name=bricksToolsURL_'+searchType+']').val() + 'columns.php?';
	url += 'akCategoryHandle=' + $('select[name=akCategoryHandle]').val();
	if($('select[name=akCategoryHandle]').val() == $('input[name=originalAKC_'+searchType+']').val()) {
		if($('input[name=columns_'+searchType+']').val()) {
			url += '&columns=' + $('input[name=columns_'+searchType+']').val();
		}
	}
	url += '&persistantBID=' + $('input[name=persistantBID_'+searchType+']').val();
	$('#ccm-bricksPane-columns').load(url);
}

loadSearchFormAdvanced = function (searchType) {
	url = CCM_TOOLS_PATH + '/bricks/search_form_advanced.php?';
	url += 'akCategoryHandle=' + $('select[name=akCategoryHandle]').val();
	url += '&searchInstance=' + searchType;
	if($('select[name=akCategoryHandle]').val() == $('input[name=originalAKC_'+searchType+']').val()) {
		if($('input[name=keywords_'+searchType+']').val()) {
			url += '&keywords=' + $('input[name=keywords_'+searchType+']').val();
		}
		if($('input[name=numResults_'+searchType+']').val()) {
			url += '&numResults=' + $('input[name=numResults_'+searchType+']').val();
		}
		if($('input[name=akID_'+searchType+']').val()) {
			url += '&akID'+'=' + $('input[name=akID_'+searchType+']').val();
		}
	}
	url += '&disableSubmit=1';
	$('#default-search-parameters').load(url);
}

$(function() {	
	bricksTabSetup($('input[name=fakeInstance]').val());		
});

$('select[name=akCategoryHandle]').change(
	function(){ 
		loadColumnsPane($('input[name=fakeInstance]').val());
		loadSearchFormAdvanced($('input[name=fakeInstance]').val());
	}
);

ccm_setupAdvancedSearch($('input[name=fakeInstance]').val());