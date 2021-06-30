<?php

use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

global $USER, $APPLICATION;

$request = Context::getCurrent()->getRequest();
$postList = $request->getPostList()->getValues();

if (!isset($postList['AJAX']) || $postList['AJAX'] !== 'Y' || empty($postList['XML_IDS']) || !$request->isPost()) {
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

if (Loader::includeModule('sale') === false) {
    echo json_encode(
        ['status' => 'error', 'msg' => Loc::getMessage('FAILED_TO_CONNECT_MODULE_SALE')],
        JSON_THROW_ON_ERROR
    );
    die();
}

$dbRes = ElementTable::getList([
    'select' => [
        'ID',
    ],
    'filter' => [
        'XML_ID' => $postList['XML_IDS'],
    ],
]);

if ($dbRes->getSelectedRowsCount() === 0) {
    echo json_encode(
        ['status' => 'error', 'msg' => Loc::getMessage('ELEMENTS_NOT_FOUND')],
        JSON_THROW_ON_ERROR
    );
    die();
}

$productIds = [];
while ($fields = $dbRes->fetch()) {
    $productIds[$fields['ID']] = $fields['ID'];
}

$basket = Basket::loadItemsForFUser(Fuser::getId(), Context::getCurrent()->getSite());
foreach ($productIds as $productId) {
    if ($item = $basket->getExistsItem('catalog', $productId)) {
        $item->setField('QUANTITY', 1);
    } else {
        $item = $basket->createItem('catalog', $productId);
        $item->setFields(array(
            'QUANTITY' => 1,
            'CURRENCY' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
            'LID' => Bitrix\Main\Context::getCurrent()->getSite(),
            'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
        ));
    }
}

$result = $basket->save();
$status = $result->isSuccess() ? 'success' : 'error';
$data = [
    'status' => $status,
];
$errors = $result->getErrorMessages();
if (count($errors) > 0) {
    $errorsStr = implode("\n", $errors);
    $data['msg'] = $errorsStr;
} else {
    $data['msg'] = Loc::getMessage('ELEMENTS_ADDED_SUCCESSFULLY');
}

echo json_encode($data);
