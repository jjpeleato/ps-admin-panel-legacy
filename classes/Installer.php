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

// phpcs:disable
/**
 * If this file is called directly, then abort execution.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
// phpcs:enable

use Configuration;
use PrestaShopLogger;

/**
 * Class Installer
 *
 * This class is responsible for installing module fixtures.
 * It handles the installation of configuration values for different shops and languages.
 */
class Installer
{
    /** @var array */
    private array $shops = [];

    /** @var array */
    private array $languages = [];

    /** @var array */
    private array $fields = [];

    /**
     * Installer constructor.
     *
     * @param array $shops
     * @param array $languages
     * @param array $fields
     */
    public function __construct(array $shops = [], array $languages = [], array $fields = [])
    {
        $this->shops = $shops;
        $this->languages = $languages;
        $this->fields = $fields;
    }

    /**
     * This method is used to install the module fixtures.
     * It is called when the module is installed.
     *
     * @return bool
     */
    public function installShopFixtures(): bool
    {
        foreach ($this->shops as $shop) {
            $idShopGroup = (int) $shop['id_shop_group'];
            $idShop = (int) $shop['id_shop'];

            if (!$this->installShopFixture($idShopGroup, $idShop)) {
                return false;
            }

            foreach ($this->languages as $lang) {
                $idLang = (int) $lang['id_lang'];

                if (!$this->installLanguageFixture($idLang, $idShopGroup, $idShop)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * This method is used to install the fixture for a specific shop group and shop.
     * It updates the configuration values for the given shop group and shop.
     *
     * @param int $idShopGroup
     * @param int $idShop
     * @return bool
     */
    public function installShopFixture(int $idShopGroup = 0, int $idShop = 0): bool
    {
        foreach ($this->fields as $key => $field) {
            if ($field['lang'] === true) {
                continue;
            }

            if (!Configuration::updateValue($key, $field['value'], false, $idShopGroup, $idShop)) {
                PrestaShopLogger::addLog(
                    "Failed to update configuration key: $key for shop $idShop and shop group $idShopGroup",
                    3
                );

                return false;
            }
        }

        return true;
    }

    /**
     * This method is used to install the language fixture.
     * It updates the configuration values for the given language, shop group, and shop.
     *
     * @param int $idLang
     * @param int $idShopGroup
     * @param int $idShop
     * @return bool
     */
    public function installLanguageFixture(int $idLang = 0, int $idShopGroup = 0, int $idShop = 0): bool
    {
        foreach ($this->fields as $key => $field) {
            if ($field['lang'] === false) {
                continue;
            }

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
}
