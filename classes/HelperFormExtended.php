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

namespace PrestaShop\Module\PsAdminPanelLegacy\Native\Classes;

// phpcs:disable
/**
 * If this file is called directly, then abort execution.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
// phpcs:enable

use AdminController;
use Configuration;
use Context;
use HelperForm;
use PrestaShopLogger;
use Tools;

/**
 * HelperFormExtended class for managing the installation and uninstallation of tabs in the back office.
 *
 * This class provides methods to install and uninstall a tab in the back office of PrestaShop.
 * It is used to manage the AdminPanelLegacy tab.
 */
class HelperFormExtended
{
    /** @var Translator $translator */
    private $translator;

    /** @var array $languages */
    private array $languages = [];

    /** @var array $fields */
    private array $fields = [];

    /** @var ImageHandler $imageHandler */
    private ImageHandler $imageHandler;

    /**
     * HelperFormExtended constructor.
     * This constructor is used to initialize the HelperFormExtended class.
     * It does not take any parameters and does not perform any actions.
     */
    public function __construct($languages = [], $fields = [])
    {
        $this->translator = Context::getContext()->getTranslator();
        $this->languages = $languages;
        $this->fields = $fields;

        // Initialize the image handler.
        $this->imageHandler = new ImageHandler(PS_ADMIN_PANEL_LEGACY_UPLOAD_DIR);
    }

    /**
     * This method is used to render the form.
     * It is called when the module is displayed in the back office.
     *
     * @see https://devdocs.prestashop-project.org/8/development/components/helpers/helperform/#attributes
     *
     * @return string
     */
    public function renderForm($module = null, $table = '', $name = '', $identifier = '', $pathUri = ''): string
    {
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
                    'title' => $this->translator->trans('Settings', [], PS_ADMIN_PANEL_LEGACY_DOMAIN),
                    'icon' => 'icon-cogs'
                ],
                'submit' => [
                    'title' => $this->translator->trans('Save', [], PS_ADMIN_PANEL_LEGACY_DOMAIN),
                ],
            ],
        ];

        // Add fields to the form.
        foreach ($this->fields as $key => $field) {
            $form['form']['input'][$field['machine_name']] = [
                'type' => $field['type'],
                'lang' => $field['lang'],
                'required' => $field['required'],
                'label' => $field['label'],
                'name' => $key,
                'desc' => $field['desc'],
            ];

            if (false === empty($field['tab'])) {
                $form['form']['tabs'][$field['tab']] = $field['tab'];
                $form['form']['input'][$field['machine_name']]['tab'] = $field['tab'];
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

        foreach ($this->languages as $lang) {
            $idLang = (int) $lang['id_lang'];

            foreach (array_keys($this->fields) as $key) {
                $fields[$key][$idLang] = Tools::getValue(
                    $key . '_' . $idLang,
                    Configuration::get($key, $idLang)
                );
            }
        }

        return $fields;
    }

    /**
     * This method is used to process the form submission.
     * It is called when the form is submitted.
     *
     * @return bool
     */
    public function postProcess(): bool
    {
        $values = [];
        $errors = [];

        foreach ($this->fields as $key => $field) {
            foreach ($this->languages as $lang) {
                if ($field['type'] === 'image') {
                    $uploaded = $this->imageHandler->uploadImage($_FILES, $key, (int) $lang['id_lang']);

                    if (true === $uploaded['success']) {
                        $uploaded = $uploaded['filename'];
                        $values[$key][$lang['id_lang']] = $uploaded;
                    } else {
                        $errors[] = $uploaded['error'];
                    }
                } else {
                    $values[$key][$lang['id_lang']] = Tools::getValue($key . '_' . $lang['id_lang']);
                }
            }

            if (!Configuration::updateValue($key, $values[$key], true)) {
                $errors[] = $this->translator->trans(
                    'Failed to update configuration for key "%key%".',
                    [
                        '%key%' => $key
                    ],
                    PS_ADMIN_PANEL_LEGACY_DOMAIN
                );
            }
        }

        // If there are errors, display them.
        if (!empty($errors)) {
            foreach ($errors as $error) {
                PrestaShopLogger::addLog($error, 3);
            }

            return false;
        }

        return true;
    }
}
