<?php
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * A generic object that can render the email template.
 * Unlike normal view, email templates:
 * - Should not have headers sent
 * - Should not print out actual content
 * - Do not have a page object binded to them
 * 
 * @package Core
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 * @category Concrete
 * @copyright Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license http://www.concrete5.org/license/     MIT License
 *
 */
class MailTemplate extends Object {
	
	private $subject = false;
	private $from = false;
	private $body = false;
	private $bodyHTML = false;
	
	private $themePaths = array();
	
	public function getSubject() {return $this->subject;}
	public function getFrom() {return $this->from;}
	public function getBody() {return $this->body;}
	public function getBodyHTML() {return $this->bodyHTML;}
	
	private function setThemeForView($pl, $filename) {
		$this->ptHandle = $pl;
		if (file_exists(DIR_FILES_THEMES . '/' . $pl . '/' . $filename)) {
			$themePath = DIR_REL . '/' . DIRNAME_THEMES . '/' . $pl;
			$themeDir = DIR_FILES_THEMES . "/" . $pl;
			$themeFile = $filename;
		} else if (file_exists(DIR_FILES_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . $pl . '.php')) {
			$themeDir = DIR_FILES_THEMES . '/' . DIRNAME_THEMES_CORE;
			$themeFile = $pl . '.php';
		} else if (file_exists(DIR_FILES_THEMES_CORE . "/" . $pl . '/' . $filename)) {
			$themePath = ASSETS_URL . '/' . DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . $pl;
			$themeDir = DIR_FILES_THEMES_CORE . "/" . $pl;
			$themeFile = $filename;
		} else if (file_exists(DIR_FILES_THEMES_CORE_ADMIN . "/" . $pl . '.php')) {
			$themeDir = DIR_FILES_THEMES_CORE_ADMIN;
			$themeFile = $pl . '.php';
		}
		
		$this->themePath = $themePath;
		$this->themeDir = $themeDir;
		if (isset($themeFile)) {
			$this->theme = $themeDir . '/' . $themeFile;
		}
	}
	
	protected function getMailTemplateFile($template, $_filename, $pkgHandle = null) {
		if (file_exists(DIR_FILES_EMAIL_TEMPLATES . '/' . $template . '/' . $_filename)) {
			return DIR_FILES_EMAIL_TEMPLATES . '/' . $template . '/' . $_filename;
		} else if (file_exists(DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . '/' . $template . '/' . $_filename)) {
			return DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . '/' . $template . '/' . $_filename;
		} else if (file_exists(DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . '/' . $template . '/' . $_filename)) {
			return DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . '/' . $template . '/' . $_filename;
		} else if (file_exists(DIR_FILES_EMAIL_TEMPLATES_CORE . '/' . $template . '/' . $_filename)) {
			return DIR_FILES_EMAIL_TEMPLATES_CORE . '/' . $template . '/' . $_filename;
		}
		return false;
	}
	
	public function setTemplate($tpl) {
		$this->template = $tpl;
	}
	
	public function load($template, $args = null, $pkgHandle = null) {
		extract($args);
		
		// loads template from mail templates directory
		// the main template file contains the basic data for the template
		// first checks template/data.php and then template.php
		// This needs to be here to preserve backwards compatibility!
		$dataFile = FILENAME_MAIL_DATA;
		if ($file = $this->getMailTemplateFile($template, $dataFile, $pkgHandle)) {
			include($file);
		} else if (file_exists(DIR_FILES_EMAIL_TEMPLATES . "/{$template}.php")) {
			include(DIR_FILES_EMAIL_TEMPLATES . "/{$template}.php");
		} else if ($pkgHandle != null && file_exists(DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . "/{$template}.php")) {
			include(DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . "/{$template}.php");
		} else if ($pkgHandle != null && file_exists(DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . "/{$template}.php")) {
			include(DIR_PACKAGES_CORE . '/' . $pkgHandle . '/' . DIRNAME_MAIL_TEMPLATES . "/{$template}.php");
		} else {
			include(DIR_FILES_EMAIL_TEMPLATES_CORE . "/{$template}.php");
		}
		
		$this->subject = $subject;
		if (isset($from)) {
			$this->from = array('email' => $from[0], 'name' => $from[1]);
		}
		
		// loads the plain mail template if $body is not defined
		$view = FILENAME_MAIL_TPL_PLAIN;
		if (!isset($body)) {
			// Check whether plain template exists
			ob_start();
			if ($file = $this->getMailTemplateFile($template, $view, $pkgHandle)) {
				include($file);
			}
			$body = ob_get_contents();
			ob_end_clean();
		}
		$this->body = $body;
		
		// loads the HTML mail template if $bodyHTML is not defined
		$view = FILENAME_MAIL_TPL_HTML;
		if (!isset($bodyHTML)) {
			// Check whether HTML template exists
			ob_start();
			if ($file = $this->getMailTemplateFile($template, $view, $pkgHandle)) {
				include($file);
			}
			$bodyHTML = ob_get_contents();
			ob_end_clean();
		}
		
		// Extract controller information from the view, and put it in the current context
		if (!isset($this->controller)) {
			$this->controller = Loader::controller($view);
			$this->controller->setupAndRun();
		}
		if ($this->controller->getRenderOverride() != '') {
		   $view = $this->controller->getRenderOverride();
		}
		
		// Determine which outer item/theme to load
		// obtain theme information for this collection
		$themeFilename = FILENAME_MAIL_THEME_FILE;
		$theme = PageTheme::getSiteTheme();
		if (isset($this->themeOverride)) {
			$theme = $this->themeOverride;
		} else if ($this->controller->theme != false) {
			$theme = $this->controller->theme;
		} else if (is_object($theme)) {
			if (file_exists($theme->getThemeDirectory() . '/' . $themeFilename)) {
				$theme = $theme->getThemeHandle();
			} else {
				$theme = false;
			}
		}
		if (!$theme) {
			$theme = FILENAME_COLLECTION_DEFAULT_THEME;
		}
		$this->setThemeForView($theme, $themeFilename);
		
		$this->controller->on_before_render();
		
		if (strlen($bodyHTML)) {
			if (file_exists($this->theme)) {
				ob_start();
				include($this->theme);
				$bodyHTML = ob_get_contents();
				ob_end_clean();
			}
			$this->bodyHTML = $bodyHTML;
		}
	}
	
}
