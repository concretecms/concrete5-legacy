<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 

$json = Loader::helper('json');
$error = Loader::helper('validation/error');

$akcHandle = $_REQUEST['akCategoryHandle'];
$akciIDs = $_REQUEST['akciID'];
$columns = $_REQUEST['columns'];
$perms = $_REQUEST['perms'];

if(!is_array($columns)){
	$columns = array();
}

Loader::model('attribute_key_category_item_permission');
//$akcip = AttributeKeyCategoryItemPermission::get($akcHandle);

Loader::model('attribute_key_category_item_list');


$akc = AttributeKeyCategory::getByHandle($akcHandle);

//$akList = AttributeKey::getList($akcHandle);

$akcsh = Loader::helper('attribute_key_category_settings');
$akcs = $akcsh->getRegisteredSettings($akcHandle);

$akccs = new AttributeKeyCategoryAvailableColumnSet($akcHandle);

$output = array();//$output['cols']= $akccs->getColumns();
$output['akCategoryHandle'] = $akcHandle;
foreach($akciIDs as $akciID){
	$akci = $akc->getItemObject($akciID);
	if(is_object($akci)){
		$output[$akciID] = array('id'=>$akciID);
		$akcip = AttributeKeyCategoryItemPermission::get($akciID);
		if($akcip->canRead()){
			foreach($akccs->getColumns() as $col){
				if(empty($columns) || in_array($col->getColumnKey(), $columns)){					
					$output[$akciID][$col->getColumnKey()] = $col->getColumnValue($akci);					
				}
			}
		}else if(!$perms){
			$output[$akciID]['error'] = '401';//Unauthorized
		}
		//Add the permissions info
		foreach($akcip->getPermissionTypes() as $permType){
			if(!is_array($perms) || in_array($permType, $perms))
				$output[$akciID]['_can'][$permType] = $akcip->can($permType);
		}
		
	}else{
		$output[$akciID]['error'] = '404';//Not found
	}
}

echo $json->encode($output);


