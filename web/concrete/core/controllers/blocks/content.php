<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * The controller for the content block.
 *
 * @package Blocks
 * @subpackage Content
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Controller_Block_Content extends BlockController {

	protected $btTable = 'btContentLocal';
	protected $btInterfaceWidth = "600";
	protected $btInterfaceHeight = "465";
	protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = true;
	protected $btCacheBlockOutputForRegisteredUsers = true;
	protected $btCacheBlockOutputLifetime = 0; //until manually updated or cleared

	public function getBlockTypeDescription() {
		return t("HTML/WYSIWYG Editor Content.");
	}

	public function getBlockTypeName() {
		return t("Content");
	}

	function getContent() {
		$content = Loader::helper('content')->translateFrom($this->content);
		return $content;
	}

	public function getSearchableContent(){
		return $this->content;
	}

	function getContentEditMode() {
		$content = Loader::helper('content')->translateFromEditMode($this->content);
		return $content;
	}

	public function getImportData($blockNode) {
		$args = array();
		$args['content'] = Loader::helper('content')->import($blockNode->data->record->content);
		return $args;
	}

	public function export(SimpleXMLElement $blockNode) {
		$data = $blockNode->addChild('data');
		$data->addAttribute('table', $this->btTable);
		$record = $data->addChild('record');
		$cnode = $record->addChild('content');
		$node = dom_import_simplexml($cnode);
		$no = $node->ownerDocument;
		$node->appendChild($no->createCDataSection(Loader::helper('content')->export($this->content)));
	}

	function save($args) {
		$args['content'] = Loader::helper('content')->translateTo($args['content']);
		parent::save($args);
	}
}
