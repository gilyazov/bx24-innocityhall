<?php

namespace Gilyazov\Core\Bizproc\EventHandlers;

class Workflow
{

    /*
     * При запуске БП найдем все команды согласования и составим лист согласования
     * */
    public static function OnCreateWorkflow($templateId, $arDocumentId, $targetUser, $uid){
        $elementID = $arDocumentId[2];

        $res = \CBPWorkflowTemplateLoader::GetList(['ID' => 'DESC'], ['ID' => $templateId], $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array());
        if($arFields = $res->GetNext())
        {
            foreach ($arFields['TEMPLATE'][0]['Children'] as $arTemplate){
                $statusName = $arTemplate['Properties']['Title'];
                // команды статусов
                $activityList = self::searchApproveActivity($arTemplate, true);

                $sheet[] = [
                    'id' => $arTemplate['Name'],
                    'name' => $statusName,
                    'approveActivity' => $activityList
                ];
            }

            // сохраним как json в элемент
            \CIBlockElement::SetPropertyValuesEx($elementID, false, array("REVIEWERS_JSON" => \Bitrix\Main\Web\Json::encode($sheet)));
        }
    }

    protected static function searchApproveActivity($arTasks, $first = false): array
    {
        static $result = [];

        if ($first){
            $result = [];
        }
        if ($arTasks['Type'] === 'ApproveActivity') {
            $result[] = $arTasks['Properties'];
        }

        foreach ($arTasks['Children'] as $arTask) {
            if (is_array($arTask['Children'])) {
                self::searchApproveActivity($arTask);
            }
        }

        return $result;
    }

    protected static function getIblockByElementID(int $id): int
    {
        $res = \CIBlockElement::GetByID($id);
        if ($arElement = $res->GetNext()) {
            return $arElement["IBLOCK_ID"];
        }
    }
}
