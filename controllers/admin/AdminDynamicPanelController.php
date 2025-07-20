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

use const PS_DYNAMIC_ADMIN_PANEL_UPLOAD_DIR;

/**
 * AdminDynamicPanelController class
 *
 * This class handles the AJAX request to delete media files in the dynamic admin panel.
 * It extends the ModuleAdminController class to provide the necessary functionality.
 */
class AdminDynamicPanelController extends ModuleAdminController
{
    /**
     * AdminDynamicPanelController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handles the AJAX request to delete a file.
     *
     * @return void
     */
    public function displayAjaxDeleteMedia(): void
    {
        $body = Tools::getValue('body');
        $name = $body['name'] ?? '';
        $idLang = (int) $body['lang'];

        if (!$name || !$idLang) {
            $this->ajaxRenderJson([
                'success' => false,
                'message' => 'Missing required parameters.',
            ]);
            exit;
        }

        $filename = Configuration::get($name, $idLang);

        if (!$filename) {
            $this->ajaxRenderJson([
                'success' => false,
                'message' => 'No media registered for this field and language.',
            ]);
            exit;
        }

        $mediaPath = PS_DYNAMIC_ADMIN_PANEL_UPLOAD_DIR . $filename;

        if (!file_exists($mediaPath)) {
            $this->ajaxRenderJson([
                'success' => false,
                'message' => 'Media file not found on server.',
            ]);
            exit;
        }

        if (!@unlink($mediaPath)) {
            $this->ajaxRenderJson([
                'success' => false,
                'message' => 'Unable to delete the media file.',
            ]);
            exit;
        }

        Configuration::updateValue($name, [$idLang => '']);

        $this->ajaxRenderJson([
            'success' => true,
            'message' => 'Media deleted successfully.',
        ]);
    }

    /**
     * Renders a JSON response.
     *
     * @param mixed $content
     *
     * @return void
     */
    private function ajaxRenderJson($content): void
    {
        header('Content-Type: application/json');
        $this->ajaxRender(json_encode($content));
    }
}
