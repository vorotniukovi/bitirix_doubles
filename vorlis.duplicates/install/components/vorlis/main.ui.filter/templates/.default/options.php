<?php
$module_id = "solverweb.sitemap";
$CAT_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($CAT_RIGHT > "D"):
	IncludeModuleLangFile($DOCUMENT_ROOT.BX_ROOT."/modules/main/options.php");

    include_once(__DIR__ . '/include.php');

    if ($REQUEST_METHOD == "GET" && strlen($RestoreDefaults) > 0 && $CAT_RIGHT == "W" && check_bitrix_sessid())
    {
        COption::RemoveOption($module_id);
        $z = CGroup::GetList($sort = "id", $order = "asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
        while($zr = $z->Fetch())
            $APPLICATION->DelGroupRight($module_id, array($zr["ID"]));

        LocalRedirect($APPLICATION->GetCurPage()."?lang=".LANG."&mid=".urlencode($mid));
    }

    $aTabs = array(
        array(
			"DIV" => "edit1",
			"TAB" => GetMessage("MAIN_TAB_RIGHTS"),
			"ICON" => "sitemap_settings",
			"TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")
		),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);

    $tabControl->Begin();
    ?>
    <form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&lang=<?echo LANGUAGE_ID?>" name="<?=$module_id?>">
        <?
		echo bitrix_sessid_post();

        $tabControl->BeginNextTab();

        require_once($DOCUMENT_ROOT.BX_ROOT."/modules/main/admin/group_rights.php");

        $tabControl->Buttons();
		?>
        <script type="text/javascript">
            function RestoreDefaults()
            {
                if (confirm('<? echo GetMessageJS("MAIN_HINT_RESTORE_DEFAULTS_WARNING"); ?>'))
                    window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANGUAGE_ID; ?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>";
            }
        </script>
        <input type="submit" <?if ($CAT_RIGHT < "W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
        <input type="hidden" name="Update" value="Y">
        <input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
        <input type="button" <?if ($CAT_RIGHT < "W") echo "disabled" ?> title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
        <?$tabControl->End();?>
    </form>
	<?
endif;