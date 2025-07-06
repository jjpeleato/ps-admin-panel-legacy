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

if (true === file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
// phpcs:enable

use PrestaShop\Module\PsAdminPanelLegacy\Native\Classes\HelperFormExtended;
use PrestaShop\Module\PsAdminPanelLegacy\Native\Classes\Installer;
use PrestaShop\Module\PsAdminPanelLegacy\Native\Classes\TabInstaller;
use PrestaShop\Module\PsAdminPanelLegacy\Native\Classes\Uninstaller;
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

    /** @var array $fields */
    private array $fields = [];

    /** @var TabInstaller $tabInstaller */
    private TabInstaller $tabInstaller;

    /** @var Installer $installer */
    private Installer $installer;

    /** @var Uninstaller $uninstaller */
    private Uninstaller $uninstaller;

    /** @var HelperFormExtended $helperFormExtended */
    private HelperFormExtended $helperFormExtended;

    /**
     * Ps_Admin_Panel_Legacy constructor.
     */
    public function __construct()
    {
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
        $this->displayName = $this->trans('PrestaShop Admin Panel', [], PS_ADMIN_PANEL_LEGACY_DOMAIN);
        $this->description = $this->trans(
            'PrestaShop module that adds an admin panel to the back office, allowing easy configuration of different types of fields.',
            [],
            PS_ADMIN_PANEL_LEGACY_DOMAIN
        );
        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], PS_ADMIN_PANEL_LEGACY_DOMAIN);

        // Set the module's configuration.
        $this->shops = Shop::getShops();
        $this->languages = Language::getLanguages(false);
        $this->fields = PS_ADMIN_PANEL_LEGACY_FIELDS;

        // Initialize the tab installer.
        $this->tabInstaller = new TabInstaller($this->name);

        // Initialize the installer.
        $this->installer = new Installer($this->shops, $this->languages, $this->fields);

        // Initialize the uninstaller.
        $this->uninstaller = new Uninstaller($this->fields);

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
                    $this->trans('You must select a store.', [], PS_ADMIN_PANEL_LEGACY_DOMAIN)
                );
            }
        }

        $this->addJsDefList();

        /**
         * If submitted, the save process is triggered.
         */
        $output = '';
        if (Tools::isSubmit('submit_' . $this->name)) {
            $postProcess = $this->helperFormExtended->postProcess();

            if ($postProcess === true) {
                $output = $this->displayConfirmation(
                    $this->trans('The settings have been updated.', [], PS_ADMIN_PANEL_LEGACY_DOMAIN)
                );
            } else {
                $output = $this->displayError(
                    $this->trans(
                        'Some settings could not be updated. Please check the logs for more details: Advanced Parameters > Logs.',
                        [],
                        PS_ADMIN_PANEL_LEGACY_DOMAIN
                    )
                );
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
     * This method is used to add JavaScript definitions to the page.
     * It is called when the module is displayed in the back office.
     *
     * @return void
     */
    private function addJsDefList()
    {
        Media::addJsDef([
            'psapl_controller_delete_url' => $this->context->link->getAdminLink(PS_ADMIN_PANEL_LEGACY_NAME),
            'psapl_controller_delete' => PS_ADMIN_PANEL_LEGACY_NAME,
        ]);
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
        return $this->fetch('module:' . $this->name . '/views/templates/widget/index.tpl');
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
