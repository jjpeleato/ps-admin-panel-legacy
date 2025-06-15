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

use Context;
use ImageManager;
use Configuration;
use Shop;

// phpcs:disable
/**
 * If this file is called directly, then abort execution.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
// phpcs:enable

/**
 * ImageHandler class for managing image uploads.
 *
 * This class provides functionality to handle image uploads, including
 * creating the upload directory if it does not exist.
 */
class ImageHandler
{
    /** @var string $path */
    private string $path = '';

    /** @var Translator $translator */
    private $translator;

    /** @var array $authExtensions. */
    private array $authExtensions = [];

    /** @var array $authMimeType. */
    private array $authMimeType = [];

    /**
     * Constructor for the ImageHandler class.
     *
     * This constructor initializes the allowed image extensions.
     * It is called when an instance of the class is created.
     */
    public function __construct()
    {
        $this->path = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;
        $this->translator = Context::getContext()->getTranslator();
        $this->authExtensions = ['gif', 'jpg', 'jpeg', 'jpe', 'png', 'svg'];
        $this->authMimeType = [
            'image/gif',
            'image/jpg',
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/x-png',
            'image/svg',
            'image/svg+xml'
        ];
    }

    /**
     * This method is used to upload an image.
     * It is called when the form is submitted.
     *
     * @param string $key
     * @param int $lang
     *
     * @return array
     */
    public function uploadImage(string $key = '', int $lang = 0): array
    {
        if (
            isset($_FILES[$key . '_' . $lang])
            && isset($_FILES[$key . '_' . $lang]['tmp_name'])
            && !empty($_FILES[$key . '_' . $lang]['tmp_name'])
        ) {
            if ($error = ImageManager::validateUpload($_FILES[$key . '_' . $lang], 4000000, $this->authExtensions, $this->authMimeType)) {
                return [
                    'success' => false,
                    'filename' => '',
                    'error' => $error,
                ];
            }

            $ext = substr(
                $_FILES[$key . '_' . $lang]['name'],
                strrpos($_FILES[$key . '_' . $lang]['name'], '.') + 1
            );
            $file = md5($_FILES[$key . '_' . $lang]['name']) . '.' . $ext;

            if (
                false === move_uploaded_file(
                    $_FILES[$key . '_' . $lang]['tmp_name'],
                    $this->path . $file
                )
            ) {
                return [
                    'success' => false,
                    'filename' => '',
                    'error' => $this->translator->trans(
                        'An error occurred while attempting to run move_uploaded_file in the language: ' . $lang,
                        [],
                        PS_ADMIN_PANEL_LEGACY_DOMAIN
                    ),
                ];
            }

            // Delete old image.
            if (
                Configuration::hasContext($key, $lang, Shop::getContext())
                && Configuration::get($key, $lang) != $file
            ) {
                $oldImage = Configuration::get($key, $lang);
                @unlink($this->path . $oldImage);
            }

            return [
                'success' => true,
                'filename' => $file,
                'error' => '',
            ];
        }

        return [
            'success' => true,
            'filename' => Configuration::get($key, $lang),
            'error' => '',
        ];
    }

    /**
     * This method is used to delete all images in the images folder.
     * It is called when the module is uninstalled.
     *
     * @return void
     */
    public function deleteImages(): void
    {
        if (!is_dir($this->path)) {
            return;
        }

        $images = glob($this->path . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
        if ($images) {
            array_map('unlink', $images);
        }
    }
}
