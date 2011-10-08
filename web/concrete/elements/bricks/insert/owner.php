<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 

?>

<?php 
	global $u;
	if($akcip->canAdmin()){
	    $uh = Loader::helper('form/user_selector');	    
	    echo $uh->selectUser('uID', $u->uID);
	}else{
		$ui = UserInfo::getByID($u->getUserID());
		echo '<strong>'.$u->getUserName().' / '.$ui->getUserEmail().'</strong>';
	} 
?>
