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

/**
 * VideoHandler class for managing video uploads.
 *
 * This class provides functionality to handle video uploads, including
 * creating the upload directory if it does not exist.
 */
class VideoHandler extends MediaHandler
{
    /**
     * Maximum file size for video uploads.
     * This is set to 10 MB (10.000.000 bytes).
     */
    private const MAX_FILE_SIZE = 10000000; // 10 MB

    /**
     * Constructor for the VideoHandler class.
     *
     * This constructor initializes the allowed video extensions.
     * It is called when an instance of the class is created.
     *
     * @param string $path The path where videos will be uploaded.
     */
    public function __construct(string $path = '')
    {
        $this->path = $path;
        $this->maxFileSize = self::MAX_FILE_SIZE;
        $this->authExtensions = ['mp4', 'avi', 'mov', 'mkv', 'webm', 'flv', 'quicktime'];
        $this->authMimeType = [
            'video/mp4',
            'video/avi',
            'video/mov',
            'video/x-matroska',
            'video/webm',
            'video/x-msvideo',
            'video/x-flv',
            'video/quicktime',
        ];
        parent::__construct();
    }
}
