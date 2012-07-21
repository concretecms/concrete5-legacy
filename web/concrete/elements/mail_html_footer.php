<?php defined('C5_EXECUTE') or die("Access Denied.");
$app = WHITE_LABEL_APP_NAME ? WHITE_LABEL_APP_NAME : 'concrete5';

$src = BASE_URL . ASSETS_URL_IMAGES . '/logo_menu.png';
if (WHITE_LABEL_LOGO_SRC) {
	$src = WHITE_LABEL_LOGO_SRC;
} else if (file_exists(DIR_BASE . '/' . DIRNAME_IMAGES . '/' . $filename)) {
	$src = DIRNAME_IMAGES . '/' . $filename;
}
if (strpos($src, "http://") === false && strpos($src, "https://") === false) {
	$src = BASE_URL . DIR_REL . '/' . trim($src, "/");
}
if (MAIL_TPL_HTML_SYSTEM_FOOTER) :
?>
<!-- common mail footer -->
<table width="100%" style="padding-top:30px;">
	<tr valign="bottom">
		<td width="95%" align="right">
			<div style="padding-bottom:10px;"><?php echo t("Mail sent through %s website", $app) ?></div>
		</td>
		<td>
			<img src="<?php echo $src ?>" alt="<?php echo $app ?>" />
		</td>
	</tr>
</table>
<?php endif; ?>
</div>