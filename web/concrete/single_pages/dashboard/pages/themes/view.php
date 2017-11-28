<?php
defined('C5_EXECUTE') or die("Access Denied.");

// HELPERS
$bt = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');
$form = Loader::helper("form");
$alreadyActiveMessage = t('This theme is currently active on your site.');

?>
	
	<?php if (isset($activate_confirm)) { ?>
    
    <?php
	
	// Confirmation Dialogue.
	// Separate inclusion of dashboard header and footer helpers to allow for more UI-consistant 'cancel' button in pane footer, rather than alongside activation confirm button in alert-box.
	
	?>
    
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Themes'), false, 'span10 offset1', false);?>
    
    <div class="ccm-pane-body">
    
        <div class="alert-message block-message error" style="margin-bottom:0px;">
            
            <h5>
                <strong><?php echo t('Apply this theme to every page on your site?')?></strong>
            </h5>
            
        </div>
    
    </div>
    
    <div class="ccm-pane-footer">
        <?php echo $bt->button(t("Ok"), $activate_confirm, 'right', 'primary');?>            
    	<?php echo $bt->button(t('Cancel'), $this->url('/dashboard/pages/themes/'), 'left');?>
    </div>
    
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
    	
    
	<?php } else { ?>
    
    <?php
	
	// Themes listing / Themes landing page.
	// Separate inclusion of dashboard header and footer helpers - no pane footer.
	
	?>
    
    <?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Themes'), false, 'span10 offset1');?>
	
	<h3><?php echo t('Currently Installed')?></h3>
	
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
	<?php
	if (count($tArray) == 0) { ?>
		
        <tbody>
            <tr>
                <td><p><?php echo t('No themes are installed.')?></p></td>
            </tr>        
		</tbody>
    
	<?php } else { ?>
    
    	<tbody>
		
		<?php foreach ($tArray as $t) { ?>        
        
            <tr <?php if ($siteThemeID == $t->getThemeID()) { ?> class="ccm-theme-active" <?php } ?>>
                
                <td>
					<div class="ccm-themes-thumbnail" style="padding:4px;background-color:#FFF;border-radius:3px;border:1px solid #DDD;">
						<?php echo $t->getThemeThumbnail()?>
					</div>
				</td>
                
                <td width="100%" style="vertical-align:middle;">
                
                    <p class="ccm-themes-name"><strong><?php echo $t->getThemeDisplayName()?></strong></p>
                    <p class="ccm-themes-description"><em><?php echo $t->getThemeDisplayDescription()?></em></p>
                    
                    <div class="ccm-themes-button-row clearfix">
                    <?php if ($siteThemeID == $t->getThemeID()) { ?>
                        <?php echo $bt->button_js(t("Activate"), "alert('" . $alreadyActiveMessage . "')", 'left', 'primary ccm-button-inactive', array('disabled'=>'disabled'));?>
                    <?php } else { ?>
                        <?php echo $bt->button(t("Activate"), $this->url('/dashboard/pages/themes','activate', $t->getThemeID()), 'left', 'primary');?>
                    <?php } ?>
                        <?php echo $bt->button_js(t("Preview"), "ccm_previewInternalTheme(1, " . intval($t->getThemeID()) . ",'" . addslashes(str_replace(array("\r","\n",'\n'),'',$t->getThemeDisplayName())) . "')", 'left');?>
                        <?php echo $bt->button(t("Inspect"), $this->url('/dashboard/pages/themes/inspect', $t->getThemeID()), 'left');?>
                        <?php echo $bt->button(t("Customize"), $this->url('/dashboard/pages/themes/customize', $t->getThemeID()), 'left');?>
                    
                        <?php echo $bt->button(t("Remove"), $this->url('/dashboard/pages/themes', 'remove', $t->getThemeID(), $valt->generate('remove')), 'right', 'error');?>
                    </div>
                
                </td>
            </tr>
            
		<?php } ?>
        
        </tbody>
        
	<?php } ?>
    
    </table>
    
    
    <form method="post" action="<?php echo $this->action('save_mobile_theme')?>" class="form-horizontal">
    <h3><?php echo t("Mobile Theme")?></h3>
    <p><?php echo t("To use a separate theme for mobile browsers, specify it below.")?></p>
    
    <div class="control-group">
    <?php echo $form->label('MOBILE_THEME_ID', t('Mobile Theme'))?>
    <div class="controls">
    	<?php $themes[0] = t('** Same as website (default)'); ?>
    	<?php foreach($tArray as $pt) {
    		$themes[$pt->getThemeID()] = $pt->getThemeDisplayName();
    	} ?>
    	<?php echo $form->select('MOBILE_THEME_ID', $themes, Config::get('MOBILE_THEME_ID'))?>
    	<?php echo $form->submit('save_mobile_theme', t('Save'))?>
    </div>
    </div>
    </form>
    <br/><br/>
    
    
	<?php 
	if (count($tArray2) > 0) { ?>

	<h3><?php echo t('Themes Available to Install')?></h3>
	

	<table class="table">
		<tbody>
		<?php foreach ($tArray2 as $t) { ?>
            <tr>
                
                <td>
					<div class="ccm-themes-thumbnail" style="padding:4px;background-color:#FFF;border-radius:3px;border:1px solid #DDD;">
						<?php echo $t->getThemeThumbnail()?>
					</div>
				</td>
                
                <td width="100%" style="vertical-align:middle;">
                <p class="ccm-themes-name"><strong><?php echo $t->getThemeDisplayName()?></strong></p>
                <p class="ccm-themes-description"><em><?php echo $t->getThemeDisplayDescription()?></em></p>
                
                <div class="ccm-themes-button-row clearfix">
                <?php echo $bt->button(t("Install"), $this->url('/dashboard/pages/themes','install',$t->getThemeHandle()),'left','primary');?>
                </div>
                </td>
                
            </tr>
        <?php } // END FOREACH ?>
        
        </tbody>
	</table>
    
    <!-- END AVAILABLE TO INSTALL -->
			
	<?php } // END 'IF AVAILABLE' CHECK ?>
    
    <?php if (ENABLE_MARKETPLACE_SUPPORT == true) { ?>

	<div class="well" style="padding:10px 20px;">
        <h3><?php echo t('Want more themes?')?></h3>
        <p><?php echo t('You can download themes and add-ons from the concrete5 marketplace.')?></p>
        <p><a class="btn success" href="<?php echo $this->url('/dashboard/extend/themes')?>"><?php echo t("Get More Themes")?></a></p>
    </div>
    
    <?php } ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
	
	<?php } // END 'ELSE' DEFAULT LISTING ?>	