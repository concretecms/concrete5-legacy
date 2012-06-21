<?php   defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardSystemSeoMetasTagsController extends Controller {	
	
	protected $csvKeys = array(
	'cID'=>'Page ID',
	'cName'=>'Page Name',
	'cHandle'=>'Page Handle',
	'meta_title'=>'Meta Title',
	'meta_description'=>'Meta Description',
	'meta_keywords'=>'Meta Keywords');
		
	public $helpers = array('form', 'concrete/interface', 'validation/token');
	
	public function getHeaderArray() { return $this->csvKeys; }

	public function view() {
		$this->set('csvKeys', $this->getHeaderArray());
	}

	public function export(){

		header('Content-type: application/octet-stream');
		header("Content-Disposition: attachment; filename=\"metas_tags_records.csv\"");
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Transfer-Encoding: binary");
				
		Loader::model('page_list');
		$pl = new PageList();
		$pages = $pl->get();
		
		foreach($pages as $row) {
			$cID = $row->getCollectionID();
			$cName = $row->getCollectionName();
			$cHandle = $row->getCollectionHandle();
			$mTitle = $row->getAttribute('meta_title');
			$mDesc = $row->getAttribute('meta_description');
			$mKey = $row->getAttribute('meta_keywords');
			
			$metas = array($cID, $cName, $cHandle, $mTitle, $mDesc, $mKey);
									
			echo '"'.implode('","',$metas).'"'.PHP_EOL;
		}
		exit;
	}
	
	public function import() {
		$valt = Loader::helper('validation/token');
		
		$error = array();
		
		if (!$valt->validate('upload_Metas_Tags')) {
			$error[] = $valt->getErrorMessage();
		}
		
		if (count($error) == 0) {
			$res = $this->importCSV();	

			if(!count($res)) {
				$this->redirect('/dashboard/system/seo/metas_tags','success');
			} else {
				$error = $res;
			}
		}
		$this->set('error',$error);
		$this->view();
	}
	
	public function success() {
		$this->set('message', t('Records uploaded successfully.'));
		$this->view();
	}
	
	protected function importCSV() {
		ini_set('auto_detect_line_endings',true );
		$errors = array();
		$row = 0;
		
		if (is_uploaded_file($_FILES['theFile']['tmp_name'])) {
			$row = 1;
			$handle = fopen($_FILES['theFile']['tmp_name'], "r");
						
			$headers = $this->getHeaderArray();
			
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				if (count($data) != count($headers)) {
					$errors[] = t("Error on line %s:",$row)." ".t("Number of fields don't match");
					break;
				} else {
					$page = Page::getByID($data[0]);
					
					if ($page->getCollectionID()){
						$page->update(array('cHandle'=>$data[2], 'cName'=>$data[1]));
						$page->setAttribute('meta_title', $data[3]);
						$page->setAttribute('meta_description', $data[4]);
						$page->setAttribute('meta_keywords', $data[5]);
					}
					
					$row++;
				}				
			}
			fclose($handle);
		} else {
			$errors[] = t('Unable to Upload File');
		}
		
		return $errors;
	}
}