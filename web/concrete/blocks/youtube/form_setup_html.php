<?php defined('C5_EXECUTE') or die("Access Denied."); ?> 
<style type="text/css">
table#videoBlockSetup th {font-weight: bold; text-style: normal; padding-right: 8px; white-space: nowrap; vertical-align:top ; padding-bottom:8px}
table#videoBlockSetup td{ font-size:12px; vertical-align:top; padding-bottom:8px;}
table#videoBlockSetup .note{ font-size:10px; color:#999999; font-weight:normal }
</style> 

<?php
if (!$bObj->vWidth) {
	$bObj->vWidth=425;
}
if (!$bObj->vHeight) {
	$bObj->vHeight=344;
}

?>

<table id="videoBlockSetup" style="width:100%" class="table table-bordered"> 
	<tr>
		<th><?php echo t('Title')?></th>
		<td><input type="text" style="width: 230px" name="title" value="<?php echo $bObj->title?>"/></td>
	</tr>	
	<tr>
		<th><?php echo t('YouTube URL')?></th>
		<td>
			<input type="text" style="width: 230px" id="YouTubeVideoURL" name="videoURL" value="<?php echo $bObj->videoURL?>" />
		</td>
	</tr>
	<tr>
		<th><?php echo t('Width')?></th>
		<td>
			<input type="text" style="width: 40px" id="YouTubeVideoWidth" name="vWidth" value="<?php echo $bObj->vWidth?>" />
		</td>
	</tr>
	<tr>
		<th><?php echo t('Height')?></th>
		<td>
			<input type="text" style="width: 40px" id="YouTubeVideoHeight" name="vHeight" value="<?php echo $bObj->vHeight?>" />
		</td>
	</tr>
	<tr>
		<th><?php echo t('Video Player')?></th>
		<td>
			<input type="radio" name="vPlayer" value="1" <?php echo ($bObj->vPlayer)?'checked':''?> /> <?php echo t('iFrame - Works in more devices')?> <br/>
			<input type="radio" name="vPlayer" value="0" <?php echo (!$bObj->vPlayer)?'checked':''?> /> <?php echo t('Flash Embed - Legacy method')?>
		</td>
	</tr>	
</table>