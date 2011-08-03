<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksAddController extends Controller {
	
	public function view($packageHandle = NULL) {
		$subnav = array(
			array(View::url('dashboard/bricks'), t('Categories')),
			array(View::url('dashboard/bricks/structure'), t('Attribute Management')),
			array(View::url('dashboard/bricks/permissions'), t('Global Permissions'))
		);
		$this->set('subnav', $subnav);
		
		Loader::model('attribute_key_category_item_permission');
		$akcip = AttributeKeyCategoryItemPermission::get('GLOBAL');
		$this->set('permission', $akcip->canAdmin());
		
		if($akcip->canAdmin()) {
			if($post = $this->post()) {
				$txt = Loader::helper('text');
				if(empty($post['akCategoryName'])) {
					$this->redirect('dashboard/bricks/add/error/malformed');
				} else {
					try{
						if(!$post['package_handle']) $post['package_handle'] = 0;
						$akc = AttributeKeyCategory::add($txt->uncamelcase($txt->camelcase($post['akCategoryName'])), $post['enableSets'], $post['package_handle']);
						if($post['associateAttributeTypes'] == '1') {
							$atypes = AttributeType::getList();
							foreach($atypes as $type) {
								$akc->associateAttributeKeyType($type);
							}
						}
						$this->redirect('dashboard/bricks');
					} catch(Exception $e) {
						$this->redirect('dashboard/bricks/add/error/exists');
					}
				}
			} else {
				$this->set('packageHandle', $packageHandle);
				$form = Loader::helper('form');
				$this->set('form', $form);
				$ih = Loader::helper('concrete/interface');
				$this->set('ih', $ih);
			}
		}
	}
	
	public function error($msg) {
		$form = Loader::helper('form');
		$this->set('form', $form);
		$ih = Loader::helper('concrete/interface');
		$this->set('ih', $ih);
		switch($msg) {
			case 'exists':
				$this->set('error', array('Attribute Key Category name already in use.'));
				break;
			case 'malformed':
				$this->set('error', array('Invalid Attribute Key Category name.'));
				break;
		}
	}
		
} ?>
