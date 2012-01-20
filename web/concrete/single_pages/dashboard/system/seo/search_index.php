<?php defined('C5_EXECUTE') or die('Access Denied');
/* @var $form FormHelper */
$form = Loader::helper('form');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Search Index'), t('Choose which areas on your site are indexed.'), 'span12 offset2', false); ?>
	<form method="post" id="ccm-search-index-manage" action="<?=$this->action('')?>">
		<div class="ccm-pane-body">
			<?php echo $this->controller->token->output('update_search_index');?>
			
			<div class="clearfix">
				<label id="indexingMethod"><?php echo t('Indexing Method')?></label>
				<?php 
					$selected = IndexedSearch::getSearchableAreaAction();
				?>
				<div class="input">
					<ul class="inputs-list">
						<li>
							<label>
								<?=$form->radio('SEARCH_INDEX_AREA_METHOD', 'whitelist', $selected)?>
								<span><?php echo t('Whitelist: Selected areas are only areas indexed.')?></span>
							</label>	
						</li>
						<li>
							<label>
								<?=$form->radio('SEARCH_INDEX_AREA_METHOD', 'blacklist', $selected)?>
								<span><?php echo t('Blacklist: Every area but the selected areas are indexed.')?></span>
							</label>
						</li>
					</ul>
				</div>
			</div>

			<div class="clearfix">
				<label><?php echo t('Areas')?></label>
				<div class="input">
					<ul class="inputs-list">
					<?php foreach($areas as $a) { ?>
						<li>
							<label>
								<?=$form->checkbox('arHandle[]', $a, in_array($a, $selectedAreas))?>
								<span><?=$a?></span>
							</label>
						</li>
					<?php } ?>
					</ul>
				</div>
			</div>

		</div>
		<div class="ccm-pane-footer">
			<?php
			$ih = Loader::helper('concrete/interface');
			print $ih->submit(t('Save'), 'ccm-search-index-manage', 'right', 'primary');
			?>
		</div>
	</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false); ?>