<?php defined('C5_EXECUTE') or die('Access Denied');

class DashboardSystemEnvironmentAssetsController extends DashboardBaseController {

	public function view() {
	
		$this->set('jsenabled', unserialize(Config::get('LOADED_JS_ASSETS')));
		$this->set('cssenabled', unserialize(Config::get('LOADED_CSS_ASSETS')));
		
		$fh = Loader::helper('file');
		$jignore = array('build.sh', 'ccm_app', 'old', 'i18n', 'tiny_mce', 'swfupload');
		$top = $fh->getDirectoryContents(DIR_BASE . '/' . DIRNAME_JAVASCRIPT, $jignore, true);
		$core = $fh->getDirectoryContents(DIR_BASE_CORE . '/' . DIRNAME_JAVASCRIPT, $jignore, true);
		$js = array_unique(array_merge($top, $core));
		$js = $this->filter($js);
		$this->set('js', $js);
		
		$cignore = array('legacy', 'images', 'ccm_app');
		$top = $fh->getDirectoryContents(DIR_BASE . '/' . DIRNAME_CSS, $cignore, true);
		$core = $fh->getDirectoryContents(DIR_BASE_CORE . '/' . DIRNAME_CSS, $cignore, true);
		$css = array_unique(array_merge($top, $core));
		$css = $this->filter($css);
		$this->set('css', $css);
	}
	
	public function save_assets() {
		if ($this->token->validate("loaded_assets")) {
			if(is_array($this->post('js'))) {
				Config::save('LOADED_JS_ASSETS', serialize($this->post('js')));
			} else {
				Config::save('LOADED_JS_ASSETS', serialize(array()));
			}
			if(is_array($this->post('css'))) {
				Config::save('LOADED_CSS_ASSETS', serialize($this->post('css')));
			} else {
				Config::save('LOADED_CSS_ASSETS', serialize(array()));
			}
			$this->set('message', t('Loaded Assets Updated'));
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
		$this->view();
	}
	
	private function filter($arr = array()) {
		$new = array();
		foreach($arr as $item) {
			if(substr($item, -3) == '.js' || substr($item, -4) == '.css') {
				$item = str_replace(DIR_BASE.'/'.DIRNAME_JAVASCRIPT.'/', '', $item);
				$item = str_replace(DIR_BASE.'/'.DIRNAME_CSS.'/', '', $item);
				$item = str_replace(DIR_BASE_CORE.'/'.DIRNAME_JAVASCRIPT.'/', '', $item);
				$item = str_replace(DIR_BASE_CORE.'/'.DIRNAME_CSS.'/', '', $item);
				$new[] = $item;
			}
		}
		return $new;
	}
}