<?php
/**
 * Created by PhpStorm.
 * User: a.gilyazov
 * Date: 10/05/2021
 * Time: 10:57 PM
 */

namespace Gilyazov\Core\Bizproc;


class Helper
{
    public static function getDocumentState($documentId, $documentIblockId = false, $userId = false)
    {
        return current(self::getDocumentStates($documentId, $documentIblockId, $userId));
    }

    public static function getDocumentStates($documentId, $documentIblockId = false, $userId = false)
    {
        if (!$userId) {
            $userId = $GLOBALS['USER']->getId();
        }
        if (!$userId) {
            return array();
        }

        if ($documentId > 0 && false === $documentIblockId) {
            $arElement = \CIBlockElement::GetList(
                array(),
                array('ID' => $documentId),
                false,
                false,
                array('ID', 'IBLOCK_ID')
            )->Fetch();
            if (!$arElement) {
                return array();
            }
            $documentIblockId = $arElement['IBLOCK_ID'];
        }
        return \CBPDocument::GetDocumentStates(
            self::getDocumentComplexType($documentIblockId),
            self::getDocumentComplexId($documentId)
        );
    }

    public static function getDocumentComplexType($iblockId)
    {
        return $iblockId ? array(self::getDocumentModuleId(), self::getDocumentClassName(), 'iblock_' . $iblockId) : null;
    }

    public static function getDocumentComplexId($documentId)
    {
        return $documentId ? array(self::getDocumentModuleId(), self::getDocumentClassName(), $documentId) : null;
    }

    public static function getDocumentModuleId()
    {
        return 'lists';
    }

    public static function getDocumentClassName()
    {
        return 'BizprocDocument';
    }
}