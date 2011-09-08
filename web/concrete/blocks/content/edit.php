<?
defined('C5_EXECUTE') or die("Access Denied.");
//$replaceOnUnload = 1;
$bt->inc('editor_init.php');
?>

<div style="text-align: center" id="ccm-editor-pane">
<textarea id="ccm-content-<?=$b->getBlockID()?>-<?=$a->getAreaID()?>" class="advancedEditor ccm-advanced-editor" name="content"><?=htmlentities($controller->getContentEditMode(),ENT_QUOTES,APP_CHARSET)?></textarea>
</div>