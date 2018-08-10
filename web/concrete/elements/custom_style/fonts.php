<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-styleEditPane-fonts" class="ccm-styleEditPane">	
	<div>
	<h3><?php echo t('Fonts')?></h3>
		<table class="ccm-style-property-table table" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td>
				<?php echo t('Face')?>:
				</td>
				<td>  
				<select name="font_family"> 
					<option <?php echo ($cssData['font_family']=='inherit')?'selected':'' ?> value="inherit"><?php echo t('Inherit') ?></option>
					<option <?php echo ($cssData['font_family']=='Arial')?'selected':'' ?> value="Arial">Arial</option>
					<option <?php echo ($cssData['font_family']=='Times New Roman')?'selected':'' ?> value="Times New Roman">Times New Roman</option>
					<option <?php echo ($cssData['font_family']=='Courier')?'selected':'' ?> value="Courier">Courier</option>
					<option <?php echo ($cssData['font_family']=='Georgia')?'selected':'' ?> value="Georgia">Georgia</option>
					<option <?php echo ($cssData['font_family']=='Verdana')?'selected':'' ?> value="Verdana">Verdana</option>
				</select>
				</td>
			</tr>
			<tr>
				<td> 
				<?php echo t('Size')?>:
				</td>
				<td>
					<input name="font_size" type="text" value="<?php echo htmlentities( $cssData['font_size'], ENT_COMPAT, APP_CHARSET) ?>" size=2 />	
				</td>
			</tr>
			<tr>
				<td> 
					<?php echo t('Line Height')?>:
				</td>
				<td>
					<input name="line_height" type="text" value="<?php echo htmlentities( $cssData['line_height'], ENT_COMPAT, APP_CHARSET) ?>" size=2 />	
				</td>
			</tr>
			<tr>
				<td> 
					<?php echo t('Color')?>:
				</td>
				<td>
					<?php echo $fh->output( 'color', '', $cssData['color']) ?> 
				</td>

			</tr>											
			<tr>
				<td> 
				<?php echo t('Alignment')?>:
				</td>
				<td> 
				<select name="text_align"> 
					<option <?php echo ($cssData['text_align']=='')?'selected':'' ?> value=""><?php echo t('Default')?></option>
					<option <?php echo ($cssData['text_align']=='left')?'selected':'' ?> value="left"><?php echo t('Left')?></option>
					<option <?php echo ($cssData['text_align']=='center')?'selected':'' ?> value="center"><?php echo t('Center')?></option>
					<option <?php echo ($cssData['text_align']=='right')?'selected':'' ?> value="right"><?php echo t('Right')?></option>
					<option <?php echo ($cssData['text_align']=='justify')?'selected':'' ?> value="justify"><?php echo t('Justify')?></option>
				</select>
				</td>
			</tr>
		</table>
	</div> 
</div>	