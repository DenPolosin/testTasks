<?php

namespace Sprint\Migration;


class Version20210628211129 extends Version
{
    protected $description = "Создание формы";

    protected $moduleVersion = "3.28.7";

    private const SID = 'TEXT_ERROR';

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $formHelper = $helper->Form();
        $formId = $formHelper->saveForm(array(
            'NAME' => 'Сообщения об ошибках в тексте',
            'SID' => self::SID,
            'BUTTON' => 'Сохранить',
            'C_SORT' => '100',
            'FIRST_SITE_ID' => NULL,
            'IMAGE_ID' => NULL,
            'USE_CAPTCHA' => 'N',
            'DESCRIPTION' => '',
            'DESCRIPTION_TYPE' => 'text',
            'FORM_TEMPLATE' => '',
            'USE_DEFAULT_TEMPLATE' => 'Y',
            'SHOW_TEMPLATE' => NULL,
            'MAIL_EVENT_TYPE' => 'FORM_FILLING_SIMPLE_FORM_2',
            'SHOW_RESULT_TEMPLATE' => NULL,
            'PRINT_RESULT_TEMPLATE' => NULL,
            'EDIT_RESULT_TEMPLATE' => NULL,
            'FILTER_RESULT_TEMPLATE' => NULL,
            'TABLE_RESULT_TEMPLATE' => NULL,
            'USE_RESTRICTIONS' => 'N',
            'RESTRICT_USER' => '0',
            'RESTRICT_TIME' => '0',
            'RESTRICT_STATUS' => '',
            'STAT_EVENT1' => 'form',
            'STAT_EVENT2' => '',
            'STAT_EVENT3' => '',
            'LID' => NULL,
            'C_FIELDS' => '0',
            'QUESTIONS' => '1',
            'STATUSES' => '1',
            'arSITE' =>
                array(
                    0 => 's1',
                ),
            'arMENU' =>
                array(
                    'ru' => 'Сообщения об ошибках в тексте',
                    'en' => 'Error messages in the text',
                ),
            'arGROUP' =>
                array(),
            'arMAIL_TEMPLATE' =>
                array(),
        ));
        $formHelper->saveStatuses($formId, array(
            0 =>
                array(
                    'CSS' => 'statusgreen',
                    'C_SORT' => '100',
                    'ACTIVE' => 'Y',
                    'TITLE' => 'DEFAULT',
                    'DESCRIPTION' => 'DEFAULT',
                    'DEFAULT_VALUE' => 'Y',
                    'HANDLER_OUT' => NULL,
                    'HANDLER_IN' => NULL,
                ),
        ));
        $formHelper->saveFields($formId, array(
            0 =>
                array(
                    'ACTIVE' => 'Y',
                    'TITLE' => 'Текст с ошибкой',
                    'TITLE_TYPE' => 'text',
                    'SID' => 'ERROR_TEXT_QUESTION',
                    'C_SORT' => '100',
                    'ADDITIONAL' => 'N',
                    'REQUIRED' => 'Y',
                    'IN_FILTER' => 'Y',
                    'IN_RESULTS_TABLE' => 'Y',
                    'IN_EXCEL_TABLE' => 'Y',
                    'FIELD_TYPE' => 'text',
                    'IMAGE_ID' => NULL,
                    'COMMENTS' => '',
                    'FILTER_TITLE' => 'Текст с ошибкой',
                    'RESULTS_TABLE_TITLE' => 'Текст с ошибкой',
                    'ANSWERS' =>
                        array(
                            0 =>
                                array(
                                    'MESSAGE' => ' ',
                                    'VALUE' => '',
                                    'FIELD_TYPE' => 'textarea',
                                    'FIELD_WIDTH' => '0',
                                    'FIELD_HEIGHT' => '0',
                                    'FIELD_PARAM' => '',
                                    'C_SORT' => '0',
                                    'ACTIVE' => 'Y',
                                ),
                        ),
                    'VALIDATORS' =>
                        array(
                            0 =>
                                array(
                                    'ACTIVE' => 'Y',
                                    'C_SORT' => '100',
                                    'PARAMS' =>
                                        array(
                                            'LENGTH_FROM' => 1,
                                            'LENGTH_TO' => 1000,
                                        ),
                                    'NAME' => 'text_len',
                                ),
                        ),
                ),
            1 =>
                array(
                    'ACTIVE' => 'Y',
                    'TITLE' => 'Адрес',
                    'TITLE_TYPE' => 'text',
                    'SID' => 'URL_QUESTION',
                    'C_SORT' => '200',
                    'ADDITIONAL' => 'N',
                    'REQUIRED' => 'Y',
                    'IN_FILTER' => 'Y',
                    'IN_RESULTS_TABLE' => 'Y',
                    'IN_EXCEL_TABLE' => 'Y',
                    'FIELD_TYPE' => 'text',
                    'IMAGE_ID' => NULL,
                    'COMMENTS' => '',
                    'FILTER_TITLE' => 'Адрес',
                    'RESULTS_TABLE_TITLE' => 'Адрес',
                    'ANSWERS' =>
                        array(
                            0 =>
                                array(
                                    'MESSAGE' => ' ',
                                    'VALUE' => '',
                                    'FIELD_TYPE' => 'text',
                                    'FIELD_WIDTH' => '0',
                                    'FIELD_HEIGHT' => '0',
                                    'FIELD_PARAM' => '',
                                    'C_SORT' => '0',
                                    'ACTIVE' => 'Y',
                                ),
                        ),
                    'VALIDATORS' =>
                        array(),
                ),
        ));
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $formHelper = $helper->Form();
        $formHelper->deleteFormIfExists(self::SID);
    }
}

