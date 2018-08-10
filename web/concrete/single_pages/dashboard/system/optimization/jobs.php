<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
/* @var $form FormHelper */
$form = Loader::helper('form');
/* @var $jh JsonHelper */
$jh = Loader::helper('json');
/* @var $dh DateHelper */
$dh = Loader::helper('date');

?>
<style type="text/css">
#ccm-jobs-list td {
	vertical-align: middle;
	-webkit-transition-property: color, background-color;
	-webkit-transition-duration: .9s, .9s;
	-moz-transition-property: color, background-color;
	-moz-transition-duration: .9s, .9s;
	-o-transition-property: color, background-color;
	-o-transition-duration: .9s, .9s;
	-ms-transition-property: color, background-color;
	-ms-transition-duration: .9s, .9s;
	transition-property: color, background-color;
	transition-duration: .9s, .9s;
 }

#ccm-jobs-list td button {
 	float: right;
 }

#ccm-jobs-list tr.error td {
	color: #f00;
}

#ccm-jobs-list tr.success td {
	color: #090;
}

</style>

<?php echo $h->getDashboardPaneHeaderWrapper(t('Automated Jobs'), false, false);?>

<?php echo Loader::helper('concrete/interface')->tabs(array(
	array($this->action('view'), t('Jobs'), $jobListSelected),
	array($this->action('view_sets'), t('Job Sets'), $jobSetsSelected)
), false);?>

