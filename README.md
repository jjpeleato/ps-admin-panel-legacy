# PrestaShop: Admin Panel (Legacy)

This module facilitates the creation and management of custom administration panels within PrestaShop’s back office. It automatically generates configuration forms based on the `settings.php` file, incorporating various types of fields such as text, HTML, switches, images, and videos.

### The problem

This module has been developed with the aim of simplifying and streamlining the creation of custom admin panels in PrestaShop. Typically, these tasks involve repetitive work that consumes time and resources without adding significant value to the project. Thanks to this module, much of that process is automated, allowing developers to focus on more important aspects of development.

In addition, it is specifically designed to support frontend teams, enabling them to build functional admin interfaces without directly relying on the backend team. This results in greater autonomy, reduced development time, and overall improved workflow efficiency.

### Main features

- **Automatic panel generation**: Dynamically creates admin interfaces based on the configuration in `settings.php`.
- **Multiple field types**: Supports text fields, HTML with WYSIWYG editor, switches, images and videos.
- **Tabbed organization**: Fields can be organized into different tabs.
- **Media management**: Includes upload, validation and deletion of media files (images and videos).
- **Built-in validation**: Automatically validates uploaded files and required fields.
- **Multi-language support**: Fully supports PrestaShop’s multi-language features, allowing language-specific configurations.
- **Multi-store compatibility**: This module supports PrestaShop's multi-store feature. If multistore is enabled, ensure that you select the appropriate store context before configuring the module.

### Requirements

- **PrestaShop version**: 1.7.6.0 to 8.2.1
- **PHP version**: Compatible with the PHP version required by your PrestaShop installation.

### Requirements to develop

To run this project, you must have Composer and Node.js installed, as well as a package manager such as NPM or Yarn.

For more information visit:

- Composer: https://getcomposer.org/
- Node and NPM: https://nodejs.org/es/
- Yarn: https://yarnpkg.com/es-ES/

## Installation to develop

1. Clone or download this repository into the `modules` directory of your PrestaShop installation.
2. Navigate to the module directory:
   ```bash
   cd modules/ps_admin_panel_legacy
   ```
3. Install the dependencies using Composer:
   ```bash
   composer install
   ```
4. Install the Node.js dependencies:
   ```bash
   npm install --save-dev
   ```
5. Search and replace all occurrences with your new module name throughout the project files. Remember to activate the search: "Match uppercase and lowercase".
  - Rename the module directory from `ps_admin_panel_legacy` to `your_new_module_name`.
  - Rename the main module file from `ps_admin_panel_legacy.php` to `your_new_module_name.php`. Remember that both must match to work correctly.
  - Rename `Ps_Admin_Panel_Legacy` to `Your_New_Module_Name`.
  - Rename `ps_admin_panel_legacy` to `your_new_module_name`.
  - Rename `PS_ADMIN_PANEL_LEGACY_` to `YOUR_NEW_MODULE_NAME_`.
  - Rename the `AdminPanelLegacyController` file to `AdminYourNewModuleNameController`. Remember that it is mandatory to use the prefix `Admin` and the suffix `Controller`.
  - Update the `PS_ADMIN_PANEL_LEGACY_NAME` constant in `settings.php` to use the exact same name `AdminYourNewModuleName`. Both must match for the panel to work correctly.
  - Change the value of `PS_ADMIN_PANEL_LEGACY_DOMAIN` in `settings.php` to your new domain name: `Modules.Yournewmodulename.Admin`.
6. Install the module in PrestaShop's back office.
7. Clear the PrestaShop cache to ensure the module is recognized.
8. End and happy coding!

### Configure file `settings.php`

The `settings.php` file is the core of the module's configuration. It is used to define the structure and content of the admin panel, and should be placed in the root directory of the module.

### Project structure

```
ps_admin_panel_legacy/
├── .husky/ # Husky hooks for Git
│   ├── commit-msg
│   ├── post-merge
│   ├── post-rewrite
│   └── pre-commit
├── classes/
│   ├── HelperFormExtended.php # Extended helper form class for custom fields
│   ├── ImageHandler.php # Class for handling image uploads
│   ├── index.php
│   ├── Installer.php # Installer class for setting up the module
│   ├── MediaHandler.php # Class for managing media files (images and videos)
│   ├── TabInstaller.php # Class for installing tabs in the admin panel
│   ├── Uninstaller.php # Uninstaller class for cleaning up the module
│   └── VideoHandler.php # Class for handling video uploads
├── controllers/
│   ├── admin/
│   │   ├── AdminPanelLegacyController.php # Handles the AJAX request to delete media files
│   │   └── index.php
│   └── index.php
├── upload/ # Directory for uploaded media files
│   └── index.php
├── views/ 
│   ├── js/
│   │   ├── custom.js
│   │   └── index.php
│   ├── templates/
│   │   ├── admin/
│   │   │   ├── _configure/
│   │   │   │   ├── helpers/
│   │   │   │   │   ├── form/
│   │   │   │   │   │   ├── form.tpl
│   │   │   │   │   │   └── index.php
│   │   │   │   │   └── index.php
│   │   │   │   └── index.php
│   │   │   ├── index.php
│   │   │   └── index.tpl
│   │   ├── widget/
│   │   │   ├── index.php
│   │   │   └── index.tpl
│   │   └── index.php
│   └── index.php
├── .editorconfig
├── .gitignore
├── .prettierignore
├── commitlint.config.cjs
├── composer.json
├── config.xml
├── index.php
├── LICENSE
├── logo.png
├── package.json
├── phpcs.xml
├── ps_admin_panel_legacy.php # Main module file
├── README.md
└── settings.php # Configuration file for the module
```

## Technologies and tools

This project utilizes various technologies and tools for automation and the development process. For more details and learning resources, please refer to the following links.

1. Git: https://git-scm.com/
2. PHP: https://www.php.net/
3. MariaDB: https://mariadb.org/
4. Apache: https://www.apache.org/
5. Docker: https://www.docker.com/
6. Lando: https://docs.devwithlando.io/
7. PrestaShop: https://prestashop.com/
8. Composer: https://getcomposer.org/
9. PHP_CodeSniffer: https://github.com/squizlabs/PHP_CodeSniffer
10. Node.js: https://nodejs.org/
11. NPM: https://www.npmjs.com/
12. Yarn: https://yarnpkg.com/
13. Gulp: https://gulpjs.com/
14. EditorConfig: https://editorconfig.org/
15. Husky: https://www.npmjs.com/package/husky
16. Commitlint: https://commitlint.js.org/
17. Commitizen: http://commitizen.github.io/cz-cli/

**Note:** Many thanks to all the developers who worked on these projects.

## Contributing

Contributions are welcome! Please follow the coding standards defined in `phpcs.xml` and ensure all changes are tested before submitting a pull request.

## Support

For issues or feature requests, please open an issue in the repository or contact with me directly.

## Finally

More information on the following commits. If required.

Grettings **@jjpeleato**.
