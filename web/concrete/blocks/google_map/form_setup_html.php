<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<style type="text/css">
.ccm-googleMapBlock-pane>table { width: 100%; }
.ccm-googleMapBlock-pane>table>tbody>tr>th { font-weight: bold; text-style: normal; padding-right: 8px; vertical-align:top ; padding-bottom:8px; width: 130px; }
.ccm-googleMapBlock-pane>table>tbody>tr>td { font-size: 12px; vertical-align: top; padding-bottom: 8px; }
.ccm-googleMapBlock-pane>table>tbody>tr>td>input[type="text"] { width: 100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; height:28px; }
.ccm-googleMapBlock-pane>table>tbody>tr>td>input[type="number"] { width: 50px; }
</style>

<div class="ccm-ui">
	<ul class="tabs" id="ccm-googleMapBlock-tabs">
		<li class="active ccm-googleMapBlock-tab" id="ccm-googleMapBlock-tab-general"><a href="javascript:void(0)"><?php echo t('General')?></a></li>
		<li class="ccm-googleMapBlock-tab" id="ccm-googleMapBlock-tab-balloon"><a href="javascript:void(0)"><?php echo t('Balloon')?></a></li>
	</ul>
	<div class="ccm-googleMapBlock-pane" id="ccm-googleMapBlock-pane-general">
		<table class="table table-bordered">
			<tbody>
				<tr>
					<th><?php echo t('Map Title')?>: <div class="note">(<?php echo t('Optional')?>)</div></th>
					<td><input id="ccm_googlemap_block_title" name="title" value="<?php echo $mapObj->title?>" maxlength="255" type="text" /></td>
				</tr>
				<tr>
					<th><?php echo t('Location')?>:</th>
					<td>
						<input id="ccm_googlemap_block_location" name="location" value="<?php echo $mapObj->location?>" maxlength="255" type="text" />
						<div class="note"><?php echo t('e.g. 17 SE 3rd #410, Portland, OR, 97214')?></div>
					</td>
				</tr>
				<tr>
					<th><?php echo t('Zoom')?>:</th>
					<td>
						<input id="ccm_googlemap_block_zoom" name="zoom" value="<?php echo $mapObj->zoom?>" maxlength="2" type="number" min="0" max="21" />
						<div class="ccm-note"><?php echo t('Enter a number from 0 to 21, with 21 being the most zoomed in.')?> </div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="ccm-googleMapBlock-pane" id="ccm-googleMapBlock-pane-balloon" style="display: none">
		<table class="table table-bordered">
			<tbody>
				<tr>
					<th><?php echo t('Show a balloon?'); ?></th>
					<td><input type="checkbox" name="balloonShow" id="balloonShow"<?php echo (isset($balloonShow) && $balloonShow) ? ' checked="checked"' : ''; ?> /></td>
				</tr>
				<tr class="ccm-googleMapBlock-with-balloon">
					<th><?php echo t('Balloon content'); ?></th>
					<td><?php
					Loader::element('editor_config', array('editor_height' => 50));
					echo Loader::helper('form')->textarea('balloonContent', (isset($balloonContent) && is_string($balloonContent)) ? $balloonContent : '', array('class' => 'ccm-advanced-editor'));
					?></td>
				</tr>
				<tr class="ccm-googleMapBlock-with-balloon">
					<th><?php echo t('Show link to Google Maps?'); ?></th>
					<td><input type="checkbox" name="balloonWithLinkToMaps" id="balloonWithLinkToMaps"<?php echo (isset($balloonWithLinkToMaps) && $balloonWithLinkToMaps) ? ' checked="checked"' : ''; ?> /></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>