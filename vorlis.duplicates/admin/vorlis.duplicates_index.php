<?php
use Bitrix\Main\Localization\Loc;
use \Bitrix\Iblock\PropertyEnumerationTable;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Page\Asset;
use \Vorlis\Duplicates\Duplicates;


require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php';

$title = GetMessage('VORLIS_DUBLES_TITLE');
$APPLICATION->SetTitle($title);

CJSCore::Init(array('popup', 'date','jquery'));

$RIGHT = $APPLICATION->GetGroupRight("main");
if ($RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("VORLIS_NOT_ACCESS"));

Asset::getInstance()->addCss('/bitrix/css/main/grid/webform-button.css');
Loc::loadMessages(__FILE__);
$MODULE_ID = "vorlis.duplicates";

\Bitrix\Main\Loader::includeModule($MODULE_ID);

$odj_api = new Duplicates();
// @todo: filter, table, Elements (iblock getIblock getElements getProps)
$list_id = 'tbl_dbl';
$grid_options = new GridOptions($list_id);
$sort = $grid_options->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
$nav_params = $grid_options->GetNavParams();

$nav = new PageNavigation($list_id);
$nav->allowAllRecords(true)->setPageSize($nav_params['nPageSize'])->initFromUri();
if ($nav->allRecordsShown()) {
    $nav_params = true;
} else {
    $nav_params['iNumPage'] = $nav->getCurrentPage();
}
$ui_filter = [
    ['id' => 'ACTIVE','type' => 'checkbox', "name" =>  GetMessage('VORLIS_DUBLES_ACTIVE'), 'default' => true,
        'operators' => [ 
           'default' => ''
        ]
    ],
    ['id' => 'DATE_CREATE', 'name' => GetMessage('VORLIS_DUBLES_DATES'), 'type'=>'date', 'default' => true],
];

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php';

$oMenu = new CAdminContextMenu(array(
    array(
        "TEXT"  => GetMessage('VORLIS_DUBLES_DOWLOAD'),
        "TITLE" => "title",
        "ICON"=>"btn_green",
        "LINK_PARAM"=>"onclick = 'exportF(this)'" 
    ),
));
?>
<br>
<?if($odj_api->GetIblockType() != 0){
    $ibl = $odj_api->GetIblockType();

   


    //die();
    ?>
<select id="idbl" name="idbl">
        <option value=""><?=GetMessage('VORLIS_DUBLES_CHENCH')?></option>   
        <?$ibl = $odj_api->GetIblockType();
         
        ?>
<?foreach($ibl as $key=>$val){

    ?>
    <?foreach($ibl[$key] as $iblock=>$name ){?>
        <option data-type="<?=$iblock?>" value="<?=$iblock?>"><?=$name?></option>
<?}

    }?>
</select>

<select id="opti" name="opti">
    <option value=""><?=GetMessage('VORLIS_DUBLES_CHENCH2')?></option>
</select>
<div style="margin:15px 0;">
    <input type="button" id="start_search" value="<?=GetMessage('VORLIS_DUBLES_SEARCH')?>" disabled class="adm-btn-save" style="height: 25px !important; padding-top: 0">
</div>
<?}else{
    die(GetMessage('VORLIS_DUBLES_NOIBLOKS'));
}?>
 <hr>
