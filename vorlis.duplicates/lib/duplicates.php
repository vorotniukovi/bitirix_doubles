<?php
namespace Vorlis\Duplicates;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\TypeTable;
use Bitrix\Iblock\PropertyTable;
/**
 * Class Duplicates for api
 */
class Duplicates {

    public function __construct(){
        Loader::includeModule('iblock');
    }
    /**
     * GetIblocks private method, get all IBLOCKS names
     * @var string
     */
    private function GetIblocks($type){
        $result = IblockTable::getList( [
            'select' => ['ID', 'NAME'],
            'filter' => ['IBLOCK_TYPE_ID' => $type],
        ] );
        while ($row = $result->fetch()) {
            $iblocks["iblock_id_".$row['ID']] = $row['NAME'];
        }
        return $iblocks ? $iblocks : 0;
    }
    /**
     * GetIblockType public method, get all type iblocks
     */
    public function GetIblockType(){
        $iblockTypes = TypeTable::getList(array(
            'select' => array('*', 'NAME' => 'LANG_MESSAGE.NAME'),
            'filter' => array('=LANG_MESSAGE.LANGUAGE_ID' => 'ru')
        ))->FetchAll();
        foreach($iblockTypes as $typeinfo){
            $iblocks = $this->GetIblocks($typeinfo['ID']);

            if($iblocks != NULL){
                $types[$typeinfo['ID']] = $iblocks;
            }

        }
        return $types ? $types : 0;
    }
    /**
     * GetOneType public method
     */
    public function GetOneType($id){
        $iblockOneType = IblockTable::getList( [
            'select' => ['ID', 'NAME', 'IBLOCK_TYPE_ID'],
            'filter' => ['ID'=> $id],
        ] )->FetchAll();
        return $iblockOneType[0]['IBLOCK_TYPE_ID'] ? $iblockOneType[0]['IBLOCK_TYPE_ID'] : NULL;
    }
    /**
     * GetProps method, get all PROPS values
     */
    public function GetProps($iblock_id){
        $propertyList = PropertyTable::getList(array(
            "select" => array("NAME","CODE","ID"),
            "filter" => array("=IBLOCK_ID" => $iblock_id, "MULTIPLE"=>'N',"ACTIVE"=>"Y")));
        while ($row = $propertyList->fetch()) {
            $json["PROPERTY_".$row["ID"]] = $row["CODE"]." ".$row["NAME"]." [".$row["ID"]."]";
        }
        return $json ? $json : 0;
    }
    /**
     * GetMainGroup public method
     *  @todo: this  methods need to be rewritten on D7 on new update Let"s Start
     */
    public function GetMainGroup($sort,$arFilter,$GROUP,$nav_params){
        $rsData = \CIBlockElement::GetList($sort, $arFilter, array($GROUP), $nav_params);
        return $rsData ? $rsData : 0;
    }
    public function GetIdbyName($IBLOCK_ID,$FIELD_NAME,$name){
        if($name == 'Empty'){
            $name = false;
        }
        $arFilter = Array("IBLOCK_ID"=>IntVal($IBLOCK_ID),$FIELD_NAME=>$name);
        $arSelect = Array("ID", "IBLOCK_ID","NAME","PROPERTY_*");

        $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        if($ob = $res->GetNextElement()){
            $arFields = $ob->GetFields();

            $odj = $arFields["ID"];
        }
        return $odj ? $odj : 0;
    }
    public function GetIdbyPropName($IBLOCK_ID,$PROP_ATT,$VALUE){
        if($VALUE == 'Empty'){
            $VALUE = false;
        }
        $arFilter = Array("IBLOCK_ID"=>IntVal($IBLOCK_ID),"PROPERTY_".$PROP_ATT=>$VALUE);
        $arSelect = Array("ID", "IBLOCK_ID","NAME","PROPERTY_*");

        $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        if($ob = $res->GetNextElement()){
            $arFields = $ob->GetFields();

            $odj = $arFields["ID"];
        }
        return $odj ? $odj : 0;
    }
    public function GetPropsName($iblock_id,$code){
        $properties = \CIBlockProperty::GetList(Array("sort"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$iblock_id,"MULTIPLE"=>'N', "CODE" => $code));
        if($prop_fields = $properties->GetNext()){
            $json =  $prop_fields["NAME"];
        }
        return $json ? $json : 0;
    }
    public function GetName($code){
        $mainfields = [
            "NAME" => "NAME".GetMessage("API_NAME"),
            "CODE" => "CODE".GetMessage("API_CODE"),
            "XML_ID" => "XML_ID".GetMessage("API_XML_ID"),
            "SORT" => GetMessage("API_SORT"),
            "DATE_ACTIVE_FROM" => GetMessage("API_DATE_ACTIVE_FROM"),
            "DATE_ACTIVE_TO" => GetMessage("API_DATE_ACTIVE_TO"),
            "TAGS" => GetMessage("API_TAGS"),
            "CREATED_USER_NAME" => GetMessage("API_CREATED_USER_NAME"),
        ];
        return  $mainfields[$code];
    }
    public function AllData($sort,$arFilter,$GROUP,$nav_params){

        $rsData = \CIBlockElement::GetList($sort, $arFilter, array($GROUP), $nav_params);
        while($item = $rsData->Fetch()){
            $data[] = $item;
        }

        return $data ? $data : 0;
    }
}