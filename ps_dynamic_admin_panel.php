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

// phpcs:disable
if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php') === true) {
    require_once __DIR__ . '/vendor/autoload.php';
}
// phpcs:enable

use PrestaShop\Module\PsDynamicAdminPanel\Helper\Includes\SettingsValidator;
use PrestaShop\Module\PsDynamicAdminPanel\Native\Classes\HelperFormExtended;
use PrestaShop\Module\PsDynamicAdminPanel\Native\Classes\Installer;
use PrestaShop\Module\PsDynamicAdminPanel\Native\Classes\TabInstaller;
use PrestaShop\Module\PsDynamicAdminPanel\Native\Classes\Uninstaller;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

/**
 * Ps_Dynamic_Admin_Panel
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
//phpcs:ignore
class Ps_Dynamic_Admin_Panel extends Module implements WidgetInterface
{
    /** @var array */
    private array $shops = [];

    /** @var array */
    private array $languages = [];

    /** @var array */
    private array $fields = [];

    /** @var TabInstaller */
    private TabInstaller $tabInstaller;

    /** @var Installer */
    private Installer $installer;

    /** @var Uninstaller */
    private Uninstaller $uninstaller;

    /** @var SettingsValidator */
    private SettingsValidator $settingsValidator;

    /** @var HelperFormExtended */
    private HelperFormExtended $helperFormExtended;

    /**
     * Ps_Dynamic_Admin_Panel constructor.
     */
    public function __construct()
    {
        $this->name = 'ps_dynamic_admin_panel';
        $this->tab = 'front_office_features';
        $this->version = '0.1.0';
        $this->author = '@jjpeleato';
        $this->author_uri = 'https://www.jjpeleato.com/';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;
        $this->displayName = $this->trans('PrestaShop: Dynamic Admin Panel', [], 'Modules.Psdynamicadminpanel.Admin');
        $this->description = $this->trans(
            'PrestaShop module that adds an admin panel to the back office, allowing easy configuration of different types of fields.',
            [],
            'Modules.Psdynamicadminpanel.Admin'
        );
        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Psdynamicadminpanel.Admin');

        // Set the module's configuration.
        $this->shops = Shop::getShops();
        $this->languages = Language::getLanguages(false);
        $this->fields = PS_DYNAMIC_ADMIN_PANEL_FIELDS;

        // Initialize the tab installer.
        $this->tabInstaller = new TabInstaller($this->name);

        // Initialize the installer.
        $this->installer = new Installer($this->shops, $this->languages, $this->fields);

        // Initialize the uninstaller.
        $this->uninstaller = new Uninstaller($this->fields);

        // Initialize the settings validator.
        $this->settingsValidator = new SettingsValidator($this->fields);

        // Initialize the helper form extended.
        $this->helperFormExtended = new HelperFormExtended($this->languages, $this->fields);

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
        PrestaShopLogger::addLog("Installed module: $this->name", 1);

        return parent::install() &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->tabInstaller->installTab() &&
            $this->installer->installShopFixtures();
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
        PrestaShopLogger::addLog("Uninstalled module: $this->name", 1);

        $this->_clearCache('*'); // Clear module cache

        return parent::uninstall() &&
            $this->unregisterHook('actionAdminControllerSetMedia') &&
            $this->tabInstaller->uninstallTab() &&
            $this->uninstaller->uninstall();
    }

    /**
     * This method is used to add JavaScript files to the module.
     * It is called when the module is displayed in the back office.
     *
     * @return void
     */
    public function hookActionAdminControllerSetMedia(): void
    {
        $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/custom.js');
    }

    /**
     * This method is used to add JavaScript definitions to the page.
     * It is called when the module is displayed in the back office.
     *
     * @return void
     */
    private function addJsDefList()
    {
        Media::addJsDef([
            'psapl_controller_delete_url' => $this->context->link->getAdminLink(PS_DYNAMIC_ADMIN_PANEL_NAME),
            'psapl_controller_delete' => PS_DYNAMIC_ADMIN_PANEL_NAME,
        ]);
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
                    $this->trans('You must select a store.', [], 'Modules.Psdynamicadminpanel.Admin')
                );
            }
        }

        $validation = $this->settingsValidator->validate();
        if (empty($validation) === false) {
            return $this->displayError($validation);
        }

        $this->addJsDefList();

        /**
         * If submitted, the save process is triggered.
         */
        $output = '';
        if (Tools::isSubmit('submit_' . $this->name)) {
            $errors = $this->helperFormExtended->postProcess();

            if (empty($errors) === true) {
                $output = $this->displayConfirmation(
                    $this->trans('The settings have been updated.', [], 'Modules.Psdynamicadminpanel.Admin')
                );
            } else {
                // If there are errors, display them.
                foreach ($errors as $error) {
                    $output .= $this->displayError($error);
                }
            }

            // Clear the cache after updating the configuration.
            $this->_clearCache('*');
        }

        /**
         * Render all templates.
         */
        $renderForm = $this->helperFormExtended->renderForm($this, $this->table, $this->name, $this->identifier, $this->getPathUri());
        $renderTemplate = $this->context->smarty->fetch('module:' . $this->name . '/views/templates/admin/index.tpl');

        return $output . $renderForm . $renderTemplate;
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
        if (empty($variables) === true) {
            return '';
        }

        $this->context->smarty->assign($variables);
        $this->smarty->assign([
            'path' => $this->_path,
        ]);
        return $this->fetch('module:' . $this->name . '/views/templates/front/index.tpl');
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
            $variables[$field['machine_name']] = Configuration::get($key, $field['lang'] ? $idLang : null);
        }

        return $variables;
    }
}
