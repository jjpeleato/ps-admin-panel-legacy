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
 * HelperImage class for managing image uploads.
 *
 * This class provides functionality to handle image uploads, including
 * creating the upload directory if it does not exist.
 */
class HelperImage
{
    /**
     * Maximum file size for image uploads.
     * This is set to 4 MB (4000000 bytes).
     */
    private const MAX_FILE_SIZE = 4000000; // 4 MB

    /** @var string $path */
    private string $path = '';

    /** @var Translator $translator */
    private $translator;

    /** @var array $authExtensions. */
    private array $authExtensions = [];

    /** @var array $authMimeType. */
    private array $authMimeType = [];

    /**
     * Constructor for the HelperImage class.
     *
     * This constructor initializes the allowed image extensions.
     * It is called when an instance of the class is created.
     *
     * @param string $path The path where images will be uploaded.
     */
    public function __construct(string $path = '')
    {
        $this->path = $path;
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
     * Uploads an image to the configured directory.
     *
     * @param array $file The file data array (e.g., $_FILES['field']).
     * @param string $key The configuration key for the image.
     * @param int $lang The language ID for the configuration.
     *
     * @return array {
     *     @type bool   $success  True if upload succeeded, false otherwise.
     *     @type string $filename The sanitized file name.
     *     @type string $error    Error message if any.
     * }
     */
    public function uploadImage(array $files = [], string $key = '', int $lang = 0): array
    {
        if (!is_dir($this->path)) {
            return [
                'success' => false,
                'filename' => '',
                'error' => $this->translator->trans(
                    'Upload directory could not be created.',
                    [],
                    PS_ADMIN_PANEL_LEGACY_DOMAIN
                ),
            ];
        }

        $uploading = $files[$key . '_' . $lang];

        if (
            isset($uploading)
            && isset($uploading['tmp_name'])
            && !empty($uploading['tmp_name'])
        ) {
            $error = ImageManager::validateUpload(
                $uploading,
                self::MAX_FILE_SIZE,
                $this->authExtensions,
                $this->authMimeType
            );
            if ($error) {
                return [
                    'success' => false,
                    'filename' => '',
                    'error' => $error,
                ];
            }

            $ext = substr($uploading['name'], strrpos($uploading['name'], '.') + 1);
            $safeName = $this->sanitizeFileName($uploading['name']);
            $filename = md5($safeName) . '.' . $ext;

            if (!move_uploaded_file($uploading['tmp_name'], $this->path . $filename)) {
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
            $this->deleteImage($key, $lang, $filename);

            return [
                'success' => true,
                'filename' => $filename,
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
     * Deletes a specific image file.
     *
     * @param string $key The configuration key for the image.
     * @param int $lang The language ID for the configuration.
     * @param string $filename The name of the file to delete.
     *
     * @return bool True if the file was deleted, false otherwise.
     */
    private function deleteImage(string $key, int $lang, string $filename): bool
    {
        if (Configuration::hasContext($key, $lang, Shop::getContext()) && Configuration::get($key, $lang) != $filename) {
            $oldImage = Configuration::get($key, $lang);
            return @unlink($this->path . $oldImage);
        }

        return false;
    }

    /**
     * Deletes all images in the configured directory.
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

    /**
     * Sanitizes the file name to prevent security issues.
     *
     * @param string $filename
     *
     * @return string
     */
    private function sanitizeFileName(string $filename): string
    {
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);

        return $filename;
    }
}
