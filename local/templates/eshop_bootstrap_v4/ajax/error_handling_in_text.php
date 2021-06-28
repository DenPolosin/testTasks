<?php

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

global $USER, $APPLICATION;

$request = Context::getCurrent()->getRequest();
$postList = $request->getPostList()->getValues();
if ((isset($postList['AJAX']) && $postList['AJAX'] !== 'Y') || !$request->isPost()) {
    CHTTP::SetStatus('404 Not Found');
    @define('ERROR_404', 'Y');
}

if (Loader::includeModule('form') === false) {
    echo json_encode(
        ['status' => 'error', 'msg' => Loc::getMessage('FAILED_TO_CONNECT_MODULE_FORM')],
        JSON_THROW_ON_ERROR
    );
    die();
}

$formSid = 'TEXT_ERROR';
$rsForm = CForm::GetBySID($formSid);
if ($rsForm->SelectedRowsCount() !== 1) {
    echo json_encode(
        ['status' => 'error', 'msg' => Loc::getMessage('FORM_NOT_FOUND')],
        JSON_THROW_ON_ERROR
    );
    die();
}

$arForm = $rsForm->Fetch();

$arFormFields = [
    'ERROR_TEXT_QUESTION' => 'SELECTED_TEXT',
    'URL_QUESTION' => 'URL',
];
foreach ($arFormFields as $fieldSID => $postCode) {
    $rsField = (new CFormField())->GetBySID($fieldSID);
    if ($rsField->SelectedRowsCount() !== 1) {
        echo json_encode(
            ['status' => 'error', 'msg' => Loc::getMessage('NO_FIELDS_FOUND')],
            JSON_THROW_ON_ERROR
        );
        die();
    }

    $arField = $rsField->Fetch();

    $by = null;
    $order = null;
    $arFilter = [];
    $isFiltered = [];
    $rsAnswers = (new CFormAnswer())->GetList($arField['ID'], $by, $order, $arFilter, $isFiltered);
    if ($rsAnswers->SelectedRowsCount() !== 1) {
        echo json_encode(
            ['status' => 'error', 'msg' => Loc::getMessage('NO_ANSWER_LIST_FOUND')],
            JSON_THROW_ON_ERROR
        );
        die();
    }

    $arAnswer = $rsAnswers->Fetch();
    $formValues['form_' . $arAnswer['FIELD_TYPE'] . '_' . $arAnswer['ID']] = $postList[$postCode];
}

$result = (new CFormResult())->Add($arForm['ID'], $formValues);
if ($result === false) {
    global $strError;

    echo json_encode(['status' => 'error', 'msg' => $strError, 'data' => $formValues]);
    die();
}

echo json_encode(['status' => 'success']);
