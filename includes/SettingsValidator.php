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

namespace PrestaShop\Module\PsAdminPanelLegacy\Helper\Includes;

// phpcs:disable
/**
 * If this file is called directly, then abort execution.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
// phpcs:enable

use Context;

/**
 * Class SettingsValidator
 *
 * Validates the structure of the settings file.
 *
 * @since 0.1.0
 * @author @jjpeleato
 */
class SettingsValidator
{
    /** @var Translator $translator */
    protected $translator;

    /** @var array $fields */
    private array $fields = [];

    /**
     * SettingsValidator constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        $this->translator = Context::getContext()->getTranslator();
        $this->fields = $fields;
    }

    /**
     * Validates the settings structure.
     *
     * @return string
     */
    public function validate(): string
    {
        $errors = '';

        // Validate constants.
        $validationFields = $this->validateFields();
        foreach ($validationFields as $error) {
            $errors .= $error . '<br>';
        }

        return $errors;
    }

    /**
     * Validates the fields defined in the module.
     *
     * @return array
     */
    private function validateFields(): array
    {
        $errors = [];

        // Validate if keys is uppercase.
        foreach (array_keys($this->fields) as $key) {
            if (strtoupper($key) !== $key) {
                $errors[] = $this->translator->trans(
                    'The key "%key%" must be uppercase.',
                    [
                        '%key%' => $key
                    ],
                    PS_ADMIN_PANEL_LEGACY_DOMAIN
                );
            }
        }

        // Validate if all required keys are present.
        $requiredKeys = ['machine_name', 'tab', 'type', 'lang', 'required', 'label', 'desc', 'value'];
        foreach ($this->fields as $key => $field) {
            foreach ($requiredKeys as $requiredKey) {
                if (!array_key_exists($requiredKey, $field)) {
                    $errors[] = $this->translator->trans(
                        'The key "%key%" is required in the field "%field%".',
                        [
                            '%key%' => $requiredKey,
                            '%field%' => $key
                        ],
                        PS_ADMIN_PANEL_LEGACY_DOMAIN
                    );
                }
            }
        }

        return $errors;
    }
}
