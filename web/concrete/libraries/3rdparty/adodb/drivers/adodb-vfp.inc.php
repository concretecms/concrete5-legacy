<?php
/*
V5.10 10 Nov 2009   (c) 2000-2009 John Lim (jlim#natsoft.com). All rights reserved.
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
Set tabs to 4 for best viewing.

  Latest version is available at http://adodb.sourceforge.net

  Microsoft Visual FoxPro data driver. Requires ODBC. Works only on MS Windows.
*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!defined('_ADODB_ODBC_LAYER')) {
    include(ADODB_DIR."/drivers/adodb-odbc.inc.php");
}
if (!defined('ADODB_VFP')) {
define('ADODB_VFP',1);
class ADODB_vfp extends ADODB_odbc
{
    public $databaseType = "vfp";
    public $fmtDate = "{^Y-m-d}";
    public $fmtTimeStamp = "{^Y-m-d, h:i:sA}";
    public $replaceQuote = "'+chr(39)+'" ;
    public $true = '.T.';
    public $false = '.F.';
    public $hasTop = 'top';		// support mssql SELECT TOP 10 * FROM TABLE
    public $_bindInputArray = false; // strangely enough, setting to true does not work reliably
    public $sysTimeStamp = 'datetime()';
    public $sysDate = 'date()';
    public $ansiOuter = true;
    public $hasTransactions = false;
    public $curmode = false ; // See sqlext.h, SQL_CUR_DEFAULT == SQL_CUR_USE_DRIVER == 2L

    public function ADODB_vfp()
    {
        $this->ADODB_odbc();
    }

    public function Time()
    {
        return time();
    }

    public function BeginTrans() { return false;}

    // quote string to be sent back to database
    public function qstr($s,$nofixquotes=false)
    {
        if (!$nofixquotes) return  "'".str_replace("\r\n","'+chr(13)+'",str_replace("'",$this->replaceQuote,$s))."'";
        return "'".$s."'";
    }

    // TOP requires ORDER BY for VFP
    public function SelectLimit($sql,$nrows=-1,$offset=-1, $inputarr=false,$secs2cache=0)
    {
        $this->hasTop = preg_match('/ORDER[ \t\r\n]+BY/is',$sql) ? 'top' : false;
        $ret = ADOConnection::SelectLimit($sql,$nrows,$offset,$inputarr,$secs2cache);

        return $ret;
    }

};

class  ADORecordSet_vfp extends ADORecordSet_odbc
{
    public $databaseType = "vfp";

    public function ADORecordSet_vfp($id,$mode=false)
    {
        return $this->ADORecordSet_odbc($id,$mode);
    }

    public function MetaType($t,$len=-1)
    {
        if (is_object($t)) {
            $fieldobj = $t;
            $t = $fieldobj->type;
            $len = $fieldobj->max_length;
        }
        switch (strtoupper($t)) {
        case 'C':
            if ($len <= $this->blobSize) return 'C';
        case 'M':
            return 'X';

        case 'D': return 'D';

        case 'T': return 'T';

        case 'L': return 'L';

        case 'I': return 'I';

        default: return 'N';
        }
    }
}

} //define
