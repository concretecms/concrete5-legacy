<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $title ?></title>
</head>
<body style="margin:0; padding:0; min-height:100%; height:100%;">
<style type="text/css">
html, body {
	min-height: 100%;
	height: 100%;
	color: #777777;
	font-family: Arial;
	font-size: 13px;
}
table {
	color: #777777;
}
h1,h2,h3,h4,h5{ margin:0px 0px 4px 0px; padding:4px 0px; margin-top:8px }
h1{ font-family: Arial; font-size: 32px; line-height:28px; color: #999999; }
h2{ font-size:18px; line-height:24px }
h3{ font-size:16px; line-height:21px }
h4{ font-size:14px; line-height:18px }
h5{ font-size:13px; line-height:16px }
p{ padding:0px 0px 0px 0px; margin:0px 0px 12px 0px; }
a,a:visited,a:active {
	color: #66aa33;
}
a:hover {
	color: #66CC00;
}
</style>
<!-- outer container -->
<table cellpadding="0" cellspacing="0" bgcolor="#ffffff" color="#777777" width="100%" height="100%" border="0" style="margin:0; padding:0; font-family:Arial; font-size:13px; color:#777777;"><tr valign="top"><td align="center">
<?php if (isset($alternateEmailUrl)) : ?>
	<p><a href="<?php echo $alternateEmailUrl ?>"><?php echo t("Click here if this message is not displaying correctly.") ?></a></p>
<?php endif; ?>
<!-- inner container -->
<table align="center" border="0" cellpadding="0" cellspacing="0" width="720" bgcolor="#ffffff" style="padding:10px 20px;"><tr><td>
	<?php Loader::element("mail_html_header"); ?>
	<table style="width:100%;">
		<tr>
			<td style="border-bottom: 1px dotted #777777;"><h1><?php echo $title; ?></h1></td>
		</tr>
	</table>
	<table cellpadding="0" cellpadding="0" style="width:100%;"><tr><td style="border-bottom:1px dotted #777777; padding-top:20px; padding-bottom:20px; color:#777777;">
		<?php echo $bodyHTML ?>
	</td></tr></table>
	<table cellpadding="0" cellspacing="0" style="width:100%; font-size:10px; padding-top:20px;"><tr><td>
		&copy; <?php echo date("Y") ?> <a target="_blank" href="<?php echo BASE_URL . DIR_REL ?>"><?php echo SITE ?></a>
	</td></tr></table>
	<?php Loader::element("mail_html_footer"); ?>
</tr></td></table>
<!-- inner container end -->
<?php if (isset($alternateEmailUrl)) : ?>
	<p><a href="<?php echo $alternateEmailUrl ?>"><?php echo t("Click here if this message is not displaying correctly.") ?></a></p>
<?php endif; ?>
</td></tr></table>
<!-- outer container end -->
</body>
</html>