<?php if (in_array($this->controller->getTask(), array('view', 'install', 'uninstall', 'job_installed', 'job_uninstalled', 'reset', 'reset_complete', 'job_scheduled'))) { ?>

<div id="ccm-tab-content-list">

<?php if (count($installedJobs) > 0) { ?>

<table class="table" id="ccm-jobs-list">
	<thead>
	<tr>
		<th><?php echo t('ID')?></th>
		<th style="width: 200px"><?php echo t('Name')?></th>
		<th><?php echo t('Last Run')?></th>
		<th style="width: 200px"><?php echo t('Results of Last Run')?></th>
		<td><a href="<?php echo $this->action('reset')?>" class="btn pull-right btn-mini"><?php echo t('Reset All Jobs')?></a></td>
		<td></td>
	</tr>
	</thead>
	<tbody>
	<?php foreach($installedJobs as $j) { ?>
		<tr class="<?php if ($j->didFail()) { ?>error<?php } ?> <?php if ($j->getJobStatus() == 'RUNNING') {?>running<?php } ?>">
			<td><?php echo $j->getJobID()?></td>
			<td><i class="icon-question-sign" title="<?php echo $j->getJobDescription()?>"></i> <?php echo $j->getJobName()?></td>
			<td class="jDateLastRun"><?php
				if ($j->getJobStatus() == 'RUNNING') {
					$runtime = $dh->formatTime($j->getJobDateLastRun(), true, true);
					echo ("<strong>");
					echo t("Running since %s", $runtime);
					echo ("</strong>");
				} else if($j->getJobDateLastRun() == '' || substr($j->getJobDateLastRun(), 0, 4) == '0000') {
					echo t('Never');
				} else {
					$runtime = $dh->formatTime($j->getJobDateLastRun(), true, true);
					echo $runtime;
				}
			?></td>
			<td class="jLastStatusText"><?php echo $j->getJobLastStatusText()?></td>
			<td class="ccm-jobs-button">
				<button data-jID="<?php echo $j->getJobID()?>" data-jSupportsQueue="<?php echo $j->supportsQueue()?>" data-jName="<?php echo $j->getJobName()?>" class="btn-run-job btn-small btn"><i class="icon-play"></i> <?php echo t('Run')?></button>
			</td>
			<td>
				<a href="javascript:void(0)" class="ccm-automate-job-instructions" data-jSupportsQueue="<?php echo $j->supportsQueue()?>" data-jID="<?php echo $j->getJobID()?>" title="<?php echo t('Automate this Job')?>"><i class="icon-tasks"></i></a>
				<?php if ($j->canUninstall()) { ?>
					<a href="<?php echo $this->action('uninstall', $j->getJobID())?>" title="<?php echo t('Remove this Job')?>"><i class="icon-trash"></i></a>
				<?php } ?>
			</td>
		</tr>

	<?php } ?>
	</tbody>
</table>


<div style="display: none" id="ccm-jobs-automation-dialogs">

<?php foreach($installedJobs as $j) { ?>
	<div id="jd<?php echo $j->getJobID()?>" class="ccm-ui">
		<form action="<?php echo $this->action('update_job_schedule')?>" method="post">
			<?php echo $form->hidden('jID', $j->getJobID());?>
			<h4><?php echo t('Run Job')?></h4>
			
			<label class="radio">
				<input type="radio" name="isScheduled" class="ccm-jobs-automation-schedule-type" value="1" <?php echo ($j->isScheduled?'checked="checked"':'')?> />
				<?php echo t('When people browse to the page.  (which runs after the main rendering request of the page.)')?>
			</label>
			<fieldset class="ccm-jobs-automation-schedule-auto" <?php echo ($j->isScheduled?'':'style="display: none;"')?>>
				<div class="well">
					<div class="clearfix">
						<label><?php  echo t('Run this Job Every')?></label>
						<div class="input">
							<?php echo $form->text('value',$j->scheduledValue,array('class'=>'span2'))?>
							<?php echo $form->select('unit', array('hours'=>t('Hours'), 'days'=>t('Days'), 'weeks'=>t('Weeks'), 'months'=>t('Months')), $j->scheduledInterval, array('class'=>'span2'))?>
						</div>
					</div>
				</div>
			</fieldset>
			
			<label class="radio">
				<input type="radio" name="isScheduled" class="ccm-jobs-automation-schedule-type" value="0" <?php echo ($j->isScheduled?'':'checked="checked"')?> />
				<?php echo t('Through Cron')?>
			</label>
			<fieldset class="ccm-jobs-automation-schedule-cron" <?php echo ($j->isScheduled?'style="display: none;"':'')?>>
				<div class="well">
					<?php if ($j->supportsQueue()) { ?>
						<p><?php echo t('The "%s" job supports queueing, meaning it can be run in a couple different ways:', $j->getJobName())?></p>
						<h4><?php echo t('No Queueing')?></h4>
						<div><textarea style="width: 560px" rows="2" class="ccm-default-jobs-url"><?php echo BASE_URL . $this->url('/tools/required/jobs?auth=' . $auth . '&jID=' . $j->getJobID())?></textarea></div>
						<div class="alert alert-info"><?php echo t('This will treat the job as though it were like any other concrete5 job. The entire job will be run at once.')?></div>
			
						<h4><?php echo t('Queueing')?></h4>
						<p><?php echo t("First, schedule this URL for when you'd like this job to run:")?></p>
						<div><textarea style="width: 560px" rows="2" class="ccm-default-jobs-url"><?php echo BASE_URL . REL_DIR_FILES_TOOLS_REQUIRED . '/jobs/run_single?auth=' . $auth . '&jID=' . $j->getJobID()?></textarea></div>
						<p><?php echo t('Then, make sure this URL is scheduled to run frequently, like every 3-5 minutes:')?></p>
						<div><textarea style="width: 560px" rows="2" class="ccm-default-jobs-url"><?php echo BASE_URL . REL_DIR_FILES_TOOLS_REQUIRED . '/jobs/check_queue?auth=' . $auth?></textarea></div>
						<div class="alert alert-info"><?php echo t('The first URL starts the process - the second ensures that it completes in batches.')?></div>
			
					<?php } else { ?>
						<p><?php echo t('To run the "%s" job, automate the following URL using cron or a similar system:', $j->getJobName())?></p><br/>
						<div><textarea style="width: 560px" rows="2" class="ccm-default-jobs-url"><?php echo BASE_URL . $this->url('/tools/required/jobs/run_single?auth=' . $auth . '&jID=' . $j->getJobID())?></textarea></div>
					<?php } ?>	
				</div>
			</fieldset>
			<div class="ccm-pane-footer">
				<div class="ccm-buttons">
					<input type="submit" value="<?php echo t('Save'); ?>" class="btn ccm-button-v2 primary ccm-button-v2-right">
				</div>	
			</div>
		</form>
	</div>
<?php } ?>

</div>

<?php } else { ?>
	<p><?php echo t('You have no jobs installed.')?></p>
<?php } ?>

<?php if (count($availableJobs) > 0) { ?>
	<h4><?php echo t('Awaiting Installation')?></h4>
	<table class="table table-striped">
	<thead>
		<tr> 
			<th><?php echo t('Name')?></th>
			<th><?php echo t('Description')?></th> 
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($availableJobs as $availableJobName => $job):?>
		<tr> 
			<td><?php echo $job->getJobName() ?></td>
			<td><?php echo $job->getJobDescription() ?></td> 
			<td><?php if(!$job->invalid):?>
				<a href="<?php echo $this->action('install', $job->jHandle)?>" class="btn btn-small pull-right"><?php echo t('Install')?></a>
			<?php endif?></td>
		</tr>	
		<?php endforeach?>
	</tbody>
	</table>
<?php } ?>
<?php 
$djs = JobSet::getDefault();
if (is_object($djs)) { ?>
<div class="well">
<h4><?php echo t('Automation Instructions')?></h4>
<p><?php echo t('To run all the jobs in the <a href="%s">%s</a> Job Set, schedule this URL using cron or a similar system:', $this->url('/dashboard/system/optimization/jobs', 'edit_set', $djs->getJobSetID()), $djs->getJobSetDisplayName())?></p>
<div><input type="text" style="width: 700px" class="ccm-default-jobs-url" value="<?php echo BASE_URL . $this->url('/tools/required/jobs?auth=' . $auth)?>" /></div>
</div>
<?php } ?>

</div>

<?php } else { ?>

<div id="ccm-tab-content-sets">

<?php if (in_array($this->controller->getTask(), array('update_set', 'update_set_jobs', 'edit_set', 'delete_set'))) { ?>


		<div class="row">
		<div class="span-pane-half">

		<form class="form-vertical" method="post" action="<?php echo $this->action('update_set')?>">
			
			<input type="hidden" name="jsID" value="<?php echo $set->getJobSetID()?>" />

			<?php echo Loader::helper('validation/token')->output('update_set')?>

		<fieldset>
			<legend><?php echo t('Details')?></legend>

			<div class="control-group">
				<?php echo $form->label('jsName', t('Name'))?>
				<div class="controls">
					<?php echo $form->text('jsName', $set->getJobSetName())?>
				</div>
			</div>

			<div class="control-group">
				<label></label>
				<div class="controls">
					<?php echo $form->submit('submit', t('Update Set'), array('class' => ''))?>
				</div>
			</div>
		</fieldset>
		</form>


		<?php if ($set->canDelete()) { ?>

		<form method="post" action="<?php echo $this->action('delete_set')?>" class="form-vertical">
		<fieldset>
			<legend><?php echo t('Delete Set')?></legend>
			<div class="control-group">
			<div class="controls">
				<p><?php echo t('Warning, this cannot be undone. No jobs will be deleted but they will no longer be grouped together.')?></p>
			</div>
			</div>
			
			<input type="hidden" name="jsID" value="<?php echo $set->getJobSetID()?>" />
			<?php echo Loader::helper('validation/token')->output('delete_set')?>		
			<div class="clearfix">
				<?php echo $form->submit('submit', t('Delete Job Set'), array('class' => 'danger'))?>
			</div>
		</fieldset>
		</form>
		<?php } ?>
		</div>

		<div class="span-pane-half">
	
		<form class="form-vertical" method="post" action="<?php echo $this->action('update_set_jobs')?>">
			<input type="hidden" name="jsID" value="<?php echo $set->getJobSetID()?>" />
			<?php echo Loader::helper('validation/token')->output('update_set_jobs')?>

		<fieldset>
			<legend><?php echo t('Jobs')?></legend>
			
	
			<?php 
			$list = $set->getJobs();
			if (count($installedJobs) > 0) { ?>
	
				<div class="control-group">
					<div class="controls">
	
						<?php foreach($installedJobs as $g) { 	

						?>
								<label class="checkbox">
									<?php echo $form->checkbox('jID[]', $g->getJobID(), $set->contains($g)) ?>
									<span><?php echo $g->getJobName()?></span>
								</label>
						<?php } ?>
					</div>
				</div>
		
				<div class="control-group">
					<div class="controls">
					<?php echo $form->submit('submit', t('Update Jobs'), array('class' => ''))?>
					</div>
				</div>
			<?php } else { ?>
				<div class="control-group">
					<div class="controls">
						<p><?php echo t('No Jobs found.')?></p>
					</div>
				</div>
			<?php } ?>
		</fieldset>
		</form>
		</div>
	</div>

		<div class="well">
			<h4><?php echo t('Automation Instructions')?></h4>
			<form action="<?php echo $this->action('update_set_schedule');?>" method="post">
				<?php echo $form->hidden('jsID',$set->getJobSetID()); ?>
			<label class="radio">
				<input type="radio" name="isScheduled" class="ccm-jobs-automation-schedule-type" value="1" <?php echo ($set->isScheduled?'checked="checked"':'')?> />
				<?php echo t('When people browse to the page.  (which runs after the main rendering request of the page.)')?>
			</label>
			<fieldset class="ccm-jobs-automation-schedule-auto" <?php echo ($set->isScheduled?'':'style="display: none;"')?>>
				<div class="clearfix">
					<label><?php  echo t('Run this Job Every')?></label>
					<div class="input">
						<?php echo $form->text('value',$set->scheduledValue,array('class'=>'span2'))?>
						<?php echo $form->select('unit', array('hours'=>t('Hours'), 'days'=>t('Days'), 'weeks'=>t('Weeks'), 'months'=>t('Months')), $set->scheduledInterval, array('class'=>'span2'))?>
					</div>
				</div>
			</fieldset>
			
			<label class="radio">
				<input type="radio" name="isScheduled" class="ccm-jobs-automation-schedule-type" value="0" <?php echo ($set->isScheduled?'':'checked="checked"')?> />
				<?php echo t('Through Cron')?>
			</label>
			<fieldset class="ccm-jobs-automation-schedule-cron" <?php echo ($set->isScheduled?'style="display: none;"':'')?>>
				<p><?php echo t('To run all the jobs in this Job Set, schedule this URL using cron or a similar system:', $set->getJobSetID())?></p>
				<div><textarea style="width: 560px" rows="2" class="ccm-default-jobs-url"><?php echo BASE_URL . $this->url('/tools/required/jobs?auth=' . $auth . '&jsID=' . $set->getJobSetID())?></textarea></div>
			</fieldset>
			<div class="control-group">
				<div class="controls">
				<?php echo $form->submit('submit', t('Update Schedule'), array('class' => ''))?>
				</div>
			</div>
			</form>
		</div>


<?php } else { ?>

	<form method="post" class="form-horizontal" action="<?php echo $this->action('add_set')?>">


	<?php if (count($jobSets) > 0) { ?>
	
		<div class="ccm-attribute-sortable-set-list">
		
			<?php foreach($jobSets as $j) { ?>
				<div class="ccm-group" id="asID_<?php echo $j->getJobSetID()?>">
					<a class="ccm-group-inner" href="<?php echo $this->url('/dashboard/system/optimization/jobs', 'edit_set', $j->getJobSetID())?>" style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo $j->getJobSetDisplayName()?></a>
				</div>
			<?php } ?>
		</div>
	
	<?php } else { ?>
		<p><?php echo t('You have not added any Job sets.')?></p>
	<?php } ?>

	<br/>
	
	<h4><?php echo t('Add Set')?></h4>

	<?php echo Loader::helper('validation/token')->output('add_set')?>
	<div class="control-group">
		<?php echo $form->label('jsName', t('Name'))?>
		<div class="controls">
			<?php echo $form->text('jsName')?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?php echo t('Jobs')?></label>
		<div class="controls">
		<?php foreach($installedJobs as $j) { ?>
			<label class="checkbox"><?php echo $form->checkbox('jID[]', $j->getJobID())?> <span><?php echo $j->getJobName()?></span></label>			
		<?php } ?>
		</div>
	</div>
	
	<div class="control-group">
		<label></label>
		<div class="controls">
			<?php echo $form->submit('submit', t('Add Job Set'), array('class' => 'btn'))?>
		</div>
	</div>

	</form>

	<?php } ?>
</div>
<?php } ?>


