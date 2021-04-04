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
            if ($arProp['VALUE'])
            {
                return \Bitrix\Main\Web\Json::decode($arProp['~VALUE']);
            }
        }
    }

    public function getSheetWithUsers()
    {
        $arSheet = $this->getReviewersArr();

        /*echo '<pre>';
        print_r($arSheet);
        echo '</pre>';*/

        foreach ($arSheet as &$arStatus)
        {
            foreach ($arStatus['approveActivity'] as &$arActivity)
            {
                foreach ($arActivity['Users'] as $userConst)
                {
                    $user = str_replace(['{=', '}'], '', $userConst);
                    $arUser = explode(':', $user);
                    $bitrixUser = [];

                    switch ($arUser[0]) {
                        case 'GlobalConst':
                            $bitrixUser = Bitrix\Bizproc\Workflow\Type\GlobalConst::getValue($arUser[1]);
                            $bitrixUser = [str_replace('user_', '', $bitrixUser)];
                            break;
                        case 'Document':
                            $res = \CIBlockElement::GetProperty($this->arParams['IBLOCK_ID'], $this->arParams['ELEMENT_ID'], "sort", "asc", array("CODE" => str_replace('PROPERTY_', '', $arUser[1])));
                            while ($arProp = $res->GetNext())
                            {
                                if ($arProp['VALUE'])
                                {
                                    $bitrixUser[] = $arProp['VALUE'];
                                }
                            }
                            break;
                    }

                    if ($bitrixUser)
                    {
                        $rsUser = CUser::GetList(($by="ID"), ($order="ASC"), ['ID' => implode('|', $bitrixUser)]);
                        while ($arUser = $rsUser->Fetch())
                        {
                            $arActivity['REAL_USER'][$arUser['ID']] = $arUser;
                        }
                    }
                    unset($bitrixUser);
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
