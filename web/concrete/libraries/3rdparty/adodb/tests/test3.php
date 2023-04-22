<?php
/*
  @version   v5.21.0-dev  ??-???-2016
  @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
  @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
  Set tabs to 8.
 */


error_reporting(E_ALL);

$path = dirname(__FILE__);

include("$path/../adodb-exceptions.inc.php");
include("$path/../adodb.inc.php");

try {
$db = NewADOConnection("oci8");
$db->Connect('','scott','natsoft');
$db->debug=1;

$cnt = $db->GetOne("select count(*) from adoxyz");
$rs = $db->Execute("select * from adoxyz order by id");

$i = 0;
foreach($rs as $k => $v) {
	$i += 1;
	echo $k; adodb_pr($v);
	flush();
}

if ($i != $cnt) die("actual cnt is $i, cnt should be $cnt\n");



$rs = $db->Execute("select bad from badder");

} catch (exception $e) {
	adodb_pr($e);
	$e = adodb_backtrace($e->trace);
}
