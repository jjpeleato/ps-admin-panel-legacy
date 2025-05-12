# PrestaShop: Admin Panel (Legacy)

This PrestaShop module adds an admin panel to the back office, allowing easy configuration of various types of fields. It is designed to work with PrestaShop versions 1.7.8.0 to 8.2.1 and provides a user-friendly interface for managing module settings.

## Multi-store and multi-language compatibility

This module supports PrestaShop's multi-store and multi-language feature. If multistore is enabled, ensure that you select the appropriate store context before configuring the module.

## Requirements

- **PrestaShop version**: 1.7.8.0 to 8.2.1
- **PHP version**: Compatible with the PHP version required by your PrestaShop installation.

## Installation

1. Clone or download this repository into the `modules` directory of your PrestaShop installation.
2. Navigate to the PrestaShop back office.
3. Go to **Modules > Module Manager**.
4. Search for `PrestaShop Admin Panel` and click **Install**.

### Installation to develop

- You have to install **Lando**: https://docs.devwithlando.io/

If Lando's tools does not work for you, there is another way. You must install the environment manually: XAMP, Composer, Node.JS and NPM or Yarn.

For more information visit:

- XAMP: https://www.apachefriends.org/es/index.html
- Composer: https://getcomposer.org/
- Node and NPM: https://nodejs.org/es/
- Yarn: https://yarnpkg.com/es-ES/

**Note:** If you work with Windows < 10. To execute the commands, we recommend installing **Cygwin** http://www.cygwin.com/

**Note:** If you work with Windows 10. To execute the commands, we recommend installing **WSL 2** with Ubuntu https://docs.microsoft.com/es-es/windows/wsl/install-win10

**Note:** If you work with Windows 10. You need install the following package: https://www.npmjs.com/package/win-node-env

**Note:** I recommend installing the following IDE for PHP Programming: Visual Studio Code (https://code.visualstudio.com/) or PHPStorm (https://www.jetbrains.com/phpstorm/).

### Project structure

```
ps_admin_panel_legacy/
├── .husky
├── views/
│   ├── index.php
│   ├── templates/
│       ├── admin/
│       │   ├── index.tpl
│       └── widget/
│           └── index.tpl
├── .editorconfig
├── .gitignore
├── .prettierignore
├── commitlint.config.cjs
├── composer.json
├── index.php
├── LICENSE
├── logo.png
├── package.json
├── phpcs.xml
├── ps_admin_panel_legacy.php
└── README.md
```

### Technologies and tools

This project utilizes various technologies and tools for automation and the development process. For more details and learning resources, please refer to the following links.

1. PHP: https://www.php.net/
2. MariaDB: https://mariadb.org/
3. MySQL: https://www.mysql.com/
4. Apache: https://www.apache.org/
5. PrestaShop: https://prestashop.com/
6. Lando: https://docs.devwithlando.io/
7. Docker: https://www.docker.com/
8. Git: https://git-scm.com/
9. Composer: https://getcomposer.org/
10. PHP_CodeSniffer: https://github.com/squizlabs/PHP_CodeSniffer
11. Node.js: https://nodejs.org/
12. NPM: https://www.npmjs.com/
13. Yarn: https://yarnpkg.com/
14. Gulp: https://gulpjs.com/
15. EditorConfig: https://editorconfig.org/
16. Husky: https://www.npmjs.com/package/husky
17. Commitlint: https://commitlint.js.org/
18. Commitizen: http://commitizen.github.io/cz-cli/

**Note:** Many thanks to all the developers who worked on these projects.

## Contributing

Contributions are welcome! Please follow the coding standards defined in `phpcs.xml` and ensure all changes are tested before submitting a pull request.

## Support

For issues or feature requests, please open an issue in the repository or contact with me directly.

## Finally

More information on the following commits. If required.

Grettings **@jjpeleato**.
