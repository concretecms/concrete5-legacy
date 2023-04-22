<?php

/**
  @version   v5.21.0-dev  ??-???-2016
  @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
  @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.

  Set tabs to 4 for best viewing.

*/

/*
In ADOdb, named quotes for MS SQL Server use ". From the MSSQL Docs:

	Note Delimiters are for identifiers only. Delimiters cannot be used for keywords,
	whether or not they are marked as reserved in SQL Server.

	Quoted identifiers are delimited by double quotation marks ("):
	SELECT * FROM "Blanks in Table Name"

	Bracketed identifiers are delimited by brackets ([ ]):
	SELECT * FROM [Blanks In Table Name]

	Quoted identifiers are valid only when the QUOTED_IDENTIFIER option is set to ON. By default,
	the Microsoft OLE DB Provider for SQL Server and SQL Server ODBC driver set QUOTED_IDENTIFIER ON
	when they connect.

	In Transact-SQL, the option can be set at various levels using SET QUOTED_IDENTIFIER,
	the quoted identifier option of sp_dboption, or the user options option of sp_configure.

	When SET ANSI_DEFAULTS is ON, SET QUOTED_IDENTIFIER is enabled.

	Syntax

		SET QUOTED_IDENTIFIER { ON | OFF }


*/

// security - hide paths
if (!defined('ADODB_DIR')) die();

class ADODB2_mssqlnative extends ADODB_DataDict {
	var $databaseType = 'mssqlnative';
	var $dropIndex = 'DROP INDEX %1$s ON %2$s';
	var $renameTable = "EXEC sp_rename '%s','%s'";
	var $renameColumn = "EXEC sp_rename '%s.%s','%s'";
	var $typeX = 'TEXT';  ## Alternatively, set it to VARCHAR(4000)
	var $typeXL = 'TEXT';

	//var $alterCol = ' ALTER COLUMN ';

	function MetaType($t,$len=-1,$fieldobj=false)
	{
		if (is_object($t)) {
			$fieldobj = $t;
			$t = $fieldobj->type;
			$len = $fieldobj->max_length;
		}

		$_typeConversion = array(
			-155 => 'D',
			  93 => 'D',
			-154 => 'D',
			  -2 => 'D',
			  91 => 'D',

			  12 => 'C',
			   1 => 'C',
			  -9 => 'C',
			  -8 => 'C',

			  -7 => 'L',
			  -6 => 'I2',
			  -5 => 'I8',
			 -11 => 'I',
			   4 => 'I',
			   5 => 'I4',

			  -1 => 'X',
			 -10 => 'X',

			   2 => 'N',
			   3 => 'N',
			   6 => 'N',
			   7 => 'N',

			-152 => 'X',
			-151 => 'X',
			  -4 => 'X',
			  -3 => 'X'
			);

		if (isset($_typeConversion[$t]))
		return $_typeConversion[$t];
		
		return ADODB_DEFAULT_METATYPE;

	}

	function ActualType($meta)
	{
		$DATE_TYPE = 'DATETIME';

		switch(strtoupper($meta)) {

		case 'C': return 'VARCHAR';
		case 'XL': return (isset($this)) ? $this->typeXL : 'TEXT';
		case 'X': return (isset($this)) ? $this->typeX : 'TEXT'; ## could be varchar(8000), but we want compat with oracle
		case 'C2': return 'NVARCHAR';
		case 'X2': return 'NTEXT';

		case 'B': return 'IMAGE';

		case 'D': return $DATE_TYPE;
		case 'T': return 'TIME';
		case 'L': return 'BIT';

		case 'R':
		case 'I': return 'INT';
		case 'I1': return 'TINYINT';
		case 'I2': return 'SMALLINT';
		case 'I4': return 'INT';
		case 'I8': return 'BIGINT';

		case 'F': return 'REAL';
		case 'N': return 'NUMERIC';
		default:
			return $meta;
		}
	}


	function AddColumnSQL($tabname, $flds)
	{
		$tabname = $this->TableName ($tabname);
		$f = array();
		list($lines,$pkey) = $this->_GenFields($flds);
		$s = "ALTER TABLE $tabname $this->addCol";
		foreach($lines as $v) {
			$f[] = "\n $v";
		}
		$s .= implode(', ',$f);
		$sql[] = $s;
		return $sql;
	}