<script type="text/javascript">

var pulseRowInterval = false;

jQuery.fn.showLoading = function() {
	if ($(this).find('button').attr('data-jSupportsQueue')) {
		$(this).find('button').html('<i class="icon-refresh"></i> <?php echo t('View')?>');
	} else {
		$(this).find('button').html('<i class="icon-refresh"></i> <?php echo t('Run')?>').prop('disabled', true);
	}
	var row = $(this);
	row.removeClass('error success');

	if (!row.attr('data-color')) {
		row.find('td').css('background-color', '#ccc');
	}
	pulseRowInterval = setInterval(function() {
		if (row.attr('data-color') == '#ccc') {
			row.find('td').css('background-color', '#fff');
			row.attr('data-color', '#fff');
		} else {
			row.find('td').css('background-color', '#ccc');
			row.attr('data-color', '#ccc');
		}			
	}, 500);
}

jQuery.fn.hideLoading = function() {
	$(this).find('button').html('<i class="icon-play"></i> <?php echo t('Run')?>').prop('disabled', false);
	var row = $(this);
	row.removeClass();
	row.find('td').css('background-color', '');
	row.attr('data-color', '');
	clearInterval(pulseRowInterval);
}

jQuery.fn.processResponse = function(r) {
	$(this).hideLoading();
	if (r.error) {
		$(this).addClass('error');
	} else {
		$(this).addClass('success');
	}
	$(this).find('.jDateLastRun').html(r.jDateLastRun);
	$(this).find('.jLastStatusText').html(r.result);
}

