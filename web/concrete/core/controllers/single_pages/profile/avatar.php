<?php defined('C5_EXECUTE') or die("Access Denied.");
Loader::controller('/profile/edit');

class Concrete5_Controller_Profile_Avatar extends ProfileEditController {
	
	public function __construct(){
		parent::__construct();
		$html = Loader::helper('html');
		Loader::model("file_set");
		$this->set('av', Loader::helper('concrete/avatar'));
		$this->addFooterItem($html->javascript('jquery.jcrop.js'));
		$this->addHeaderItem($html->css('jquery.jcrop.css'));
		$this->addHeaderItem('<script type="text/javascript" src="'.REL_DIR_FILES_TOOLS_REQUIRED.'/i18n_js"></script>');
		$this->addFooterItem($html->javascript("ccm.app.js"));
		
	    $fs = FileSet::getByName('ORIGINAL_AVATAR');
		if (!$fs) { $fs = FileSet::createAndGetSet('ORIGINAL_AVATAR', FileSet::TYPE_PUBLIC, $uid = true); }// $uID = true, Only One avatar upload fileset!
	}

	
	public function save_thumb(){
		$ui = $this->get('ui');
		if (!is_object($ui) || $ui->getUserID() < 1) {
			return false;
		}
		
		if(isset($_POST['thumbnail']) && strlen($_POST['thumbnail'])) {
			$thumb = base64_decode($_POST['thumbnail']);
			$fp = fopen(DIR_FILES_AVATARS."/".$ui->getUserID().".jpg","w");
			if($fp) {
				fwrite($fp,base64_decode($_POST['thumbnail']));
				fclose($fp);
				$data['uHasAvatar'] = 1;
				$ui->update($data);
			}
		}	
		
		$this->redirect('/profile/avatar', 'saved');
	}
	
	
	public function saved() {
		$this->set('message', 'Avatar updated!');
	}

	
	public function deleted() {
		$this->set('message', 'Avatar removed.');
	}
	
	
	public function delete(){ 
		$ui = $this->get('ui');
		$av = $this->get('av');
		
		$av->removeAvatar($ui);
		$this->redirect('/profile/avatar', 'deleted');
	}
	
	
	public function crop_and_save_avatar(){
		$valt = Loader::helper('validation/token');
		$av = Loader::helper('concrete/avatar');
		$error = "";
		$errorCode = -1;
		if ($valt->validate('crop_n_save_upload')) {
			$fv = File::getByID($_POST['imageId'])->getRecentVersion();
			//crop it
			$img_o = imagecreatefromstring(file_get_contents($fv->getPath()));
			$target_w = ceil($_POST['w']/$_POST["shown_w"]*$_POST["real_w"]);
			$target_h = ceil($_POST['h']/$_POST["shown_h"]*$_POST["real_h"]);
			$dst_r = ImageCreateTrueColor($target_w ,$target_h);
			imagecopyresampled($dst_r,$img_o,0,0,ceil($_POST['x']/$_POST["shown_w"]*$_POST["real_w"]),ceil($_POST['y']/$_POST["shown_h"]*$_POST["real_h"]),
			$target_w,$target_h,$target_w,$target_h);
			imagejpeg( $dst_r, $fv->getPath(), 90);
			// free up unused memmory (if images are expected to be large)
			unset($img_o);
			unset($dst_r);
			$u = new User();
			$av->updateUserAvatar($fv->getPath(),$u->getUserID());
		}
		
		$this->redirect("/profile/avatar");
	}
	
	
	public function upload_avatar(){
		$valt = Loader::helper('validation/token');
		Loader::library("file/importer");
		$fi = new FileImporter();
		$error = "";
		$errorCode = -1;
		if ($valt->validate('upload')) {
			if (isset($_FILES['Filedata']) && (is_uploaded_file($_FILES['Filedata']['tmp_name']))) {
				if(!strstr($_FILES['Filedata']['type'],"image")) {
					$errorCode = FileImporter::E_FILE_INVALID_EXTENSION;
					$error = t("Please upload an image.");
				} else {
					//We give the imagedata directly to the view
		  
					Loader::model("file_set");
					$fs = FileSet::getByName('ORIGINAL_AVATAR');
					$newFile = $fi->import($_FILES['Filedata']['tmp_name'], $_FILES['Filedata']['name']);
					$fs->addFileToSet($newFile);
					$this->set('targetImage',$newFile->getRelativePath());
					$this->set('targetImageId',$newFile->getFileID());
					$this->set('targetImageType',"data:".$_FILES['Filedata']['type'].";base64,".base64_encode(file_get_contents($_FILES['Filedata']['tmp_name'])));
					$sizearray = @getimagesize($_FILES['Filedata']['tmp_name']); //seems we have a var to retrieve the name
          
					$this->set('targetImageWidth',$sizearray[0]);
					$this->set('targetImageHeight',$sizearray[1]);
				}
			} else {
				$errorCode = $_FILES['Filedata']['error'];
			}
		} else if (isset($_FILES['Filedata'])) {
			// first, we check for validate upload token. If the posting of a file fails because of
			// post_max_size then this may not even be set, leading to misleading errors
			$error = $valt->getErrorMessage();
		} else {
			$errorCode = FileImporter::E_PHP_FILE_ERROR_DEFAULT;
		}
		if ($errorCode > -1 && $error == '') {
			$error = FileImporter::getErrorMessage($errorCode);
		}
		$this->set('error',$error);
	}
	
	
}

?>
