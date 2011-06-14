var pi = jQuery("#pollOptions").get(0);
	
function addOption(value) {		
	if(jQuery('#ccm-survey-optionValue').val().length) {
		if (currentOption == 0) {
			pi.innerHTML = "";
		}
		currentOption++;
		ip = document.createElement("INPUT");
		ip.type = "hidden";
		ip.name = "pollOption[]";
		ip.value = jQuery('#ccm-survey-optionValue').val();
				
		ipd = document.createElement("DIV");
		ipd.id = "option" + currentOption;
		ipd.className = 'survey-block-option';
		ipd.innerHTML = "<a href=\"#\" onclick=\"removeOption(" + currentOption + ")\"><img src=\"" + CCM_IMAGE_PATH + "/icons/delete_small.png\" /><" + "/a> " + ip.value;
		ipd.appendChild(ip);
		pi.appendChild(ipd);	
		jQuery('#ccm-survey-optionValue').val('');
	}
}

function removeOption(id) {
	opt = jQuery("#option" + id).get(0);
	pi.removeChild(opt);
	currentOption--;
	if (currentOption == 0) {
		pi.innerHTML = "None";
	}
}