	function DefaultConstraintname($tabname, $colname)
	{
		$constraintname = false;
		$rs = $this->connection->Execute(
			"SELECT name FROM sys.default_constraints
			WHERE object_name(parent_object_id) = '$tabname'
			AND col_name(parent_object_id, parent_column_id) = '$colname'"
		);
		if ( is_object($rs) ) {
			$row = $rs->FetchRow();
			$constraintname = $row['name'];
		}
		return $constraintname;
	}
  
	function AlterColumnSQL($tabname, $flds, $tableflds='',$tableoptions='')
	{
		$tabname = $this->TableName ($tabname);
		$sql = array();

		list($lines,$pkey,$idxs) = $this->_GenFields($flds);
		$alter = 'ALTER TABLE ' . $tabname . $this->alterCol . ' ';
		foreach($lines as $v) {
			$not_null = false;
			if ($not_null = preg_match('/NOT NULL/i',$v)) {
				$v = preg_replace('/NOT NULL/i','',$v);
			}
			if (preg_match('/^([^ ]+) .*DEFAULT (\'[^\']+\'|\"[^\"]+\"|[^ ]+)/',$v,$matches)) {
				list(,$colname,$default) = $matches;
				$v = preg_replace('/^' . preg_quote($colname) . '\s/', '', $v);
				$t = trim(str_replace('DEFAULT '.$default,'',$v));
				if ( $constraintname = $this->DefaultConstraintname($tabname,$colname) ) {
					$sql[] = 'ALTER TABLE '.$tabname.' DROP CONSTRAINT '. $constraintname;
				}
				if ($not_null) {
					$sql[] = $alter . $colname . ' ' . $t  . ' NOT NULL';
				} else {
					$sql[] = $alter . $colname . ' ' . $t ;
				}
				$sql[] = 'ALTER TABLE ' . $tabname
					. ' ADD CONSTRAINT DF__' . $tabname . '__' .  $colname .  '__' . dechex(rand())
					. ' DEFAULT ' . $default . ' FOR ' . $colname;
			} else {
				$colname = strtok($v," ");
				if ( $constraintname = $this->DefaultConstraintname($tabname,$colname) ) {
					$sql[] = 'ALTER TABLE '.$tabname.' DROP CONSTRAINT '. $constraintname;
				}
				if ($not_null) {
					$sql[] = $alter . $v  . ' NOT NULL';
				} else {
					$sql[] = $alter . $v;
				}
			}
		}
		if (is_array($idxs)) {
			foreach($idxs as $idx => $idxdef) {
				$sql_idxs = $this->CreateIndexSql($idx, $tabname, $idxdef['cols'], $idxdef['opts']);
				$sql = array_merge($sql, $sql_idxs);
			}
		}
		return $sql;
	}


	/**
	 * Drop a column, syntax is ALTER TABLE table DROP COLUMN column,column
	 *
	 * @param string   $tabname      Table Name
	 * @param string[] $flds         One, or an array of Fields To Drop
	 * @param string   $tableflds    Throwaway value to make the function match the parent
	 * @param string   $tableoptions Throway value to make the function match the parent
	 *
	 * @return string  The SQL necessary to drop the column
	 */
	function DropColumnSQL($tabname, $flds, $tableflds='',$tableoptions='')
	{
		$tabname = $this->TableName ($tabname);
		if (!is_array($flds))
			$flds = explode(',',$flds);
		$f = array();
		$s = 'ALTER TABLE ' . $tabname;
		foreach($flds as $v) {
			if ( $constraintname = $this->DefaultConstraintname($tabname,$v) ) {
				$sql[] = 'ALTER TABLE ' . $tabname . ' DROP CONSTRAINT ' . $constraintname;
			}
			$f[] = ' DROP COLUMN ' . $this->NameQuote($v);
		}
		$s .= implode(', ',$f);
		$sql[] = $s;
		return $sql;
	}

	// return string must begin with space
	function _CreateSuffix($fname,&$ftype,$fnotnull,$fdefault,$fautoinc,$fconstraint,$funsigned)
	{
		$suffix = '';
		if (strlen($fdefault)) $suffix .= " DEFAULT $fdefault";
		if ($fautoinc) $suffix .= ' IDENTITY(1,1)';
		if ($fnotnull) $suffix .= ' NOT NULL';
		else if ($suffix == '') $suffix .= ' NULL';
		if ($fconstraint) $suffix .= ' '.$fconstraint;
		return $suffix;
	}

