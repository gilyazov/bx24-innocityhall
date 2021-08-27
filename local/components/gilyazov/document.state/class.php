<?php

use Gilyazov\Core\Bizproc\Helper;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

class DocumentStateComponent extends CBitrixComponent
{
    private $modules = ['crm', 'iblock'];
    private $elementID = '';
    private $iblockID = '';

    public function setBaseParams()
    {
        $this->iblockID = $this->arParams['IBLOCK_ID'];
        $this->elementID = $this->arParams['ELEMENT_ID'];
    }

    public function includeModules(): void
    {
        foreach($this->modules as $module) {
            Loader::includeModule($module);
        }
    }

    public function GetDocumentStates()
    {
        $arDocumentStates = Helper::getDocumentStates($this->elementID, $this->iblockID);

        $arGroups = \CUser::GetUserGroup($GLOBALS["USER"]->GetID());
        $arOpertions = \CBPDocument::GetAllowableOperations($GLOBALS["USER"]->GetID(), $arGroups, $arDocumentStates, true);

        if (!$arOpertions)
            return '';

        foreach ($arDocumentStates as $arDocumentState)
        {
            if($arDocumentState['STATE_NAME'] === 'Completed'){
                continue;
            }
            if(!$arDocumentState['DOCUMENT_ID']){
                continue;
            }

            $arState = $arDocumentState;
        }

        return $arState;
    }

    public function executeComponent()
    {
        $this->includeModules();
        $this->setBaseParams();
        $this->arResult['STATE'] = $this->GetDocumentStates();

        $this->includeComponentTemplate();
    }
}
