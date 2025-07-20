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

use Language;
use Tab;

use const PS_DYNAMIC_ADMIN_PANEL_NAME;

/**
 * Class TabInstaller
 *
 * This class is responsible for installing and uninstalling a tab in the back office.
 */
class TabInstaller
{
    /** @var string */
    private string $name = '';

    /**
     * TabInstaller constructor.
     *
     * @param string $name
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
    }

    /**
     * This method is used to install the tab in the back office.
     * It is called when the module is installed.
     *
     * @return bool Returns true if the tab was successfully installed, false otherwise
     */
    public function installTab(): bool
    {
        $tab = new Tab();
        $tab->active = true;
        $tab->class_name = PS_DYNAMIC_ADMIN_PANEL_NAME;
        $tab->id_parent = -1;
        $tab->module = $this->name;

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->name;
        }

        return $tab->add();
    }

    /**
     * This method is used to uninstall the tab in the back office.
     * It is called when the module is uninstalled.
     *
     * @return bool returns true if the tab was successfully uninstalled, false otherwise
     */
    public function uninstallTab(): bool
    {
        // PrestaShopBundle\Entity\Repository\TabRepository::findOneIdByClassName($className) is not a static method.
        $id_tab = Tab::getIdFromClassName(PS_DYNAMIC_ADMIN_PANEL_NAME);
        $tab = new Tab($id_tab);

        return $tab->delete();
    }
}
