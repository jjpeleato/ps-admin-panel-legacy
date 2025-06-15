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

define('PS_ADMIN_PANEL_LEGACY_UPLOAD_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR);
const PS_ADMIN_PANEL_LEGACY_DOMAIN = 'Modules.Psadminpanellegacy.Admin';
const PS_ADMIN_PANEL_LEGACY_FIELDS = [
    'PS_ADMIN_PANEL_LEGACY_TITLE' => [
        'machine_name' => 'title',
        'type' => 'text',
        'lang' => true,
        'required' => true,
        'label' => 'Title',
        'desc' => 'Write a title for the section.',
        'value' => '',
    ],
    'PS_ADMIN_PANEL_LEGACY_SHORT_DESCRIPTION' => [
        'machine_name' => 'short_description',
        'type' => 'html',
        'lang' => true,
        'required' => false,
        'label' => 'Short description',
        'desc' => 'Write a short description for the section.',
        'value' => '',
    ],
    'PS_ADMIN_PANEL_LEGACY_DESCRIPTION' => [
        'machine_name' => 'description',
        'type' => 'html',
        'lang' => true,
        'required' => false,
        'label' => 'Description',
        'desc' => 'Write a description for the section.',
        'value' => '',
    ],
    'PS_ADMIN_PANEL_LEGACY_IMAGE' => [
        'machine_name' => 'image',
        'type' => 'image',
        'lang' => true,
        'required' => false,
        'label' => 'Image',
        'desc' => 'Upload an image for the section.',
        'value' => '',
    ],
];
