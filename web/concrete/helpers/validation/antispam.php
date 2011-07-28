<?php 

defined('C5_EXECUTE') or die("Access Denied.");
class ValidationAntispamHelper {
	protected $library='akismet';
	public function __construct() {
		$q="SELECT saslHandle FROM SystemAntiSpamLibraries WHERE saslIsDefault=1";
    	$antispamHandle=$db->getOne($q);
		$this->library=$antispamHandle;
		parent::__construct();
	}
	public static function addLibrary($libraryHandle, $libraryName, $pkgHandle=null){
		$db = Loader::db();
    	$q="INSERT into SystemAntiSpamLibraries (saslHandle, saslName, saslDefault, pkgID) VALUES (?, ?, ?, ?)";
    	$v=array($libraryHandle, $libraryName, 0, $pkgHandle->getPackageID());
    	$db->query($q, $v);
	}
	public function check($value){
		$antiSpam=loader::helper('validation/antispam'.$this->library.'/library');
		$antiSpam->check($value);
	}	
	
}

?>
