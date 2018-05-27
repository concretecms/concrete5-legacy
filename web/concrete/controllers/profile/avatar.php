<?php
defined('C5_EXECUTE') or die("Access Denied.");
Loader::controller('/profile/edit');

class ProfileAvatarController extends Concrete5_Controller_Profile_Avatar {
	
	  public function crop_and_save_avatar()
  {
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
  public function upload_avatar()
  {
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
          $fs = FileSet::createAndGetSet("ORIGINAL_AVATAR", FileSet::TYPE_PUBLIC);
          $newFile = $fi->import($_FILES['Filedata']['tmp_name'], $_FILES['Filedata']['name']);
          $fs->addFileToSet($newFile);
          $this->set('targetImage',$newFile->getRelativePath());
          $this->set('targetImageId',$newFile->getFileID());
          $this->set('targetImageType',"data:".$_FILES['Filedata']['type'].";base64,".base64_encode(file_get_contents($_FILES['Filedata']['tmp_name'])));
          $sizearray = getimagesize($_FILES['Filedata']['tmp_name']); //seems we have a var to retrieve the name
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
