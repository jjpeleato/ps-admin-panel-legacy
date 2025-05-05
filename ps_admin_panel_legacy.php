<?php

declare(strict_types=1);

/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

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
    /** @var string $domain */
    private string $domain = 'Modules.Psadminpanellegacy.Admin';

    /**
     * Ps_Admin_Panel_Legacy constructor.
     */
    public function __construct()
    {
        $this->name = 'ps_admin_panel_legacy';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
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
        return parent::install();
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
        return parent::uninstall();
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
        return '';
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
        return [];
    }
}
