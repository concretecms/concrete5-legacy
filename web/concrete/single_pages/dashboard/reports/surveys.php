<?php
defined('C5_EXECUTE') or die("Access Denied."); 

// Helpers
$ih = Loader::helper('concrete/interface');

// Content
if ($this->controller->getTask() == 'viewDetail') { ?>

    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Results for &#34;%s&#34;', $current_survey), false, false, false);?>
    
	<div class="ccm-pane-body">
    
    	<div class="row">
    
          <div class="span10">
      
            <table class="table table-striped">
              <thead>
                <tr>
                    <th><?php echo t('Option')?></th>
                    <th><?php echo t('IP Address')?></th>
                    <th><?php echo t('Date')?></th>
                    <th><?php echo t('User')?></th>
                </tr>
              </thead>
              <tbody>
                <?php 
                foreach($survey_details as $detail) { ?>
                <tr>
                    <td><?php echo $detail['option'] ?></td>
                    <td><?php echo $detail['ipAddress'] ?></td>
                    <td><?php echo $detail['date'] ?></td>
                    <td><?php echo $detail['user'] ?></td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
        
          </div>
          
          <div class="span5" style="margin-left:30px;">
      
            <div style="text-align:center;">
              <?php echo $pie_chart ?>
              <?php echo $chart_options ?>              
            </div>
        
          </div>
        
        </div>
        
	</div>
    
    <div class="ccm-pane-footer">
        <?php print $ih->button(t('Back to List'), $this->action('view'), 'left'); ?>
    </div>
    
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<?php } else { ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Surveys'), false, false);?>
	
	<?php if (count($surveys) == 0) { ?>
	<?php echo "<p>".t('You have not created any surveys.')."</p>" ?>
	<?php } else { ?>

		<table class="table table-striped">
        	<thead>
                <tr>
                    <th class="<?php echo $surveyList->getSearchResultsClass('question')?>"><a href="<?php echo $surveyList->getSortByURL('question', 'asc')?>"><?php echo t('Name')?></a></th>
                    <th class="<?php echo $surveyList->getSearchResultsClass('cvName')?>"><a href="<?php echo $surveyList->getSortByURL('cvName', 'asc')?>"><?php echo t('Found on Page')?></a></th>
                    <th class="<?php echo $surveyList->getSearchResultsClass('lastResponse')?>"><a href="<?php echo $surveyList->getSortByURL('lastResponse', 'desc')?>"><?php echo t('Last Response')?></a></th>
                    <th class="<?php echo $surveyList->getSearchResultsClass('numberOfResponses')?>"><a href="<?php echo $surveyList->getSortByURL('numberOfResponses', 'desc')?>"><?php echo t('Number of Responses')?></a></th>
                </tr>
            </thead>
            <tbody>
			<?php foreach($surveys as $survey) { ?>
					<tr>
						<td><strong><a href="<?php echo $this->action('viewDetail', $survey['bID'], $survey['cID'])?>"><?php echo $survey['question'] ?></a></strong></td>
						<td><?php echo $survey['cvName'] ?></td>
						<td><?php echo $this->controller->formatDate($survey['lastResponse']) ?></td>
						<td><?php echo $survey['numberOfResponses'] ?></td>
					</tr>
				<?php }
			} ?>
            </tbody>
		</table>
		
		<?php $surveyList->displayPagingV2(); ?>
    
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>

<?php } ?>