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

use Configuration;
use Context;
use ImageManager;
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
 * Abstract class MediaHandler
 *
 * This class provides methods for handling media uploads, deletions, and validations.
 * It is designed to be extended by specific media handlers (e.g., ImageHandler, VideoHandler).
 */
abstract class MediaHandler
{
    /** @var Translator $translator */
    protected $translator;

    /** @var string $path */
    protected string $path = '';

    /** @var int $maxFileSize */
    protected int $maxFileSize = 0;

    /** @var array $authExtensions. */
    protected array $authExtensions = [];

    /** @var array $authMimeType. */
    protected array $authMimeType = [];

    /**
     * Constructor for the MediaHandler class.
     *
     * This constructor initializes the translator.
     */
    public function __construct()
    {
        $this->translator = Context::getContext()->getTranslator();
    }

    /**
     * Uploads media to the configured directory.
     *
     * @param array $file The file data array (e.g., $_FILES['field']).
     * @param string $key The configuration key for the media.
     * @param int $lang The language ID for the configuration.
     *
     * @return array {
     *     @type bool   $success  True if upload succeeded, false otherwise.
     *     @type string $filename The sanitized file name.
     *     @type string $error    Error message if any.
     * }
     */
    public function uploadMedia(array $files = [], string $key = '', int $lang = 0): array
    {
        if (!is_dir($this->path)) {
            return [
                'success' => false,
                'filename' => '',
                'error' => $this->translator->trans(
                    'Upload directory could not be created.',
                    [],
                    'Modules.Psdynamicadminpanel.Admin'
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
                $this->maxFileSize,
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
                        'Modules.Psdynamicadminpanel.Admin'
                    ),
                ];
            }

            // Delete old media.
            $this->deleteMedia($key, $lang, $filename);

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
     * Deletes a specific media file.
     *
     * @param string $key The configuration key for the media.
     * @param int $lang The language ID for the configuration.
     * @param string $filename The name of the file to delete.
     *
     * @return bool True if the file was deleted, false otherwise.
     */
    protected function deleteMedia(string $key = '', int $lang = 0, string $filename = ''): bool
    {
        if (Configuration::hasContext($key, $lang, Shop::getContext()) && Configuration::get($key, $lang) != $filename) {
            $media = Configuration::get($key, $lang);
            return @unlink($this->path . $media);
        }

        return false;
    }

    /**
     * Deletes all media in the configured directory.
     *
     * @return void
     */
    public function deleteAllMedia(): void
    {
        if (!is_dir($this->path)) {
            return;
        }

        $medias = glob($this->path . '*.{' . implode(',', $this->authExtensions) . '}', GLOB_BRACE);
        if ($medias) {
            array_map('unlink', $medias);
        }
    }

    /**
     * Sanitizes the file name to prevent security issues.
     *
     * @param string $filename
     *
     * @return string
     */
    protected function sanitizeFileName(string $filename = ''): string
    {
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);

        return $filename;
    }
}