$(function() {
	$('tr.running').showLoading();
	$('.ccm-default-jobs-url').on('click', function() {
		$(this).get(0).select();
	});
	$('a.ccm-automate-job-instructions').on('click', $("#ccm-jobs-list"), function() {
		//if ($(this).attr('data-jSupportsQueue')) { }
		$('#jd' + $(this).attr("data-jID")).jqdialog({
			height: 550,
			width: 650,
			modal: true,
			title: <?php echo $jh->encode(t('Automation Instructions'))?>
		});
	});
	$('.icon-question-sign').tooltip();
	$('i[class=icon-tasks],i[class=icon-trash]').parent().tooltip();
	$('.btn-run-job').on('click', $('#ccm-jobs-list'), function() {
		var row = $(this).parent().parent();
		row.showLoading();
		var jSupportsQueue = $(this).attr('data-jSupportsQueue');
		var jID = $(this).attr('data-jID');
		var jName = $(this).attr('data-jName');
		var params = [
			{'name': 'auth', 'value': '<?php echo $auth?>'},
			{'name': 'jID', 'value': jID}
		];
		if (jSupportsQueue) {
			ccm_triggerProgressiveOperation(
				CCM_TOOLS_PATH + '/jobs/run_single',
				params,
				jName, function(r) {
					$('.ui-dialog-content').dialog('close');
					row.processResponse(r);
				}, function(r) {
					row.processResponse(r);
				}
			);
		} else {
			$.ajax({ 
				url: CCM_TOOLS_PATH + '/jobs/run_single',
				data: params,
				dataType: 'json',
				cache: false,
				success: function(json) {
					row.processResponse(json);
				}
			});
		}
	});
	
	$('.ccm-jobs-automation-schedule-type').click(function() {
		if($(this).val() == 1) {
			$(this).parent().siblings('.ccm-jobs-automation-schedule-cron').hide();
			$(this).parent().siblings('.ccm-jobs-automation-schedule-auto').show();
		} else {
			$(this).parent().siblings('.ccm-jobs-automation-schedule-auto').hide();
			$(this).parent().siblings('.ccm-jobs-automation-schedule-cron').show();
		}
	});
});
</script>
<?php echo $h->getDashboardPaneFooterWrapper();?>