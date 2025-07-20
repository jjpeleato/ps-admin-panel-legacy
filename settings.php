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

define('PS_DYNAMIC_ADMIN_PANEL_UPLOAD_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR);

/** @var string PS_DYNAMIC_ADMIN_PANEL_NAME */
const PS_DYNAMIC_ADMIN_PANEL_NAME = 'AdminDynamicPanel';

/** @var array PS_DYNAMIC_ADMIN_PANEL_FIELDS */
const PS_DYNAMIC_ADMIN_PANEL_FIELDS = [
    'PS_DYNAMIC_ADMIN_PANEL_DISPLAY' => [
        'machine_name' => 'display',
        'tab' => 'general',
        'type' => 'switch',
        'lang' => false,
        'required' => false,
        'label' => 'Display',
        'desc' => 'Show or hide the section.',
        'value' => 0,
    ],
    'PS_DYNAMIC_ADMIN_PANEL_TITLE' => [
        'machine_name' => 'title',
        'tab' => 'general',
        'type' => 'text',
        'lang' => true,
        'required' => true,
        'label' => 'Title',
        'desc' => 'Write a title for the section.',
        'value' => '',
    ],
    'PS_DYNAMIC_ADMIN_PANEL_SHORT_DESCRIPTION' => [
        'machine_name' => 'short_description',
        'tab' => 'general',
        'type' => 'html',
        'lang' => true,
        'required' => false,
        'label' => 'Short description',
        'desc' => 'Write a short description for the section.',
        'value' => '',
    ],
    'PS_DYNAMIC_ADMIN_PANEL_DESCRIPTION' => [
        'machine_name' => 'description',
        'tab' => 'general',
        'type' => 'html',
        'lang' => true,
        'required' => false,
        'label' => 'Description',
        'desc' => 'Write a description for the section.',
        'value' => '',
    ],
    'PS_DYNAMIC_ADMIN_PANEL_VIDEO' => [
        'machine_name' => 'video',
        'tab' => 'media',
        'type' => 'video',
        'lang' => true,
        'required' => false,
        'label' => 'Video',
        'desc' => 'Upload a video for the section.',
        'value' => '',
    ],
    'PS_DYNAMIC_ADMIN_PANEL_IMAGE' => [
        'machine_name' => 'image',
        'tab' => 'media',
        'type' => 'image',
        'lang' => true,
        'required' => false,
        'label' => 'Image',
        'desc' => 'Upload an image for the section.',
        'value' => '',
    ],
    'PS_DYNAMIC_ADMIN_PANEL_IMAGE_ALT' => [
        'machine_name' => 'image_alt',
        'tab' => 'media',
        'type' => 'text',
        'lang' => true,
        'required' => false,
        'label' => 'Alt',
        'desc' => 'Alternative text for image.',
        'value' => '',
    ],
];
