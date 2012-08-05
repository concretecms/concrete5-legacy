<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

if (isset($error) && $error != '') {
    if ($error instanceof Exception) {
        $_error[] = $error->getMessage();
    } elseif ($error instanceof ValidationErrorHelper) {
        $_error = $error->getList();
    } elseif (is_array($error)) {
        $_error = $error;
    } elseif (is_string($error)) {
        $_error[] = $error;
    }
    ?>
    <?php if ($format == 'block') { ?>

    <div class="alert-message error">
    <?php foreach($_error as $e): ?>
        <?php echo $e?><br/>
    <?php endforeach; ?>
    </div>

    <?php } else { ?>

    <ul class="ccm-error">
    <?php foreach($_error as $e): ?>
        <li><?php echo $e?></li>
    <?php endforeach; ?>
    </ul>
    <?php } ?>

<?php }
