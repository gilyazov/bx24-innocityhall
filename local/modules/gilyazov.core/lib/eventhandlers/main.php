<?php
namespace Gilyazov\Core\EventHandlers;
use Gilyazov\Core\FileFromTemplate;

class Main
{
    public function OnBeforeProlog()
    {
        global $USER;
        if ($USER->IsAdmin() && $_REQUEST['vacation'] == 1 && check_bitrix_sessid())
        {
            $GLOBALS['APPLICATION']->RestartBuffer();

            # надо запилить отдельный метод
            \CModule::IncludeModule('iblock');
            $arSelect = Array("ID", "IBLOCK_ID", "NAME", "CREATED_BY");
            $arFilter = Array("IBLOCK_ID"=>31, "ID"=> (int)$_REQUEST['element'], "ACTIVE"=>"Y");
            $res = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            if($ob = $res->GetNextElement()){
                $arFields = $ob->GetFields();
                $arProps = $ob->GetProperties();
                $rsUser = \CUser::GetByID($arFields['CREATED_BY']);
                if ($arUser = $rsUser->Fetch())
                {
                    $dateFrom = new \DateTime($arProps['S']['VALUE']);
                    $dateTo = new \DateTime($arProps['PO']['VALUE']);
                    $dayDiff = $dateFrom->diff($dateTo)->format('%a');

                    $params = [
                        'FROM_JOB' => $arUser['WORK_POSITION'],
                        'FROM_FIO' => $arUser['LAST_NAME'] . ' ' . $arUser['NAME'],
                        'DURATION' => $dayDiff,
                        'FROM_DATE' => $arProps['S']['VALUE'],
                        'CURRENT_DATE' => date('d.m.Y')
                    ];
                    foreach ($params as $key => $letterParam) {
                        $paramToLetter[$key] = preg_replace('/\s+/', ' ', trim($letterParam));
                    }

                    $file = new FileFromTemplate\Docx('vacation_'.$arProps['TIP_OTPUSKA']['VALUE_XML_ID'], $paramToLetter, ['USE_REGEX' => true]);
                    $file->generate();
                    echo $file->getGeneratedFileAsString();
                    header("Content-Disposition: attachment; filename=vacation_".$arUser['ID'].".docx");
                    die();
                }
            }
        }

        return true;
    }
}
