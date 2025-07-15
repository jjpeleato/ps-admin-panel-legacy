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

/**
 * Class Uninstaller
 *
 * This class is responsible for uninstalling module fixtures.
 * It handles the deletion of configuration values for different fields.
 */
class Uninstaller
{
    /** @var array $fields */
    private array $fields = [];

    /** @var ImageHandler $imageHandler */
    private ImageHandler $imageHandler;

    /** @var VideoHandler $videoHandler */
    private VideoHandler $videoHandler;

    /**
     * Uninstaller constructor.
     *
     * Initializes the uninstaller with the fields to be deleted.
     *
     * @param array $fields An associative array where keys are configuration field names to be deleted.
     */
    public function __construct(array $fields = [])
    {
        $this->fields = $fields;

        // Initialize the image handler.
        $this->imageHandler = new ImageHandler(PS_DYNAMIC_ADMIN_PANEL_UPLOAD_DIR);

        // Initialize the video handler.
        $this->videoHandler = new VideoHandler(PS_DYNAMIC_ADMIN_PANEL_UPLOAD_DIR);
    }

    /**
     * Uninstalls the module by deleting all images and configuration fields.
     *
     * @return bool Returns true if the uninstallation was successful, false otherwise.
     */
    public function uninstall(): bool
    {
        // Delete all images and videos in the upload folder.
        $this->imageHandler->deleteAllMedia();
        $this->videoHandler->deleteAllMedia();

        return $this->uninstallFixtures();
    }

    /**
     * Uninstalls the fixtures by deleting the specified configuration fields.
     *
     * @return bool Returns true if all fields were successfully deleted, false otherwise.
     */
    private function uninstallFixtures(): bool
    {
        foreach (array_keys($this->fields) as $field) {
            if (!Configuration::deleteByName($field)) {
                return false; // Stop if any deletion fails
            }
        }

        return true;
    }
}
