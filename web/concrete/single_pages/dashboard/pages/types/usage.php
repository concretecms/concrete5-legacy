<?php defined('C5_EXECUTE') or die("Access Denied.");

$dashboardHelper = Loader::helper('concrete/dashboard');

echo $dashboardHelper->getDashboardPaneHeaderWrapper(/*i18n: %s is the name of a page type*/t('Usage of page type %s', $ct->ctName), false, false, false);
	?><div class="ccm-pane-body"><?php
		if(!count($pageVersions)) {
			echo t('This page type is not in use.');
		}
		else {
			?><table class="table table-bordered table-striped">
				<thead><tr>
					<th><?php echo t('Page'); ?></th>
					<th><?php echo t('Path'); ?></th>
					<th></th>
				</tr></thead>
				<tbody><?php
					foreach($pageVersions as $pageVersion) {
						?><tr>
							<td><?php echo htmlspecialchars($pageVersion['Page']->getCollectionName()); ?></td>
							<td><?php echo htmlspecialchars($pageVersion['Page']->getCollectionPath()); ?></td>
							<td><a href="<?php echo DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $pageVersion['cID'] . '&amp;cvID=' . $pageVersion['cvID']; ?>"><?php echo t('view'); ?></a></td>
						<tr><?php
					}
				?></tbody>
			</table><?php
		}
	?></div>
	<div class="ccm-pane-footer"></div>
	<?php
echo $dashboardHelper->getDashboardPaneFooterWrapper(false);
