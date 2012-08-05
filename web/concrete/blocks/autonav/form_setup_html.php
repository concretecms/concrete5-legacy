<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
?>
<ul id="ccm-autonav-tabs" class="ccm-dialog-tabs">
    <li class="ccm-nav-active"><a id="ccm-autonav-tab-add" href="javascript:void(0);"><?=t('Edit')?></a></li>
    <li class=""><a id="ccm-autonav-tab-preview"  href="javascript:void(0);"><?=t('Preview')?></a></li>
</ul>

<div style="padding: 10px">

<div style="clear: both" class="ccm-autonavPane ccm-preview-pane" id="ccm-autonavPane-preview" style="display: none">

</div>
<div class="ccm-autonavPane" id="ccm-autonavPane-add">

<input type="hidden" name="autonavCurrentCID" value="<?=$c->getCollectionID()?>" />
<input type="hidden" name="autonavPreviewPane" value="<?=REL_DIR_FILES_TOOLS_BLOCKS?>/<?=$this->getBlockTypeHandle()?>/preview_pane.php" />

<strong><?=t('Pages Should Appear')?></strong><br>
<select name="orderBy">
    <option value="display_asc" <?php if ($info['orderBy'] == 'display_asc') { ?> selected<?php } ?>><?=t('in their sitemap order.')?></option>
    <option value="chrono_desc" <?php if ($info['orderBy'] == 'chrono_desc') { ?> selected<?php } ?>><?=t('with the most recent first.')?></option>
    <option value="chrono_asc" <?php if ($info['orderBy'] == 'chrono_asc') { ?> selected<?php } ?>><?=t('with the earliest first.')?></option>
    <option value="alpha_asc" <?php if ($info['orderBy'] == 'alpha_asc') { ?> selected<?php } ?>><?=t('in alphabetical order.')?></option>
    <option value="alpha_desc" <?php if ($info['orderBy'] == 'alpha_desc') { ?> selected<?php } ?>><?=t('in reverse alphabetical order.')?></option>
    <option value="display_desc" <?php if ($info['orderBy'] == 'display_desc') { ?> selected<?php } ?>><?=t('in reverse sitemap order.')?></option>
</select>
<br><br>
<strong><?=t('Viewing Permissions')?></strong><br/>
<?=$form->checkbox('displayUnavailablePages', 1, $info['displayUnavailablePages']); ?>
<?=t('Display pages to users even when those users cannot access those pages.')?>
<br/><br/>
<strong><?=t('Display Pages')?></strong><br>
<select name="displayPages" onchange="toggleCustomPage(this.value);">
    <option value="top"<?php if ($info['displayPages'] == 'top') { ?> selected<?php } ?>><?=t('at the top level.')?></option>
    <option value="second_level"<?php if ($info['displayPages'] == 'second_level') { ?> selected<?php } ?>><?=t('at the second level.')?></option>
    <option value="third_level"<?php if ($info['displayPages'] == 'third_level') { ?> selected<?php } ?>><?=t('at the third level.')?></option>
    <option value="above"<?php if ($info['displayPages'] == 'above') { ?> selected<?php } ?>><?=t('at the level above.')?></option>
    <option value="current"<?php if ($info['displayPages'] == 'current') { ?> selected<?php } ?>><?=t('at the current level.')?></option>
    <option value="below"<?php if ($info['displayPages'] == 'below') { ?> selected<?php } ?>><?=t('At the level below.')?></option>
    <option value="custom"<?php if ($info['displayPages'] == 'custom') { ?> selected<?php } ?>><?=t('Beneath a particular page')?></option>
</select>

<div id="ccm-autonav-page-selector"<?php if ($info['displayPages'] != 'custom') { ?> style="display: none"<?php } ?>>
<?php $form = Loader::helper('form/page_selector');
print $form->selectPage('displayPagesCID', $info['displayPagesCID']);
?>
</div>

<br><br>

<strong><?=t('Sub Pages to Display')?></strong><br>
<select name="displaySubPages" onchange="toggleSubPageLevels(this.value);">
    <option value="none"<?php if ($info['displaySubPages'] == 'none') { ?> selected<?php } ?>><?=t('None')?></option>
    <option value="relevant"<?php if ($info['displaySubPages'] == 'relevant') { ?> selected<?php } ?>><?=t('Relevant sub pages.')?></option>
    <option value="relevant_breadcrumb"<?php if ($info['displaySubPages'] == 'relevant_breadcrumb') { ?> selected<?php } ?>><?=t('Display breadcrumb trail.')?></option>
    <option value="all"<?php if ($info['displaySubPages'] == 'all') { ?> selected<?php } ?>><?=t('Display all.')?></option>
</select>
<br><br>

<strong><?=t('Sub-Page Levels')?></strong><br>
<select id="displaySubPageLevels" name="displaySubPageLevels" <?php if ($info['displaySubPages'] == 'none') { ?> disabled <?php } ?> onchange="toggleSubPageLevelsNum(this.value);">
    <option value="enough"<?php if ($info['displaySubPageLevels'] == 'enough') { ?> selected<?php } ?>><?=t('Display sub pages to current.')?></option>
    <option value="enough_plus1"<?php if ($info['displaySubPageLevels'] == 'enough_plus1') { ?> selected<?php } ?>><?=t('Display sub pages to current +1.')?></option>
    <option value="all"<?php if ($info['displaySubPageLevels'] == 'all') { ?> selected<?php } ?>><?=t('Display all.')?></option>
    <option value="custom"<?php if ($info['displaySubPageLevels'] == 'custom') { ?> selected<?php } ?>><?=t('Display a custom amount.')?></option>
</select>
<div id="divSubPageLevelsNum"<?php if ($info['displaySubPageLevels'] != 'custom') { ?> style="display: none"<?php } ?>>
    <br>
    <input type="text" name="displaySubPageLevelsNum" value="<?=$info['displaySubPageLevelsNum']?>" style="width: 30px; vertical-align: middle">
    &nbsp;<?=t('levels')?>
</div>
</div>
</div>
