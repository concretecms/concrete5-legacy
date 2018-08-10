<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo APP_CHARSET?>" />
<!-- insert CSS for Default Concrete Theme //-->
<style type="text/css">@import "<?php echo ASSETS_URL_CSS?>/ccm.default.theme.css";</style>
<style type="text/css">@import "<?php echo ASSETS_URL_CSS?>/ccm.app.css";</style>

</head>
<body>

<div id="ccm-logo"><?php echo Loader::helper('concrete/interface')->getToolbarLogoSRC()?></div>

<div id="ccm-theme-wrapper" class="ccm-ui">
<?php				Loader::element('error_fatal', array('innerContent' => $innerContent, 
					'titleContent' => $titleContent));
?>
<p><a href="<?php echo BASE_URL.DIR_REL?>" class="btn"><?php echo t('&lt; Back to Home')?></a></p>
</div>

</body>
</html>
