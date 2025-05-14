<?php

/**
 * Ps_Admin_Panel_Legacy
 *
 * This module adds an admin panel to the back office, allowing easy configuration of different types of fields.
 *
 * @author    @jjpeleato
 * @link      https://www.jjpeleato.com/
 * @license   GPL-3.0-only https://opensource.org/license/gpl-3-0/
 * @version   0.1.0
 * @since     0.1.0
 * @see       https://devdocs.prestashop-project.org/1.7/modules/creation/adding-configuration-page/
 * @see       https://devdocs.prestashop-project.org/8/modules/creation/adding-configuration-page/
 */

declare(strict_types=1);

// phpcs:disable
/**
 * If this file is called directly, then abort execution.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
// phpcs:enable

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

/**
 * Class Ps_Admin_Panel_Legacy
 *
 * @since 0.1.0
 * @author @jjpeleato
 */
//phpcs:ignore
class Ps_Admin_Panel_Legacy extends Module implements WidgetInterface
{
    /** @var array $shops */
    private array $shops = [];

    /** @var array $languages */
    private array $languages = [];

    /** @var string $domain */
    private string $domain = 'Modules.Psadminpanellegacy.Admin';

    /** @var array $fields */
    private array $fields = [];

    /**
     * Ps_Admin_Panel_Legacy constructor.
     */
    public function __construct()
    {
        $this->shops = Shop::getShops();
        $this->languages = Language::getLanguages(false);
        $this->name = 'ps_admin_panel_legacy';
        $this->tab = 'front_office_features';
        $this->version = '0.1.0';
        $this->author = '@jjpeleato';
        $this->author_uri = 'https://www.jjpeleato.com/';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.8.0',
            'max' => '8.2.1'
        ];
        $this->bootstrap = true;
        $this->displayName = $this->trans('PrestaShop Admin Panel', [], $this->domain);
        $this->description = $this->trans(
            'PrestaShop module that adds an admin panel to the back office, allowing easy configuration of different types of fields.',
            [],
            $this->domain
        );
        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], $this->domain);

        // Set the module's configuration fields.
        $this->fields = [
            'PS_ADMIN_PANEL_LEGACY_TITLE' => [
                'machine_name' => 'title',
                'type' => 'text',
                'lang' => true,
                'required' => true,
                'label' => $this->trans('Title', [], $this->domain),
                'desc' => $this->trans('Write a title for the section.', [], $this->domain),
                'value' => '',
            ],
            'PS_ADMIN_PANEL_LEGACY_SHORT_DESCRIPTION' => [
                'machine_name' => 'short_description',
                'type' => 'html',
                'lang' => true,
                'required' => false,
                'label' => $this->trans('Short description', [], $this->domain),
                'desc' => $this->trans('Write a short description for the section.', [], $this->domain),
                'value' => '',
            ],
            'PS_ADMIN_PANEL_LEGACY_DESCRIPTION' => [
                'machine_name' => 'description',
                'type' => 'html',
                'lang' => true,
                'required' => false,
                'label' => $this->trans('Description', [], $this->domain),
                'desc' => $this->trans('Write a description for the section.', [], $this->domain),
                'value' => '',
            ],
            'PS_ADMIN_PANEL_LEGACY_IMAGE' => [
                'machine_name' => 'image',
                'type' => 'image',
                'lang' => true,
                'required' => false,
                'label' => $this->trans('Image', [], $this->domain),
                'desc' => $this->trans('Upload an image for the section.', [], $this->domain),
                'value' => '',
            ],
        ];

        parent::__construct();
    }

    /**
     * This method is used to activate the translation system for the module.
     *
     * @return bool
     */
    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /**
     * Install the module.
     *
     * This method is called when the module is installed.
     *
     * @return bool
     */
    public function install(): bool
    {
        return parent::install() &&
            $this->installShopFixtures();
    }

    /**
     * This method is used to install the module fixtures.
     * It is called when the module is installed.
     *
     * @return bool
     */
    private function installShopFixtures(): bool
    {
        foreach ($this->shops as $shop) {
            $idShopGroup = (int) $shop['id_shop_group'];
            $idShop = (int) $shop['id_shop'];

            foreach ($this->languages as $lang) {
                $idLang = (int) $lang['id_lang'];

                if (!$this->installLanguageFixture($idLang, $idShopGroup, $idShop)) {
                    return false;
                }
            }
        }

        PrestaShopLogger::addLog("Installed module: $this->name", 1);

        return true;
    }

    /**
     * This method is used to install a fixture for the module.
     * It is called when the module is installed.
     *
     * @param int $idLang
     * @param int $idShopGroup
     * @param int $idShop
     */
    private function installLanguageFixture(int $idLang = 0, int $idShopGroup = 0, int $idShop = 0): bool
    {
        foreach ($this->fields as $key => $field) {
            if (!Configuration::updateValue($key, [$idLang => $field['value']], false, $idShopGroup, $idShop)) {
                PrestaShopLogger::addLog(
                    "Failed to update configuration key: $key for shop $idShop and language $idLang",
                    3
                );

                return false;
            }
        }

        return true;
    }

    /**
     * Uninstall the module.
     *
     * This method is called when the module is uninstalled.
     * It is used to remove the module from the database and clean up any resources.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        $this->_clearCache('*'); // Clear module cache

        foreach (array_keys($this->fields) as $field) {
            if (!Configuration::deleteByName($field)) {
                return false; // Stop if any deletion fails
            }
        }

        // Delete all images in the images folder.
        $this->deleteImages();

        PrestaShopLogger::addLog("Uninstalled module: $this->name", 1);

        return parent::uninstall();
    }

    /**
     * This method is used to get the content of the module.
     * It is called when the module is displayed in the back office.
     *
     * @return string
     */
    public function getContent(): string
    {
        /**
         * If the multi-store is active, check if the store is selected
         * and display an error message if not.
         */
        $isMultistore = Shop::isFeatureActive();
        if ($isMultistore === true) {
            $idShop = (int) Shop::getContextShopID();

            if ($idShop === 0) {
                return $this->displayInformation(
                    $this->trans('You must select a store.', [], $this->domain)
                );
            }
        }

        /**
         * If submitted, the save process is triggered.
         */
        $output = '';
        if (Tools::isSubmit('submit_' . $this->name)) {
            $output = $this->postProcess();
        }

        /**
         * Render all templates.
         */
        $renderTemplate = $this->context->smarty->fetch('module:ps_admin_panel_legacy/views/templates/admin/index.tpl');

        return $output . $this->renderForm() . $renderTemplate;
    }

    /**
     * This method is used to render the form.
     * It is called when the module is displayed in the back office.
     *
     * @see https://devdocs.prestashop-project.org/8/development/components/helpers/helperform/#attributes
     *
     * @return string
     */
    private function renderForm()
    {
        $helper = new HelperForm();

        // Module, table, name_controller, token and currentIndex.
        $helper->module = $this;
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);

        // Default language and languages.
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->languages = $this->context->controller->getLanguages();

        // Submit and identifier.
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit_' . $this->name;

        // Load current value into the form.
        $helper->tpl_vars = [
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
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
    private function getConfigForm(): array
    {
        $form = [
            'form' => [
                'tinymce' => true,
                'legend' => [
                    'title' => $this->trans('Settings', [], $this->domain),
                    'icon' => 'icon-cogs'
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], $this->domain),
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
    private function getConfigFieldsValues(): array
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
     * @return string
     */
    private function postProcess(): string
    {
        $values = [];
        $errors = [];

        foreach ($this->fields as $key => $field) {
            foreach ($this->languages as $lang) {
                if ($field['type'] === 'image') {
                    $action = $this->uploadImage($key, (int) $lang['id_lang']);
                    $values[$key][$lang['id_lang']] = $action;

                    if ($action === '') {
                        $errors[] = $this->trans(
                            'An error occurred while attempting to upload the file.',
                            [],
                            $this->domain
                        );
                    }
                } else {
                    $values[$key][$lang['id_lang']] = Tools::getValue($key . '_' . $lang['id_lang']);
                }
            }

            if (!Configuration::updateValue($key, $values[$key], true)) {
                $errors[] = $this->trans(
                    'Failed to update configuration for key "%key%".',
                    [
                        '%key%' => $key
                    ],
                    $this->domain
                );
            }
        }

        // Clear the cache after updating the configuration.
        $this->_clearCache('*');

        // If there are errors, display them.
        if (!empty($errors)) {
            foreach ($errors as $error) {
                PrestaShopLogger::addLog($error, 3);
            }

            return $this->displayError(
                $this->trans('Some settings could not be updated. Please check the logs for more details.', [], $this->domain)
            );
        }

        // If everything is successful, display a success message.
        return $this->displayConfirmation(
            $this->trans('The settings have been updated.', [], $this->domain)
        );
    }

    /**
     * This method is used to upload an image.
     * It is called when the form is submitted.
     *
     * @param string $key
     * @param int $lang
     *
     * @return string
     */
    private function uploadImage(string $key = '', int $lang = 0): string
    {
        if (
            isset($_FILES[$key . '_' . $lang])
            && isset($_FILES[$key . '_' . $lang]['tmp_name'])
            && !empty($_FILES[$key . '_' . $lang]['tmp_name'])
        ) {
            if ($error = ImageManager::validateUpload($_FILES[$key . '_' . $lang], 4000000)) {
                PrestaShopLogger::addLog($error, 3);
                return '';
            }

            $ext = substr(
                $_FILES[$key . '_' . $lang]['name'],
                strrpos($_FILES[$key . '_' . $lang]['name'], '.') + 1
            );
            $file = md5($_FILES[$key . '_' . $lang]['name']) . '.' . $ext;

            if (
                false === move_uploaded_file(
                    $_FILES[$key . '_' . $lang]['tmp_name'],
                    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $file
                )
            ) {
                PrestaShopLogger::addLog(
                    $this->trans('An error occurred while attempting to upload the file.', [], $this->domain),
                    3
                );
                return '';
            }

            // Delete old image.
            if (
                Configuration::hasContext($key, $lang, Shop::getContext())
                && Configuration::get($key, $lang) != $file
            ) {
                @unlink(
                    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . Configuration::get(
                        $key,
                        $lang
                    )
                );
            }

            return $file;
        }

        return '';
    }

    /**
     * This method is used to delete all images in the images folder.
     * It is called when the module is uninstalled.
     *
     * @return void
     */
    private function deleteImages(): void
    {
        $directory = dirname(__FILE__) . '/images/';
        if (!is_dir($directory)) {
            return;
        }

        $images = glob($directory . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        if ($images) {
            array_map('unlink', $images);
        }
    }

    /**
     * Implement the renderWidget method.
     *
     * This method is used to render the widget.
     * It is called when the widget is displayed.
     *
     * @param string $hookName
     * @param array $configuration
     * @return string
     */
    public function renderWidget($hookName, array $configuration): string
    {
        $variables = $this->getWidgetVariables($hookName, $configuration);
        if (true === empty($variables)) {
            return '';
        }

        $this->context->smarty->assign($variables);
        $this->smarty->assign([
            'path' => $this->_path,
        ]);
        return $this->fetch('module:ps_admin_panel_legacy/views/templates/widget/index.tpl');
    }

    /**
     * Implement the getWidgetVariables method.
     *
     * This method is used to pass variables to the template.
     * It is called when the widget is rendered.
     *
     * @param string $hookName
     * @param array $configuration
     * @return array
     */
    public function getWidgetVariables($hookName, array $configuration): array
    {
        $variables = [];
        $idLang = $this->context->language->id;

        foreach ($this->fields as $key => $field) {
            $variables[$field['machine_name']] = Configuration::get($key, $idLang);
        }

        return $variables;
    }
}
