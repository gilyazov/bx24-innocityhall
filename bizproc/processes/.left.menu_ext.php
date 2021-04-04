<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$aMenuB24 = array();
$aMenuB24[] = Array(
    "Служебные записки",
    "/bizproc/processes/33/view/0/",
    Array(),
    Array(),
    "CSite::InGroup(array(1, 3))"
);
$aMenuB24[] = Array(
    "Закупки до 10.000р.",
    "/bizproc/processes/37/view/0/",
    Array(),
    Array(),
    "CSite::InGroup(array(1, 3))"
);
$aMenuB24[] = Array(
    "Закупки от 10.000р.",
    "/bizproc/processes/36/view/0/",
    Array(),
    Array(),
    "CSite::InGroup(array(1, 3))"
);
$aMenuB24[] = Array(
    "Счет на оплату",
    "/bizproc/processes/35/view/0/",
    Array(),
    Array(),
    "CSite::InGroup(array(1, 3))"
);
$aMenuB24[] = Array(
    "Приказы",
    "/bizproc/processes/38/view/0/",
    Array(),
    Array(),
    "CSite::InGroup(array(1, 3))"
);

$aMenuLinks = array_merge($aMenuB24, $aMenuLinks);
?>