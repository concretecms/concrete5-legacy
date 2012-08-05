<?php defined('C5_EXECUTE') or die(_("Access Denied."));
$navItems = $controller->getNavItems();
?>

<ul class="nav-header">

<?php foreach ($navItems as $ni) {

    $classes = array();
    if ($ni->isCurrent) {
        $classes[] = 'nav-selected';
    }
    if ($ni->inPath) {
        $classes[] = 'nav-path-selected';
    }
    if ($ni->isFirst) {
        $classes[] = 'first';
    }
    $classes = implode(" ", $classes);
    ?>

    <li class="<?=$classes?>">
        <a class="<?=$classes?>" href="<?=$ni->url?>" target="<?=$ni->target?>"><?=$ni->name?></a>
    </li>
<?php } ?>

</ul>

<div class="ccm-spacer">&nbsp;</div>
