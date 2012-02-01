<?php
/**
 * View object now has a singleton method.
 *
 * @package Core
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @author Chris van Dam <chris@trace.nl>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class View extends BaseView implements SingletonInterface {
  /**
     * @static
     * @return View
     */
    public static function getInstance() {
        static $instance;
        if (!isset($instance)) {
            $v = __CLASS__;
            $instance = new $v;
        }
        return $instance;
    }
}