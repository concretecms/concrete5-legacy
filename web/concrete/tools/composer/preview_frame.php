<?php
defined('C5_EXECUTE') or die("Access Denied.");

$previewCID = Loader::helper('security')->sanitizeInt($_REQUEST['previewCID']);
$url = Loader::helper('concrete/urls')->getToolsURL('/composer/preview');
$url = Loader::helper('url')->setVariable('previewCID', $previewCID, $url);
?>
<iframe id="previewComposerDraft<?php echo time()?>" height="100%" style="width:100%; border:0px; " src="<?php echo $url?>"></iframe>
