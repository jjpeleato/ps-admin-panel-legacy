<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
declare(strict_types=1);

namespace PrestaShop\Module\PsDynamicAdminPanel\Native\Classes;

use AdminController;
use Configuration;
use Context;
use HelperForm;
use Module;
use PrestaShopBundle\Translation\TranslatorComponent as Translator;
use Tools;

use const PS_DYNAMIC_ADMIN_PANEL_UPLOAD_DIR;

// phpcs:disable
if (!defined('_PS_VERSION_')) {
    exit;
}
// phpcs:enable

/**
 * Class HelperFormExtended
 *
 * This class extends the functionality of the HelperForm class
 * to provide additional features for the dynamic admin panel.
 */
class HelperFormExtended
{
    /** @var Translator */
    private $translator;

    /** @var array */
    private array $languages = [];

    /** @var array */
    private array $fields = [];

    /** @var ImageHandler */
    private ImageHandler $imageHandler;

    /** @var VideoHandler */
    private VideoHandler $videoHandler;

    /**
     * HelperFormExtended constructor.
     *
     * This constructor is used to initialize the HelperFormExtended class.
     *
     * @param array $languages
     * @param array $fields
     */
    public function __construct(array $languages = [], array $fields = [])
    {
        $this->translator = Context::getContext()->getTranslator();
        $this->languages = $languages;
        $this->fields = $fields;

        // Initialize the image handler.
        $this->imageHandler = new ImageHandler(PS_DYNAMIC_ADMIN_PANEL_UPLOAD_DIR);

        // Initialize the video handler.
        $this->videoHandler = new VideoHandler(PS_DYNAMIC_ADMIN_PANEL_UPLOAD_DIR);
    }

