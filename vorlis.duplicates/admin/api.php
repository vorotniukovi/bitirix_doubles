<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
$MODULE_ID = "vorlis.duplicates";
IncludeModuleLangFile(__FILE__);
\Bitrix\Main\Loader::includeModule($MODULE_ID);
$odj_api = new \Vorlis\Duplicates\Duplicates();


$mainfields = [
    "NAME" => GetMessage("API_NAME"),
    "CODE" => GetMessage("API_CODE"),
    "XML_ID" => GetMessage("API_XML_ID"),
    "SORT" => GetMessage("API_SORT"),
    "DATE_ACTIVE_FROM" => GetMessage("API_DATE_ACTIVE_FROM"),
    "DATE_ACTIVE_TO" => GetMessage("API_DATE_ACTIVE_TO"),
    "TAGS" => GetMessage("API_TAGS"),
    "CREATED_USER_NAME" => GetMessage("API_CREATED_USER_NAME"),
];


if(stripos($_GET['idbl'], "iblock_id") !== false){
    $pattern = '/[^0-9]/';
    $iblock_id = preg_replace($pattern, "", $_GET['idbl']);
    $response = $odj_api->GetProps($iblock_id);
    if($response !== 0){
        $FandProps = array_merge($mainfields, $response);
        echo json_encode($FandProps);
    }else{
        echo json_encode($mainfields);
    }
    
}


