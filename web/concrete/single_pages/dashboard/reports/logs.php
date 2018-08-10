<?php 
defined('C5_EXECUTE') or die("Access Denied.");

// HELPERS
$valt = Loader::helper('validation/token');
$th = Loader::helper('text');
$dh = Loader::helper('date');


// VARIABLES

// Check if entries to show, assign to boolean var.
$areEntries = count($entries) > 0 ? true : false;

?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Logs'), false, false, false);?>
    
    <?php if(!$areEntries) { ?>
    
    <div class="ccm-pane-body ccm-pane-body-footer">
    
    	<p><?php echo t('There are no log entries to show at the moment.')?></p>
    
    </div>
    
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
    
    <?php } else { ?>
    
    <div class="ccm-pane-options ccm-pane-options-permanent-search">
    	<form method="post" id="ccm-log-search"  action="<?php echo $pageBase?>">
        	<div class="row">
                <div class="span5">
                    <label for="keywords"><?php echo t('Keywords')?></label>
                    <div class="input">
                        <?php echo $form->text('keywords', $keywords, array('style'=>'width:180px;'))?>
                    </div>
                </div>
                <div class="span6">
                    <label for="logType"><?php echo t('Type')?></label>
                    <div class="input">
                        <?php echo $form->select('logType', $logTypes, array('style'=>'width:180px;'))?>
                    <?php echo $form->submit('search',t('Search') )?>
                    </div>
                </div>
            </div>
        </form>
    </div>
        
	<div class="ccm-pane-body <?php if(!$paginator || !strlen($paginator->getPages())>0) { ?>ccm-pane-body-footer <?php } ?>">

        <table class="table table-bordered">
        	<thead>
                <tr>
                    <th class="subheaderActive"><?php echo t('Date/Time')?></th>
                    <th class="subheader"><?php echo t('Type')?></th>
                    <th class="subheader"><?php echo t('User')?></th>
                    <th class="subheader"><input style="float: right" class="btn error btn-mini" type="button" onclick="if (confirm('<?php echo t("Are you sure you want to clear this log?")?>')) { location.href='<?php echo $this->url('/dashboard/reports/logs', 'clear', $valt->generate(), $_POST['logType'])?>'}" value="<?php echo t('Clear Log')?>" /><?php echo t('Text')?></th>
                </tr>
			</thead>
            <tbody>
				<?php foreach($entries as $ent) { ?>
                <tr>
                    <td valign="top" style="white-space: nowrap" class="active"><?php
                        echo $dh->formatPrettyDateTime($ent->getTimestamp(), false, true);
                    ?></td>
                    <td valign="top"><strong><?php echo $ent->getType()?></strong></td>
                    <td valign="top"><strong><?php
                    $uID = $ent->getUserID();
                    if(empty($uID)) {
                        echo t("Guest");
                    }
                    else {
                        $u = User::getByUserID($uID);
                        if(is_object($u)) {
                            echo $u->getUserName();
                        }
                        else {
                            echo tc('Deleted user', 'Deleted (id: %s)', $uID);
                        }
                    }
                    ?></strong></td>
                    <td style="width: 100%"><?php echo $th->makenice($ent->getText())?></td>
                </tr>
                <?php } ?>
			</tbody>
		</table>
    
    </div>
    <!-- END Body Pane -->
    
	<?php if($paginator && strlen($paginator->getPages())>0){ ?>
    <div class="ccm-pane-footer">
        
        	<div class="pagination">
              <ul>
                  <li class="prev"><?php echo $paginator->getPrevious()?></li>
                  
                  <?php // Call to pagination helper's 'getPages' method with new $wrapper var ?>
                  <?php echo $paginator->getPages('li')?>
                  
                  <li class="next"><?php echo $paginator->getNext()?></li>
              </ul>
			</div>


	</div>
        <?php } // PAGINATOR ?>
    
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
    
    <?php } ?>