    /**
     * This method is used to render the form.
     * It is called when the module is displayed in the back office.
     *
     * @see https://devdocs.prestashop-project.org/8/development/components/helpers/helperform/#attributes
     *
     * @param Module|null $module
     * @param string $table
     * @param string $name
     * @param string $identifier
     * @param string $pathUri
     * @return string
     */
    public function renderForm(
        $module = null,
        string $table = '',
        string $name = '',
        string $identifier = '',
        string $pathUri = ''
    ): string {
        $helper = new HelperForm();

        // Module, table, name_controller, token and currentIndex.
        $helper->module = $module;
        $helper->table = $table;
        $helper->name_controller = $name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $name]);

        // Default language and languages.
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->languages = Context::getContext()->controller->getLanguages();

        // Submit and identifier.
        $helper->identifier = $identifier;
        $helper->submit_action = 'submit_' . $name;

        // Load current value into the form.
        $helper->tpl_vars = [
            'uri' => $pathUri,
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => Context::getContext()->controller->getLanguages(),
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Get configuration form.
     *
     * @see https://devdocs.prestashop-project.org/8/development/components/helpers/helperform/#basic-usage
     *
     * @return array
     */
    public function getConfigForm(): array
    {
        $form = [
            'form' => [
                'tinymce' => true,
                'legend' => [
                    'title' => $this->translator->trans('Settings', [], 'Modules.Psdynamicadminpanel.Admin'),
                    'icon' => 'icon-cogs',
                ],
                'submit' => [
                    'title' => $this->translator->trans('Save', [], 'Modules.Psdynamicadminpanel.Admin'),
                ],
            ],
        ];

        // Add fields to the form.
        foreach ($this->fields as $key => $field) {
            $form['form']['input'][$field['machine_name']] = [
                'type' => 'text',
                'lang' => is_bool($field['lang']) ? $field['lang'] : false,
                'required' => is_bool($field['required']) ? $field['required'] : false,
                'label' => $field['label'],
                'name' => $key,
                'desc' => $field['desc'],
            ];

            if (empty($field['tab']) === false) {
                $form['form']['tabs'][$field['tab']] = $field['tab'];
                $form['form']['input'][$field['machine_name']]['tab'] = $field['tab'];
            }

            if ($field['type'] === 'switch') {
                $form['form']['input'][$field['machine_name']]['type'] = 'switch';
                $form['form']['input'][$field['machine_name']]['lang'] = false;
                $form['form']['input'][$field['machine_name']]['required'] = false;
                $form['form']['input'][$field['machine_name']]['class'] = 't';
                $form['form']['input'][$field['machine_name']]['is_bool'] = true;
                $form['form']['input'][$field['machine_name']]['values'] = [
                    [
                        'id' => $field['machine_name'] . '_on',
                        'value' => 1,
                        'label' => $this->translator->trans('Enabled', [], 'Modules.Psdynamicadminpanel.Admin'),
                    ],
                    [
                        'id' => $field['machine_name'] . '_off',
                        'value' => 0,
                        'label' => $this->translator->trans('Disabled', [], 'Modules.Psdynamicadminpanel.Admin'),
                    ],
                ];
            }

            if ($field['type'] === 'html') {
                $form['form']['input'][$field['machine_name']]['type'] = 'textarea';
                $form['form']['input'][$field['machine_name']]['autoload_rte'] = true;
                $form['form']['input'][$field['machine_name']]['cols'] = 75;
                $form['form']['input'][$field['machine_name']]['rows'] = 75;
                $form['form']['input'][$field['machine_name']]['class'] = 'rte';
                $form['form']['input'][$field['machine_name']]['autoload_rte'] = true;
            }

            if ($field['type'] === 'image') {
                $form['form']['input'][$field['machine_name']]['type'] = 'image_lang';
                $form['form']['input'][$field['machine_name']]['lang'] = true;
            }

            if ($field['type'] === 'video') {
                $form['form']['input'][$field['machine_name']]['type'] = 'video_lang';
                $form['form']['input'][$field['machine_name']]['lang'] = true;
            }
        }

        return $form;
    }

    /**
     * Get configuration fields values.
     *
     * @return array
     */
    public function getConfigFieldsValues(): array
    {
        $fields = [];

        foreach ($this->fields as $key => $field) {
            if ($field['lang'] === false) {
                $fields[$key] = Configuration::get($key, null);
                continue;
            }

            foreach ($this->languages as $lang) {
                $idLang = (int) $lang['id_lang'];
                $fields[$key][$idLang] = Configuration::get($key, $idLang);
            }
        }

        return $fields;
    }

    /**
     * This method is used to process the form submission.
     * It is called when the form is submitted.
     *
     * @return array
     */
    public function postProcess(): array
    {
        $errors = [];

        foreach ($this->fields as $key => $field) {
            $saveResult = $field['lang'] === true
                ? $this->processMultiLanguageField($key, $field)
                : $this->processSingleLanguageField($key, $field);

            $errors = array_merge($errors, $saveResult);
        }

        // Flatten the errors array.
        return array_filter($errors, function ($error) {
            return !empty($error);
        });
    }

    /**
     * Save field with language support.
     *
     * @param string $key
     * @param array $field
     * @return array
     */
    private function processMultiLanguageField(string $key = '', array $field = []): array
    {
        $values = [];
        $errors = [];

        foreach ($this->languages as $lang) {
            $idLang = (int) $lang['id_lang'];
            $localeLang = $lang['locale'];

            if ($field['type'] === 'image' || $field['type'] === 'video') {
                $uploaded = $field['type'] === 'video'
                    ? $this->videoHandler->uploadMedia($_FILES, $key, $idLang)
                    : $this->imageHandler->uploadMedia($_FILES, $key, $idLang);

                if ($uploaded['success'] === false) {
                    $errors[] = $uploaded['error'];
                    continue;
                }

                $value = $uploaded['filename'];
            } else {
                $value = Tools::getValue($key . '_' . $idLang);

                if ($field['required'] === true && empty($value) === true) {
                    $errors[] = $this->translator->trans(
                        'The "%field%" field for language "%lang%" is required.',
                        [
                            '%field%' => $field['label'],
                            '%lang%' => $localeLang,
                        ],
                        'Modules.Psdynamicadminpanel.Admin'
                    );
                    continue;
                }
            }

            $values[$key][$idLang] = $value;

            if (!Configuration::updateValue($key, $values[$key], true)) {
                $errors[] = $this->translator->trans(
                    'Failed to update configuration for key "%key%".',
                    [
                        '%key%' => $key,
                    ],
                    'Modules.Psdynamicadminpanel.Admin'
                );
            }
        }

        return $errors;
    }

    /**
     * Save field without language support.
     *
     * @param string $key
     * @param array $field
     * @return array
     */
    private function processSingleLanguageField(string $key = '', array $field = []): array
    {
        $value = Tools::getValue($key);

        if ($field['required'] === true && empty($value) === true) {
            return [
                $this->translator->trans(
                    'The "%field%" field is required.',
                    [
                        '%field%' => $field['label'],
                    ],
                    'Modules.Psdynamicadminpanel.Admin'
                ),
            ];
        }

        if (!Configuration::updateValue($key, $value, true)) {
            return [
                $this->translator->trans(
                    'Failed to update configuration for key "%key%".',
                    [
                        '%key%' => $key,
                    ],
                    'Modules.Psdynamicadminpanel.Admin'
                )
            ];
        }

        return [];
    }
}
