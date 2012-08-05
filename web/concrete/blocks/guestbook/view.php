<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $c = Page::getCurrentPage(); ?>
<h4 class="guestBook-title"><?=$controller->title?></h4>
<?php if ($invalidIP) { ?>
<div class="ccm-error"><p><?=$invalidIP?></p></div>
<?php } ?>
<?php
$u = new User();
if (!$dateFormat) {
    $dateFormat = t('M jS, Y');
}
$posts = $controller->getEntries();
$bp = $controller->getPermissionsObject();
foreach ($posts as $p) { ?>
    <?php if ($p['approved'] || $bp->canWrite()) { ?>
    <div class="guestBook-entry<?php if ($c->getVersionObject()->getVersionAuthorUserName() == $u->getUserName()) {?> authorPost <?php }?>">
        <?php if ($bp->canWrite()) { ?>
                <div class="guestBook-manage-links">
                    <a href="<?=$this->action('loadEntry')."&entryID=".$p['entryID'];?>#guestBookForm"><?=t('Edit')?></a> |
                    <a href="<?=$this->action('removeEntry')."&entryID=".$p['entryID'];?>" onclick="return confirm('<?=t("Are you sure you would like to remove this comment?")?>');"><?=t('Remove')?></a> |
                    <?php if ($p['approved']) { ?>
                            <a href="<?=$this->action('unApproveEntry')."&entryID=".$p['entryID'];?>"><?=t('Un-Approve')?></a>
                    <?php } else { ?>
                        <a href="<?=$this->action('approveEntry')."&entryID=".$p['entryID'];?>"><?=t('Approve')?></a>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="contentByLine">
                <?=t('Posted by')?>
                <span class="userName">
                    <?php
                    if ( intval($p['uID']) ) {
                        $ui = UserInfo::getByID(intval($p['uID']));
                        if (is_object($ui)) {
                            echo $ui->getUserName();
                        }
                    }else echo $p['user_name'];
                    ?>
                </span>
                <?=t('on')?>
                <span class="contentDate">
                    <?=date($dateFormat,strtotime($p['entryDate']));?>
                </span>
            </div>
            <?=nl2br($p['commentText'])?>
    </div>
    <?php } ?>
<?php }

 if (isset($response)) { ?>
    <?=$response?>
<?php } ?>
<?php if ($controller->displayGuestBookForm) { ?>
    <?php
    if ( $controller->authenticationRequired && !$u->isLoggedIn() ) { ?>
        <div><?=t('You must be logged in to leave a reply.')?> <a href="<?=View::url("/login","forward",$c->getCollectionID())?>"><?=t('Login')?> &raquo;</a></div>
    <?php } else { ?>
        <a name="guestBookForm-<?=$controller->bID?>"></a>

        <div id="guestBook-formBlock-<?=$controller->bID?>" class="guestBook-formBlock">

            <h5 class="guestBook-formBlock-title"><?php echo t('Leave a Reply')?></h5>
            <form method="post" action="<?=$this->action('form_save_entry', '#guestBookForm-'.$controller->bID)?>">
            <?php if (isset($Entry->entryID)) { ?>
                <input type="hidden" name="entryID" value="<?=$Entry->entryID?>" />
            <?php } ?>

            <?php if (!$controller->authenticationRequired) { ?>
                <label for="name"><?=t('Name')?>:</label><?=(isset($errors['name'])?"<span class=\"error\">".$errors['name']."</span>":"")?><br />
                <input type="text" name="name" value="<?=$Entry->user_name ?>" /> <br />
                <label for="email"><?=t('Email')?>:</label><?=(isset($errors['email'])?"<span class=\"error\">".$errors['email']."</span>":"")?><br />
                <input type="email" name="email" value="<?=$Entry->user_email ?>" /> <span class="note">(<?=t('Your email will not be publicly displayed.')?>)</span> <br />
            <?php } ?>

            <?=(isset($errors['commentText'])?"<br /><span class=\"error\">".$errors['commentText']."</span>":"")?>
            <textarea name="commentText"><?=$Entry->commentText ?></textarea><br />
            <?php
            if ($controller->displayCaptcha) {

                $captcha = Loader::helper('validation/captcha');
                   $captcha->label();
                   $captcha->showInput();
                $captcha->display();

                echo isset($errors['captcha'])?'<span class="error">' . $errors['captcha'] . '</span>':'';

            }
            ?>
            <br/><br/>
            <input type="submit" name="Post Comment" value="<?=t('Post Comment')?>" class="button"/>
            </form>
        </div>
    <?php } ?>
<?php } ?>