<?if(!empty($_REQUEST['IBLOCK_ID']) && !empty($_REQUEST["GROUP"])){?>
    <div>
        <?$APPLICATION->IncludeComponent('vorlis:main.ui.filter', '', [
            'FILTER_ID' => $list_id,
            'GRID_ID' => $list_id,
            'FILTER' => $ui_filter,
            'ENABLE_LIVE_SEARCH' => true,
            'ENABLE_LABEL' => true,
            'DISABLE_SEARCH' => true
        ]);?>
    </div>
    <div style="clear: both;"></div>
    <hr>
<?php
$filterOption = new Bitrix\Main\UI\Filter\Options($list_id);
$filterData = $filterOption->getFilter([]);

foreach ($filterData as $k => $v){
    if($k == "DATE_CREATE_from"){
        $filterData['>DATE_CREATE'] = $v;
    }
    if($k == "DATE_CREATE_to"){
        $filterData['<DATE_CREATE'] = $v;
    }
}

if($odj_api->GetPropsName($_REQUEST['IBLOCK_ID'],$_REQUEST['GROUP']) == 0){
    $frst_col = $odj_api->GetName($_REQUEST['GROUP']);
}else{
    $frst_col = $odj_api->GetPropsName($_REQUEST['IBLOCK_ID'],$_REQUEST['ATT']);
}
$columns = [];
$columns[] = ['id' => 'ID', 'name' => $frst_col, 'sort' => $frst_col, 'default' => true];
$columns[] = ['id' => 'CNT', 'name' => GetMessage('VORLIS_DUBLES_COUNT'), 'sort' => 'CNT', 'default' => true];
$columns[] = ['id' => 'IDS', 'name' => GetMessage('VORLIS_DUBLES_ID'), 'sort' => 'ID', 'default' => true];
// bd odject
$filterData['IBLOCK_ID'] = $_REQUEST["IBLOCK_ID"];

$res = $odj_api->GetMainGroup($sort['sort'],$filterData,$_REQUEST["GROUP"],$nav_params);
$nav->setRecordCount($res->selectedRowsCount());
$tp =$odj_api->GetOneType($_REQUEST['IBLOCK_ID']);
    while($row = $res->GetNext()) {
        if(stripos($_REQUEST['GROUP'],"PROPERTY_") !== false){
            if(empty($row[$_REQUEST["GROUP"].'_VALUE'])){
                $TBL_NAME = "Empty";
            }else{
                $TBL_NAME = $row[$_REQUEST["GROUP"].'_VALUE'];
            }

            $row['ID'] = $odj_api->GetIdbyPropName($_REQUEST['IBLOCK_ID'],$_REQUEST['ATT'], $TBL_NAME);
            
        }else{
            if(empty($row[$_REQUEST["GROUP"]])){
                $TBL_NAME = "Empty"; 
            }else{
                $TBL_NAME = $row[$_REQUEST['GROUP']];
            }
            $row['ID'] = $odj_api->GetIdbyName($_REQUEST['IBLOCK_ID'],$_REQUEST["GROUP"],$TBL_NAME);
        }
         
        $row['LINK'] = '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='.$_REQUEST['IBLOCK_ID'].'&type='.$tp.'&lang=ru&ID='.$row['ID'].'&find_section_section=0&WF=Y';
        $list[] = [
            'data' => [
                "ID"    => $TBL_NAME,
                "CNT"   => $row["CNT"],
                "IDS" => $row["ID"]

            ],
            'actions' => [
                [
                'text'    => GetMessage('VORLIS_DUBLES_EDIT'),
                'default' => true,
                'onclick' => 'Edit("'.$row['LINK'].'");'
                ]
            ]
         ];
    }
    $oMenu->Show();
    $APPLICATION->IncludeComponent('vorlis:main.ui.grid', '.default', [
    'GRID_ID' => $list_id,
    'COLUMNS' => $columns,
    'ROWS' => $list,
    'SHOW_ROW_CHECKBOXES' => false,
    'NAV_OBJECT' => $nav,
    'AJAX_MODE' => 'Y',
    'AJAX_ID' => \CAjax::getComponentID('vorlis:main.ui.grid', '.default', ''),
    'PAGE_SIZES' =>  [
        ['NAME' => '10', 'VALUE' => '10'],
        ['NAME' => '50', 'VALUE' => '50'],
        ['NAME' => '1000', 'VALUE' => '1000'],
        ['NAME' => 'all', 'VALUE' => '100000']
    ],
    'TOTAL_ROWS_COUNT' => $nav->getRecordCount(),
    'AJAX_OPTION_JUMP'          => 'N',
    'SHOW_CHECK_ALL_CHECKBOXES' => false,
    'SHOW_ROW_ACTIONS_MENU'     => true,
    'SHOW_GRID_SETTINGS_MENU'   => true,
    'SHOW_NAVIGATION_PANEL'     => true,
    'SHOW_PAGINATION'           => true,
    'SHOW_SELECTED_COUNTER'     => false,
    'SHOW_TOTAL_COUNTER'        => true,
    'SHOW_PAGESIZE'             => true,
    'SHOW_ACTION_PANEL'         => false,
    'ALLOW_COLUMNS_SORT'        => true,
    'ALLOW_COLUMNS_RESIZE'      => true,
    'ALLOW_HORIZONTAL_SCROLL'   => true,
    'ALLOW_SORT'                => true,
    'ALLOW_PIN_HEADER'          => true,
    'AJAX_OPTION_HISTORY'       => 'Y'
    ]);

    }
?>
<script src="/bitrix/js/<?=$MODULE_ID?>/jquery.chained.remote.min.js"></script>
<script src="/bitrix/js/<?=$MODULE_ID?>/script.js"></script>
<SCRIPT>
$('.main-grid-row-body td:nth-child(5n)').remove();
<?if(!empty($_REQUEST["IBLOCK_ID"])){?>

$("#idbl").val('iblock_id_<?=$_REQUEST["IBLOCK_ID"]?>').change();
setTimeout(function (){
    $("#opti").val('<?=$_REQUEST["GROUP"]?>').change();
}, 190);
<?}?>
</SCRIPT>
<?require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php';