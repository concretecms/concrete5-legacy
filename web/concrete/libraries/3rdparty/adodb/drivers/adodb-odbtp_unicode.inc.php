<?php
/*
	@version   v5.21.0-dev  ??-???-2016
	@copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
	@copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence. See License.txt.
  Set tabs to 4 for best viewing.
  Latest version is available at http://adodb.sourceforge.net
*/

// Code contributed by "Robert Twitty" <rtwitty#neutron.ushmm.org>

// security - hide paths
if (!defined('ADODB_DIR')) die();

/*
    Because the ODBTP server sends and reads UNICODE text data using UTF-8
    encoding, the following HTML meta tag must be included within the HTML
    head section of every HTML form and script page:

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    Also, all SQL query strings must be submitted as UTF-8 encoded text.
*/

if (!defined('_ADODB_ODBTP_LAYER')) {
	include_once(ADODB_DIR."/drivers/adodb-odbtp.inc.php");
}

class ADODB_odbtp_unicode extends ADODB_odbtp {
	var $databaseType = 'odbtp';
	var $_useUnicodeSQL = true;
}
