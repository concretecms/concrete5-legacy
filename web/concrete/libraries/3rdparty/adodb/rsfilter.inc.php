<?php
/**
 * @version   v5.21.0-dev  ??-???-2016
 * @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
 * @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
 * Released under both BSD license and Lesser GPL library license.
 * Whenever there is any discrepancy between the two licenses,
 * the BSD license will take precedence.
 *
 * Set tabs to 4 for best viewing.
 *
 * Latest version is available at http://php.weblogs.com
 *
 * Requires PHP4.01pl2 or later because it uses include_once
*/

/*
	Filter all fields and all rows in a recordset and returns the
	processed recordset. We scroll to the beginning of the new recordset
	after processing.

	We pass a recordset and function name to RSFilter($rs,'rowfunc');
	and the function will be called multiple times, once
	for each row in the recordset. The function will be passed
	an array containing one row repeatedly.

	Example:

	// ucwords() every element in the recordset
	function do_ucwords(&$arr,$rs)
	{
		foreach($arr as $k => $v) {
			$arr[$k] = ucwords($v);
		}
	}
	$rs = RSFilter($rs,'do_ucwords');
 */
function RSFilter($rs,$fn)
{
	if ($rs->databaseType != 'array') {
		if (!$rs->connection) return false;

		$rs = $rs->connection->_rs2rs($rs);
	}
	$rows = $rs->RecordCount();
	for ($i=0; $i < $rows; $i++) {
		if (is_array ($fn)) {
        	$obj = $fn[0];
        	$method = $fn[1];
        	$obj->$method ($rs->_array[$i],$rs);
      } else {
			$fn($rs->_array[$i],$rs);
      }

	}
	if (!$rs->EOF) {
		$rs->_currentRow = 0;
		$rs->fields = $rs->_array[0];
	}

	return $rs;
}
