<?php

namespace Gilyazov\Core;

/**
 * Class Tools
 * @package Gilyazov
 */
class Tools
{
   
    /**
     * Функция склонения числительных в русском языке
     *
     * @param int $number Число которое нужно просклонять
     * @param array $titles Массив слов для склонения
     * @return string
     * пример использования
     * echo 'Я знаю '.declOfNum(5, array('иностранный язык', 'иностранных языка', 'иностранных языков'));
     **/
    public static function declOfNum($number, $titles)
    {
        $cases = array (2, 0, 1, 1, 1, 2);
        return $number." ".$titles[ ($number%100 > 4 && $number %100 < 20) ? 2 : $cases[min($number%10, 5)] ];
    }

    public static function resizeImage($id, $width, $height, $adaptiveH=false)
    {
        $imageFile = \CFile::GetFileArray($id);
        $arFileTmp = \CFile::ResizeImageGet($imageFile,  array("width" => $width, "height" => ($adaptiveH ? $width * $imageFile["HEIGHT"] / $imageFile["WIDTH"] : $height)),  BX_RESIZE_IMAGE_EXACT, true);
        $photo_img = $arFileTmp["src"];

        return $photo_img;
    }
}