	/*
CREATE TABLE
    [ database_name.[ owner ] . | owner. ] table_name
    ( { < column_definition >
        | column_name AS computed_column_expression
        | < table_constraint > ::= [ CONSTRAINT constraint_name ] }

            | [ { PRIMARY KEY | UNIQUE } [ ,...n ]
    )

[ ON { filegroup | DEFAULT } ]
[ TEXTIMAGE_ON { filegroup | DEFAULT } ]

< column_definition > ::= { column_name data_type }
    [ COLLATE < collation_name > ]
    [ [ DEFAULT constant_expression ]
        | [ IDENTITY [ ( seed , increment ) [ NOT FOR REPLICATION ] ] ]
    ]
    [ ROWGUIDCOL]
    [ < column_constraint > ] [ ...n ]

< column_constraint > ::= [ CONSTRAINT constraint_name ]
    { [ NULL | NOT NULL ]
        | [ { PRIMARY KEY | UNIQUE }
            [ CLUSTERED | NONCLUSTERED ]
            [ WITH FILLFACTOR = fillfactor ]
            [ON {filegroup | DEFAULT} ] ]
        ]
        | [ [ FOREIGN KEY ]
            REFERENCES ref_table [ ( ref_column ) ]
            [ ON DELETE { CASCADE | NO ACTION } ]
            [ ON UPDATE { CASCADE | NO ACTION } ]
            [ NOT FOR REPLICATION ]
        ]
        | CHECK [ NOT FOR REPLICATION ]
        ( logical_expression )
    }

< table_constraint > ::= [ CONSTRAINT constraint_name ]
    { [ { PRIMARY KEY | UNIQUE }
        [ CLUSTERED | NONCLUSTERED ]
        { ( column [ ASC | DESC ] [ ,...n ] ) }
        [ WITH FILLFACTOR = fillfactor ]
        [ ON { filegroup | DEFAULT } ]
    ]
    | FOREIGN KEY
        [ ( column [ ,...n ] ) ]
        REFERENCES ref_table [ ( ref_column [ ,...n ] ) ]
        [ ON DELETE { CASCADE | NO ACTION } ]
        [ ON UPDATE { CASCADE | NO ACTION } ]
        [ NOT FOR REPLICATION ]
    | CHECK [ NOT FOR REPLICATION ]
        ( search_conditions )
    }


	*/

	/*
	CREATE [ UNIQUE ] [ CLUSTERED | NONCLUSTERED ] INDEX index_name
    ON { table | view } ( column [ ASC | DESC ] [ ,...n ] )
		[ WITH < index_option > [ ,...n] ]
		[ ON filegroup ]
		< index_option > :: =
		    { PAD_INDEX |
		        FILLFACTOR = fillfactor |
		        IGNORE_DUP_KEY |
		        DROP_EXISTING |
		    STATISTICS_NORECOMPUTE |
		    SORT_IN_TEMPDB
		}
*/
	function _IndexSQL($idxname, $tabname, $flds, $idxoptions)
	{
		$sql = array();

		if ( isset($idxoptions['REPLACE']) || isset($idxoptions['DROP']) ) {
			$sql[] = sprintf ($this->dropIndex, $idxname, $tabname);
			if ( isset($idxoptions['DROP']) )
				return $sql;
		}

		if ( empty ($flds) ) {
			return $sql;
		}

		$unique = isset($idxoptions['UNIQUE']) ? ' UNIQUE' : '';
		$clustered = isset($idxoptions['CLUSTERED']) ? ' CLUSTERED' : '';

		if ( is_array($flds) )
			$flds = implode(', ',$flds);
		$s = 'CREATE' . $unique . $clustered . ' INDEX ' . $idxname . ' ON ' . $tabname . ' (' . $flds . ')';

		if ( isset($idxoptions[$this->upperName]) )
			$s .= $idxoptions[$this->upperName];


		$sql[] = $s;

		return $sql;
	}


	function _GetSize($ftype, $ty, $fsize, $fprec,$options=false)
	{
		switch ($ftype) {
		case 'INT':
		case 'SMALLINT':
		case 'TINYINT':
		case 'BIGINT':
			return $ftype;
		}
    	if ($ty == 'T') return $ftype;
    	return parent::_GetSize($ftype, $ty, $fsize, $fprec, $options);

	}
}
