<?php defined('C5_EXECUTE') or die('Access Denied.');

$dh = Loader::helper('concrete/dashboard');

echo $dh->getDashboardPaneHeaderWrapper(t('Translate special items'), false, 'span12', false);

if(empty($locales)) {
	 ?><div class="alert"><?php echo t('No locales defined.'); ?></div><?php
}
else {
	$ih = Loader::helper('concrete/interface');
	$jh = Loader::helper('json');
	?>
	<script type="text/javascript">
		function updateCurrentTable() {
			$(".tsi-table").hide();
			$("#tsi-table-" + $("#tsi-which").val()).show();
		}
		$(document).ready(function() {
			updateCurrentTable();
		});
	</script>
	<form method="post" id="user-translate-form" action="<?php echo $this->action('update') ?>" class="form-horizontal">
		<?php echo $this->controller->token->output('update_translations')?>
		<div class="ccm-pane-options">
			<div class="row">
				<div class="span5">
					<label>
						<?php echo t('Items'); ?>
						<select id="tsi-which" name="currentTable" onchange="updateCurrentTable()">
							<?php
							foreach($translationTables as $ttCode => $tt) {
								?><option value="<?php echo h($ttCode); ?>"<?php echo ($ttCode == $currentTable) ? ' selected="selected"' : ''; ?>><?php echo h($tt['name']); ?></option><?php
							}
							?>
						</select>
					</label>
				</div>
				<div class="span5">
					<label>
						<?php echo t('Language'); ?>
						<select name="locale" onchange="window.location.href = <?php echo h($jh->encode(View::url('/dashboard/system/basics/translate_special_items/?locale='))); ?> + encodeURIComponent(this.value)"><?php
						foreach($locales as $localeID => $localeName) {
							?><option value="<?php echo h($localeID); ?>"<?php echo ($localeID == $locale) ? ' selected="selected"' : ''; ?>><?php echo h($localeName); ?></option><?php
						}
						?></select>
					</label>
				</div>
			</div>
		</div>
		<div class="ccm-pane-body">
			<?php
			foreach($translationTables as $ttCode => $tt) {
				?><table class="table table-striped table-condensed tsi-table" style="display:none" id="tsi-table-<?php echo h($ttCode); ?>">
					<tbody>
						<?php echo $tt['rows']; ?>
					</tbody>
				</table><?php
			}
			?>
		</div>
		<div class="ccm-pane-footer">
			<? echo $ih->submit(t('Save'), 'user-translate-form', 'right', 'primary'); ?>
		</div>
	</form><?php
}

echo $dh->getDashboardPaneFooterWrapper(false);
