<?php
defined('C5_EXECUTE') or die("Access Denied.");

$txtToCheck = $_REQUEST['txt'];
$fieldId = $_REQUEST['fieldId'];

$spellChecker = Loader::helper('spellchecker');
$correctedHTML = addslashes($spellChecker->findMisspellings($txtToCheck));
//var_dump( $spellChecker->wordSuggestions );
$suggestionPairs = $spellChecker->wordSuggestions;
//$suggestionPairs = $spellChecker->getSuggestionPairsJSON();//not using this because it makes crappy json...

header('content-type: application/json');
$js = Loader::helper('json');
$obj = new stdClass;
$obj->html = '<div class="correctedHTML">'.$correctedHTML.'</div><div id="suggestPopup">'.t('SuggestPopup').'</div>';
$obj->suggestions = $suggestionPairs;
$obj->fieldId = $fieldId;
echo $js->encode($obj);