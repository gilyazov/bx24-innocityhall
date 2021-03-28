<?php

use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid\Options;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

class CrmReportPlanComponent extends CBitrixComponent
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

    public function getReviewersArr()
    {
        $res = \CIBlockElement::GetProperty($this->iblockID, $this->elementID, "sort", "asc", array("CODE" => "REVIEWERS_JSON"));
        if ($arProp = $res->GetNext())
        {
            return \Bitrix\Main\Web\Json::decode($arProp['~VALUE']);
        }
    }

    public function getSheetWithUsers()
    {
        $arSheet = $this->getReviewersArr();

        foreach ($arSheet as &$arStatus)
        {
            foreach ($arStatus['approveActivity'] as &$arActivity)
            {
                foreach ($arActivity['Users'] as $userConst)
                {
                    $user = str_replace(['{=', '}'], '', $userConst);
                    $arUser = explode(':', $user);
                    if ($arUser[0] == 'GlobalConst'){
                        $bitrixUser = Bitrix\Bizproc\Workflow\Type\GlobalConst::getValue($arUser[1]);
                        $bitrixUser = str_replace('user_', '', $bitrixUser);
                    }
                    $rsUser = CUser::GetByID($bitrixUser);
                    if ($arUser = $rsUser->Fetch())
                    {
                        $arActivity['REAL_USER'][$bitrixUser] = $arUser;
                    }
                }
            }
        }

        return $arSheet;
    }

    public function executeComponent()
    {
        $this->includeModules();
        $this->setBaseParams();
        $this->arResult['SHEET'] = $this->getSheetWithUsers();

        $this->includeComponentTemplate($this->arParams['EXPORT'] ? 'excel' : '');
    }
}
