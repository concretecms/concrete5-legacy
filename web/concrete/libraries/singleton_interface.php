<?php
/**
 * This interface creates a contract with a class for a signleton implementation.
 * limitations with php < 5.3 cause us to not be able to use late static binding, so redifinition of the getInstance static method is necessary.
 *
 * @package Core
 * @category Concrete
 * @author Chris van Dam <chris@trace.nl>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
interface SingletonInterface {
    public static function getInstance();
}