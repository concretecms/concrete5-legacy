<?php defined('C5_EXECUTE') or die('Access Denied');
$txt = Loader::helper('text');
if(!is_array($jsenabled)) {
	$jsenabled = array();//fallback
}
if(!is_array($cssenabled)) {
	$cssenabled = array();//fallback
}
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Site Assets'), false, 'span8 offset4', false);
	echo '<form method="post" action="'.$this->action('save_assets').'">';
		echo $this->controller->token->output('loaded_assets');
		echo '<div class="ccm-pane-body">';
			echo '<div class="clearfix">';
				echo '<p>'.t('The javascript and CSS selected will automatically be loaded on every page on the site.').'</p>';
				echo '<span class="help-block">'.t('It is recommended that jquery.js, ccm.base.js, and ccm.base.css be selected.').'</span>';
				echo '<ul class="inputs-list">';
					echo '<fieldset><legend>'.t('Javascript').'</legend>';
						if(count($js) > 0) { //this is just to prevent errors, this should NEVER actually happen though
							foreach($js as $j) {
								$checked = '';
								if(in_array($j, $jsenabled)) {
									$checked = ' checked="checked"';
								}
								echo '<li>';
									echo '<label><input'.$checked.' type="checkbox" name="js[]" value="'.$j.'"/>&nbsp;<span>'.$j.'</span></label>';
								echo '</li>';
							}
						} else {
							echo t('None');
						}
					echo '</fieldset>';
			
					echo '<fieldset><legend>'.t('CSS').'</legend>';
						if(count($css) > 0) { //this is just to prevent errors, this should NEVER actually happen though
							foreach($css as $j) {
								$checked = '';
								if(in_array($j, $cssenabled)) {
									$checked = ' checked="checked"';
								}
								echo '<li>';
									echo '<label><input'.$checked.' type="checkbox" name="css[]" value="'.$j.'"/>&nbsp;<span>'.$j.'</span></label>';
								echo '</li>';
							}
						} else {
							echo t('None');
						}
					echo '</fieldset>';
				echo '</ul>';
			echo '</div>';
		echo '</div>';
		echo '<div class="ccm-pane-footer">';
			echo '<input type="submit" class="btn ccm-button-v2 primary ccm-button-v2-right"/>';
		echo '</div>';
	echo '</form>';
echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);