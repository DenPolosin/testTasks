<?php

use Bitrix\Iblock\ElementPropertyTable;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

global $USER, $APPLICATION;

$request = Context::getCurrent()->getRequest();
$postList = $request->getPostList()->getValues();
if (!isset($postList['AJAX']) || $postList['AJAX'] !== 'Y' || !$request->isPost()) {
    CHTTP::SetStatus('404 Not Found');
    @define('ERROR_404', 'Y');
}

if (Loader::includeModule('iblock') === false) {
    echo json_encode(
        ['status' => 'error', 'msg' => Loc::getMessage('FAILED_TO_CONNECT_MODULE_IBLOCK')],
        JSON_THROW_ON_ERROR
    );
    die();
}

$dbRes = ElementTable::getList([
    'select' => [
        'ID',
        'NAME',
    ],
    'filter' => [
        'XML_ID' => $postList['XML_ID'],
    ],
    'limit' => 1,
]);
if ($dbRes->getSelectedRowsCount() !== 1) {
    echo json_encode(
        ['status' => 'error', 'msg' => Loc::getMessage('ELEMENT_NOT_FOUND')],
        JSON_THROW_ON_ERROR
    );
    die();
}

$elementFields = $dbRes->fetch();
$dbRes = ElementPropertyTable::getList([
    'select' => [
        'VALUE',
        'PROPERTY_NAME' => 'P.NAME',
        'PROPERTY_CODE' => 'P.CODE',
    ],
    'filter' => [
        'IBLOCK_ELEMENT_ID' => $elementFields['ID'],
        'PROPERTY_CODE' => ['ARTNUMBER', 'COLOR_REF', 'SIZES_CLOTHES', 'CML2_LINK'],
    ],
    'runtime' => [
        new Reference(
            'P',
            PropertyTable::class,
            Join::on('this.IBLOCK_PROPERTY_ID', 'ref.ID')
        ),
    ],
]);

$properties = [];
if ($dbRes->getSelectedRowsCount() > 0) {
    while ($fields = $dbRes->fetch()) {
        $properties[$fields['PROPERTY_CODE']] = [
            'NAME' => $fields['PROPERTY_NAME'],
            'VALUE' => $fields['VALUE'],
        ];
    }
}

if (!empty($properties['CML2_LINK']['VALUE'])) {
    $arSize = ['width' => 100, 'height' => 100];
    $dbRes = ElementTable::getList([
        'select' => [
            'DETAIL_PICTURE',
            'DETAIL_TEXT',
        ],
        'filter' => [
            'ID' => $properties['CML2_LINK']['VALUE'],
        ],
        'limit' => 1,
    ]);
    if ($dbRes->getSelectedRowsCount() > 0) {
        $parentElementFields = $dbRes->fetch();
        $detailPicture = CFile::ResizeImageGet($parentElementFields['DETAIL_PICTURE'], $arSize);
        $elementFields['DETAIL_PICTURE'] = $detailPicture['src'];
        $elementFields['DETAIL_TEXT'] = $parentElementFields['DETAIL_TEXT'];
    }
}

unset($properties['CML2_LINK']);

$elementFields['PROPERTIES'] = $properties;

echo json_encode(['status' => 'success', 'data' => $elementFields]);
