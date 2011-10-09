<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 

$p = is_object($akcip) ? $akcip : $akcp;

?>

<?php
	if($p->canAdmin()){
	    $uh = Loader::helper('form/user_selector');	    
	    echo $uh->selectUser('uID', $owner->getUserID());
	}else{
		$ui = UserInfo::getByID($owner->getUserID());
		echo '<strong>'.$owner->getUserName().' / '.$ui->getUserEmail().'</strong>';
	} 
?>