<?$module_id = "vorlis.duplicates";
$CAT_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($CAT_RIGHT > "D"):
	IncludeModuleLangFile($DOCUMENT_ROOT.BX_ROOT."/modules/main/options.php");

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
 
        <input type="submit" <?if ($CAT_RIGHT < "W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
        <input type="hidden" name="Update" value="Y">
        <input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">

        <?$tabControl->End();?>
    </form>
	<?
endif;