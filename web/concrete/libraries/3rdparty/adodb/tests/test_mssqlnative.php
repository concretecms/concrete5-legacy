<?php


include('../adodb.inc.php');

/* SQL Script to clean objects created
drop sequence MySequence1;
drop sequence MySequence2;
drop sequence MySequence3;
drop table MySequence1Emul;
drop table MySequence2Emul;
drop table MySequence3Emul;

 * */


$db = ADONewConnection("mssqlnative");  // create a connection
$db->debug=true;
$db->Connect('127.0.0.1','adodb','natsoft','northwind') or die('Fail');

//==========================
// This code tests GenId
//==========================
$ID1a=$db->GenID("MySequence1");
$ID2a=$db->GenID("MySequence2");
$ID1b=$db->GenID("MySequence1");
$ID2b=$db->GenID("MySequence2");
echo "ID1a=$ID1a,ID1b=$ID1b, ID2a=$ID2a,ID2b=$ID2b <br>\n";
if(intval($ID1a)+1!==intval($ID1b)) die(sprintf("ERROR : Second value obtains by MySequence1 should be %d but is %d",$ID1a+1,$ID1b));

$db->CreateSequence("MySequence3",100);
$ID2b=$db->GenID("MySequence3");
if(intval($ID2b)!==100) die(sprintf("ERROR : Value from MySequence3 should be 100 but is %d",$ID2b));

$db->mssql_version=10; // Force to simulate Pre 2012 (without sequence) behavior
$ID1a=$db->GenID("MySequence1Emul");
$ID2a=$db->GenID("MySequence2Emul");
$ID1b=$db->GenID("MySequence1Emul");
$ID2b=$db->GenID("MySequence2Emul");
echo "ID1a=$ID1a,ID1b=$ID1b, ID2a=$ID2a,ID2b=$ID2b <br>\n";
if(intval($ID1a+1)!==intval($ID1b)) die(sprintf("ERROR : Second value obtains by MySequence1Emul should be %d but is %d",$ID1a+1,$ID1b));

$db->CreateSequence("MySequence3Emul",100);
$ID2b=$db->GenID("MySequence3Emul");
if(intval($ID2b)!==100) die(sprintf("ERROR : Value from MySequence3Emul should be 100 but is %d",$ID2b));

echo "End of tests.";