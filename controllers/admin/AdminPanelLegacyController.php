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
/**
 * If this file is called directly, then abort execution.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
// phpcs:enable

/**
 * Class AdminPanelLegacyController
 *
 * @since 0.1.0
 * @author @jjpeleato
 */
class AdminPanelLegacyController extends ModuleAdminController
{
    /** @var ps_admin_panel_legacy */
    public $module;

    /**
     * AdminPanelLegacyController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * TODO: Short description.
     *
     * @throws PrestaShopException
     */
    public function displayAjaxDeleteImage()
    {
        $now = new DateTime();

        // Response
        $this->ajaxRenderJson($now);
    }

    /**
     * TODO: Short description.
     *
     * @param string $content
     *
     * @throws PrestaShopException
     */
    private function ajaxRenderJson($content)
    {
        header('Content-Type: application/json');
        $this->ajaxRender(json_encode($content));
    }
}
