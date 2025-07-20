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
// phpcs:enable

namespace PrestaShop\Module\PsDynamicAdminPanel\Native\Classes;

/**
 * ImageHandler class for managing image uploads.
 *
 * This class provides functionality to handle image uploads, including
 * creating the upload directory if it does not exist.
 */
class ImageHandler extends MediaHandler
{
    /**
     * Maximum file size for image uploads.
     * This is set to 4 MB (4.000.000 bytes).
     */
    private const MAX_FILE_SIZE = 4000000; // 4 MB

    /**
     * Constructor for the ImageHandler class.
     *
     * This constructor initializes the allowed image extensions.
     * It is called when an instance of the class is created.
     *
     * @param string $path
     */
    public function __construct(string $path = '')
    {
        $this->path = $path;
        $this->maxFileSize = self::MAX_FILE_SIZE;
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
        parent::__construct();
    }
}
