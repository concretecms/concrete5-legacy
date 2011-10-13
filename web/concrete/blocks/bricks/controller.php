<?
defined('C5_EXECUTE') or die("Access Denied.");

class BricksBlockController extends BlockController {

	protected $btTable = 'btBricks';
	protected $btInterfaceWidth = "600";
	protected $btInterfaceHeight = "400";
	
	public $akCategoryHandle = "";	
	
	public function getBlockTypeDescription() {
		return t("Display a list of items assocaited to an Attribute Key Category.");
	}
	
	public function getBlockTypeName() {
		return t("Bricks");
	}
	
	public function on_page_view() {
		$this->addHeaderItem(Loader::helper('html')->css('ccm.dialog.css'));
		$this->addHeaderItem(Loader::helper('html')->css('ccm.search.css'));
		$this->addHeaderItem(Loader::helper('html')->css('ccm.forms.css'));
		$this->addHeaderItem(Loader::helper('html')->css('jquery.rating.css'));
		$this->addHeaderItem('<script type="text/javascript" src="' . REL_DIR_FILES_TOOLS_REQUIRED . '/i18n_js"></script>');
		$this->addHeaderItem(Loader::helper('html')->javascript('jquery.ui.js'));
		$this->addHeaderItem(Loader::helper('html')->javascript('jquery.form.js'));
		$this->addHeaderItem(Loader::helper('html')->javascript('jquery.rating.js'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.dialog.js'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.search.js'));
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.attributekeycategory.js'));
	}
	
	function save($data) {
		$args['akCategoryHandle'] = isset($data['akCategoryHandle']) ? $data['akCategoryHandle'] : '';
		$args['isSearchable'] = isset($data['isSearchable']) ? $data['isSearchable'] : 0;
		$args['numResults'] = isset($data['numResults']) ? $data['numResults'] : 10;
		
		Loader::model('attribute_key_category_item_list');
		$akcdc = new AttributeKeyCategoryColumnSet($data['akCategoryHandle']);
		$akcdca = new AttributeKeyCategoryAvailableColumnSet($data['akCategoryHandle']);
		if(is_array($data['column'])) {
			foreach($data['column'] as $key) {
				$akcdc->addColumn($akcdca->getColumnByKey($key));
			}	
			$sortCol = $akcdca->getColumnByKey($data['fSearchDefaultSort']);
			$akcdc->setDefaultSortColumn($sortCol, $data['fSearchDefaultSortDirection']);
			$columns = serialize($akcdc);
		}
		$args['defaults']['columns'] = $columns;
		$args['defaults'] = urlencode(serialize($args['defaults']));
		
		parent::save($args);
	}
}

?>