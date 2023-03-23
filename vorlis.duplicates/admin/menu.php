<?php

use Bitrix\Main\Localization\Loc;
$MODULE_ID = 'vorlis.duplicates';
$MODULE_CODE = 'vorlis_duplicates';
Loc::loadMessages(__FILE__);
$arMenu = array(
    array(
        "parent_menu" => "global_menu_services",
        "sort"        => 1,
        "url"         => '/bitrix/admin/' . $MODULE_ID . '_index.php?lang=' . LANGUAGE_ID,
        'text' => Loc::getMessage('DUPLICATES_MODULE_MENU_TEXT'),
        'title' => Loc::getMessage('DUPLICATES_MODULE_MENU_TITLE'),
        "icon"        => 'sys_menu_icon',
        "page_icon"   => 'sys_menu_icon',
        "items"       => array(),
        'more_url'    => array(
            '/bitrix/admin/' . $MODULE_ID . '_index.php?lang=' . LANGUAGE_ID,
        ),
    )
);
return $arMenu;
