<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-styleEditPane-border" class="ccm-styleEditPane" style="display:none">
	<div>
	  <h3><?php echo t('Border')?></h3>  
		<table class="ccm-style-property-table table" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td> 
					<?php echo t('Style')?>:
				</td>
				<td>
					<select name="border_style" > 
						<option <?php echo ($cssData['border_style']=='none')?'selected':'' ?> value="none"><?php echo t('none')?></option>
						<option <?php echo ($cssData['border_style']=='solid')?'selected':'' ?> value="solid"><?php echo t('solid')?></option>
						<option <?php echo ($cssData['border_style']=='dotted')?'selected':'' ?> value="dotted"><?php echo t('dotted')?></option>
						<option <?php echo ($cssData['border_style']=='dashed')?'selected':'' ?> value="dashed"><?php echo t('dashed')?></option>
						<option <?php echo ($cssData['border_style']=='double')?'selected':'' ?> value="double"><?php echo t('double')?></option>
						<option <?php echo ($cssData['border_style']=='groove')?'selected':'' ?> value="groove"><?php echo t('groove')?></option>
						<option <?php echo ($cssData['border_style']=='inset')?'selected':'' ?> value="inset"><?php echo t('inset')?></option>
						<option <?php echo ($cssData['border_style']=='outset')?'selected':'' ?> value="outset"><?php echo t('outset')?></option>
						<option <?php echo ($cssData['border_style']=='ridge')?'selected':'' ?> value="ridge"><?php echo t('ridge')?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo t('Width')?>:
				</td> 				
				<td>
					<input name="border_width" type="text" value="<?php echo intval($cssData['border_width'])?>" size="2" style="width:20px" /><span class="ccm-note"> <?php echo t('px')?></span>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo t('Direction')?>:
				</td>
				<td>
					<select name="border_position" > 
						<option <?php echo ($cssData['border_position']=='full')?'selected':'' ?> value="full"><?php echo t('Full')?></option> 
						<option <?php echo ($cssData['border_position']=='top')?'selected':'' ?> value="top"><?php echo t('Top')?></option> 
						<option <?php echo ($cssData['border_position']=='right')?'selected':'' ?> value="right"><?php echo t('Right')?></option>
						<option <?php echo ($cssData['border_position']=='bottom')?'selected':'' ?> value="bottom"><?php echo t('Bottom')?></option>
						<option <?php echo ($cssData['border_position']=='left')?'selected':'' ?> value="left"><?php echo t('Left')?></option> 
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo t('Color')?>:
				</td>
				<td>
					<?php echo $fh->output( 'border_color', '', $cssData['border_color']) ?> 
				</td> 
			</tr>
		</table>	  
	</div>		
</div>