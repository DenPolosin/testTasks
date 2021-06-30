<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

define('HIDE_SIDEBAR', true);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

global $APPLICATION;

$APPLICATION->SetTitle(Loc::getMessage('ADDING_TO_CART_PAGE_TITLE'));
$asset = Asset::getInstance();
$asset->addCss(SITE_TEMPLATE_PATH . '/css/adding_to_cart.css');
$asset->addJs(SITE_TEMPLATE_PATH . '/js/adding_to_cart.js');
?>

    <form id="atc_form" action="/local/templates/eshop_bootstrap_v4/ajax/add_to_basket.php" method="post">
        <input type="hidden" name="AJAX" value="Y">
        <div class="atc_add-to-cart">
            <input
                    id="atc_add-to-basket"
                    type="submit"
                    class="btn btn-primary"
                    value="<?= Loc::getMessage('ADDING_TO_CART_BUTTON_ADD_TO_CART_NAME') ?>"
            >
        </div>
        <div id="atc_container">
            <div class="atc_block atc_block_head">
                <div class="atc_input_container">
                    <?= Loc::getMessage('ADDING_TO_CART_COLUMN_NAME_PRODUCT_CODE') ?>
                </div>
                <div class="atc_product-info">
                    <?= Loc::getMessage('ADDING_TO_CART_COLUMN_NAME_PRODUCT_INFO') ?>
                </div>
                <div class="atc_delete_row">
                    <?= Loc::getMessage('ADDING_TO_CART_COLUMN_NAME_DELETE_ROW') ?>
                </div>
            </div>
        </div>
    </form>

    <script type="text/javascript">
        window.buttonDeleteButtonName = <?= json_encode(Loc::getMessage('ADDING_TO_CART_BUTTON_DELETE_NAME'), JSON_THROW_ON_ERROR) ?>;
        window.buttonAddButtonName = <?= json_encode(Loc::getMessage('ADDING_TO_CART_BUTTON_ADD_NAME'), JSON_THROW_ON_ERROR) ?>;
    </script>

<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>