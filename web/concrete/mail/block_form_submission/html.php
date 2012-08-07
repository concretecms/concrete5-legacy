<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<p><?php echo t("There has been a submission of the form %s on through your concrete5 website.", $formName) ?></p>
<table cellpadding="0" cellpadding="0">
	<?php foreach($questionAnswerPairs as $questionAnswerPair) : ?>
	<tr>
		<th align="left"><?php echo $questionAnswerPair['question'] ?></th>
	</tr>
	<tr>
		<td style="padding-bottom:10px;"><?php echo $questionAnswerPair['answer'] ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<p><?php echo t("To view all of this form's submissions, visit") ?><br/><a target="_blank" href="<?php echo $formDisplayUrl ?>"><?php echo $formDisplayUrl ?></a></p>