<?php
defined('C5_EXECUTE') or die("Access Denied.");
$tp = new TaskPermission();
if ($tp->canAccessGroupSearch()) { 
	$form = Loader::helper('form');
	$date = Loader::helper('form/date_time'); 
	$txt = Loader::helper('text');
	$ih = Loader::helper('concrete/interface');
	$valt = Loader::helper('validation/token');

	if ($this->controller->getTask() == 'edit') { 
		
		$days = $group->getGroupExpirationIntervalDays();
		$hours = $group->getGroupExpirationIntervalHours();
		$minutes = $group->getGroupExpirationIntervalMinutes();
			
		$style = 'width: 60px';
	?>
	
		<h1><span><?php echo t('Edit Group')?></span></h1>
		<div class="ccm-dashboard-inner">
			<form method="post" id="update-group-form" action="<?php echo $this->action('edit_group')?>">
				<?php echo $valt->output('edit_group')?>		
				<input type="hidden" name="gID" value="<?php echo $group->getGroupID()?>" />
				<div style="margin:0px; padding:0px; width:100%; height:auto" >	
				<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
					<tr>
						<td class="subheader"><?php echo t('Name')?> <span class="required">*</span></td>
					</tr>
					<tr>
						<td><input type="text" name="gName" style="width: 100%" value="<?php echo $group->getGroupName()?>" /></td>
					</tr>
					<tr>
						<td class="subheader"><?php echo t('Description')?></td>
					</tr>
					<tr>
						<td><textarea name="gDescription" cols="135" rows="8"><?php echo $group->getGroupDescription()?></textarea></td>
					</tr>
					<tr>
						<td class="subheader"><?php echo t('Group Expiration Options')?></td>
					</tr>
					<tr>	
						<td>
							<?php echo $form->checkbox('gUserExpirationIsEnabled', 1, $group->isGroupExpirationEnabled())?>
							<?php echo '<label for="gUserExpirationIsEnabled">'.t('Automatically remove users from this group').'</label>'?>
			
							<?php echo $form->select("gUserExpirationMethod", array(
								'SET_TIME' => t('at a specific date and time'),
								'INTERVAL' => t('once a certain amount of time has passed')
							), $group->getGroupExpirationMethod(), array('disabled' => true));?>	
			
							<div id="gUserExpirationSetTimeOptions" style="display: none">
								<br/>
								<h2><?php echo t('Expiration Date')?></h2>
								<?php echo $date->datetime('gUserExpirationSetDateTime', $group->getGroupExpirationDateTime())?>
							</div>
							<div id="gUserExpirationIntervalOptions" style="display: none">
								<br/>
								<h2><?php echo t('Accounts will Expire After')?></h2>
								<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top"><strong><?php echo t('Days')?></strong><br/>
							<?php echo $form->text('gUserExpirationIntervalDays', $days, array('style' => $style))?>
							</td>
							<td valign="top"><strong><?php echo t('Hours')?></strong><br/>
							<?php echo $form->text('gUserExpirationIntervalHours', $hours, array('style' => $style))?>
							</td>
							<td valign="top"><strong><?php echo t('Minutes')?></strong><br/>
							<?php echo $form->text('gUserExpirationIntervalMinutes', $minutes, array('style' => $style))?>
							</td>
						</tr>
					</table>
							</div>
							<div id="gUserExpirationAction" style="display: none">
					<br/>
					<h2><?php echo t('Expiration Action')?></h2>
					<?php echo $form->select("gUserExpirationAction", array(
						'REMOVE' => t('Remove the user from this group'),
						'DEACTIVATE' => t('Deactivate the user account'),
						'REMOVE_DEACTIVATE' => t('Remove the user from the group and deactivate the account')
					), $group->getGroupExpirationAction());?>	
				</div>
						</td>
					</tr>
					<tr>
				<td class="header">
					<?php echo $ih->submit(t('Update'), 'update-group-form')?>
					<?php echo $ih->button(t('Cancel'), $this->url('/dashboard/users/groups'), 'left')?>
				</td>
			</tr>
				</table>
			</div>
				<br />
			</form>	
		</div>
		
		<h1><span><?php echo t('Delete Group')?></span></h1>
		<div class="ccm-dashboard-inner">
			<?php $u = new User();
			$delConfirmJS = t('Are you sure you want to permanently remove this group?');
			if(!$u->isSuperUser()){ 
				echo t('You must be logged in as %s to remove groups.', USER_SUPER);
			}else{ ?> 
				<script type="text/javascript">
					deleteGroup = function() {
						if (confirm('<?php echo $delConfirmJS?>')) { 
							location.href = "<?php echo $this->url('/dashboard/users/groups', 'delete', $group->getGroupID(), $valt->generate('delete_group_' . $group->getGroupID() ))?>";				
						}
					}
				</script>
				<?php print $ih->button_js(t('Delete Group'), "deleteGroup()", 'left');
			} ?>
		</div>

	<?php } else { 
		Loader::model('search/group');
		$gl = new GroupSearch();
		if (isset($_GET['gKeywords'])) {
			$gl->filterByKeywords($_GET['gKeywords']);
		}

		$gResults = $gl->getPage();
		?>
		<h1><span><?php echo t('Groups')?></span></h1>
		<div class="ccm-dashboard-inner">
			<form id="ccm-group-search" method="get" style="top: -30px; left: 10px" action="<?php echo $this->url('/dashboard/users/groups')?>">
				<div id="ccm-group-search-fields">
					<input type="text" id="ccm-group-search-keywords" name="gKeywords" value="<?php echo $txt->entities($_REQUEST['gKeywords'])?>" class="ccm-text" style="width: 100px" />
					<input type="submit" value="<?php echo t('Search')?>" />
					<input type="hidden" name="group_submit_search" value="1" />
				</div>
			</form>

			<?php if (count($gResults) > 0) { 
				$gl->displaySummary();
	
				foreach ($gResults as $g) { ?>
					<div class="ccm-group">
						<a class="ccm-group-inner" href="<?php echo $this->url('/dashboard/users/groups', 'edit', $g['gID'])?>" style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo $txt->entities($g['gName'])?></a>
						<?php if(trim($g['gDescription'])) { ?>
							<div class="ccm-group-description"><?php echo $txt->entities($g['gDescription'])?></div>
						<?php } ?>
					</div>
				<?php }
			$gl->displayPaging();

			} else { ?>

				<p><?php echo t('No groups found.')?></p>
	
			<?php } ?>
		</div>
		<h1><span><?php echo t('Add Group')?> (<em class="required">*</em> - <?php echo t('required field')?>)</span></h1>
		<div class="ccm-dashboard-inner">
			<form method="post" id="add-group-form" action="<?php echo $this->action('add_group')?>">
				<?php echo $valt->output('add_group')?>
				<div style="margin:0px; padding:0px; width:100%; height:auto" >	
					<table class="entry-form" border="0" cellspacing="1" cellpadding="0">
						<tr>
							<td class="subheader"><?php echo t('Name')?> <span class="required">*</span></td>
						</tr>
						<tr>
							<td><input type="text" name="gName" style="width: 100%" value="" /></td>
						</tr>
						<tr>
							<td class="subheader"><?php echo t('Description')?></td>
						</tr>
						<tr>
							<td><textarea name="gDescription" cols="135" rows="8"></textarea></td>
						</tr>
						<tr>
							<td class="subheader"><?php echo t("Group Expiration Options")?></td>
						</tr>
						<tr>	
							<td>
								<?php echo $form->checkbox('gUserExpirationIsEnabled', 1, false)?>
								<?php echo '<label for="gUserExpirationIsEnabled">'.t('Automatically remove users from this group').'</label>'?>
	
								<?php echo $form->select("gUserExpirationMethod", array(
									'SET_TIME' => t('at a specific date and time'),
									'INTERVAL' => t('once a certain amount of time has passed')
								), array('disabled' => true));?>	
	
								<div id="gUserExpirationSetTimeOptions" style="display: none">
									<br />
									<h2><?php echo t('Expiration Date')?></h2>
									<?php echo $date->datetime('gUserExpirationSetDateTime')?>
								</div>
								<div id="gUserExpirationIntervalOptions" style="display: none">
									<br />
									<h2><?php echo t('Accounts will Expire After')?></h2>
									<table border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td valign="top"><strong><?php echo t('Days')?></strong><br/>
												<?php echo $form->text('gUserExpirationIntervalDays', array('style' => $style))?>
											</td>
											<td valign="top"><strong><?php echo t('Hours')?></strong><br/>
												<?php echo $form->text('gUserExpirationIntervalHours', array('style' => $style))?>
											</td>
											<td valign="top"><strong><?php echo t('Minutes')?></strong><br/>
												<?php echo $form->text('gUserExpirationIntervalMinutes', array('style' => $style))?>
											</td>
										</tr>
									</table>
								</div>
								<div id="gUserExpirationAction" style="display: none">
									<br />
									<h2><?php echo t('Expiration Action')?></h2>
									<?php echo $form->select("gUserExpirationAction", array(
										'REMOVE' => t('Remove the user from this group'),
										'DEACTIVATE' => t('Deactivate the user account'),
										'REMOVE_DEACTIVATE' => t('Remove the user from the group and deactivate the account')
		
									));?>	

								</div>
							</td>
						</tr>
						<tr>
							<td class="header"><input type="hidden" name="add" value="1" /><?php echo $ih->submit(t('Add'), 'add-group-form')?></td>
						</tr>
					</table>
				</div>
				<br />
			</form>	
		</div>

	<?php } ?>
	<script type="text/javascript">
		ccm_checkGroupExpirationOptions = function() {
			var sel = $("select[name=gUserExpirationMethod]");
			var cb = $("input[name=gUserExpirationIsEnabled]");
			if (cb.attr('checked')) {
				sel.attr('disabled', false);
				switch(sel.val()) {
					case 'SET_TIME':
						$("#gUserExpirationSetTimeOptions").show();
						$("#gUserExpirationIntervalOptions").hide();
						break;
					case 'INTERVAL': 
						$("#gUserExpirationSetTimeOptions").hide();
						$("#gUserExpirationIntervalOptions").show();
						break;				
				}
				$("#gUserExpirationAction").show();
			} else {
				sel.attr('disabled', true);	
				$("#gUserExpirationSetTimeOptions").hide();
				$("#gUserExpirationIntervalOptions").hide();
				$("#gUserExpirationAction").hide();
			}
		}
		
		$(function() {
			$("input[name=gUserExpirationIsEnabled]").click(ccm_checkGroupExpirationOptions);
			$("select[name=gUserExpirationMethod]").change(ccm_checkGroupExpirationOptions);
			ccm_checkGroupExpirationOptions();
		});
	</script>
<?php } else { ?>
	<p><?php echo t('You do not have access to group search. This setting may be changed in the access section of the dashboard settings page.')?></p>
<?php } ?>