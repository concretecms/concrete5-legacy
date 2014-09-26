<?

defined('C5_EXECUTE') or die("Access Denied.");

class FilePageCache extends Concrete5_Library_FilePageCache {

	protected function getCacheFile($mixed) {
		$key = $this->getCacheKey($mixed);
		if ($key) {
			$key = hash('sha256',$key);
			$filename = $key . '.cache';
			$dir = DIR_FILES_PAGE_CACHE . '/' . $key[0] . '/' . $key[1] . '/' . $key[2];
			if ($dir && (!is_dir($dir))) {
				@mkdir($dir, DIRECTORY_PERMISSIONS_MODE, true);
			}
			$path = $dir . '/' . $filename;
			return $path;
		}
	}
	
}