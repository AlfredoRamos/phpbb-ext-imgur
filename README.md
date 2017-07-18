### About

Imgur Extension for phpBB 3.2.x

[![Build Status](https://img.shields.io/travis/AlfredoRamos/phpbb-ext-imgur.svg?style=flat-square&maxAge=3600)](https://travis-ci.org/AlfredoRamos/phpbb-ext-imgur) [![Latest Stable Version](https://img.shields.io/github/tag/AlfredoRamos/phpbb-ext-imgur.svg?label=stable&style=flat-square&maxAge=3600)](https://github.com/AlfredoRamos/phpbb-ext-imgur/releases) [![License](https://img.shields.io/github/license/AlfredoRamos/phpbb-ext-imgur.svg?style=flat-square)](https://raw.githubusercontent.com/AlfredoRamos/phpbb-ext-imgur/master/license.txt)

### Dependencies

- `php` 5.6 or greater
- `phpBB` 3.2 or greater
- `composer` (for development only)
- Imgur API data (`client_id` and `client_secret`)

### Installation

- Download the [latest release](https://github.com/AlfredoRamos/phpbb-ext-imgur/releases)
- Decompress the `*.zip` or `*.tar.gz` file
- Copy the files and directories inside `{PHPBB_ROOT}/ext/alfredoramos/imgur/`
- Run `composer install --prefer-dist --no-dev` inside `{PHPBB_ROOT}/ext/alfredoramos/imgur/`
- Go to your `Administration Control Panel` > `Customize` > `Manage extensions`
- Click on `Enable` and confirm.

### Usage

Click on the `Imgur` posting button and select the image(s) you want to upload.

If the upload is successful it will add the `[img]` BBCode with its respective link in the topic, private message or signature content.

### Preview

[![ACP settings](https://i.imgur.com/ydMrvEUm.png)](https://i.imgur.com/ydMrvEU.png)

[![ACP settings API](https://i.imgur.com/ylIBynrm.png)](https://i.imgur.com/ylIBynr.png)

[![Topic](https://i.imgur.com/8C7sMR2m.png)](https://i.imgur.com/8C7sMR2.png)

*(Click to view in full size)*

### Configuration

- Go to your `Administration Control Panel` > `Extensions` > `Imgur settings`
- Set the `Client ID`, `Client Secret` and optionally an `Album`
- Click on `Submit`
- Once you have the required API data. Click on the authorization link shown above
- In the Imgur authorization link, click on `Allow` and copy the `PIN`
- Go back to `Imgur settings` paste the `PIN`
- Click on `Submit`

### Uninstallation

- Go to your `Administration Control Panel` > `Customize` > `Manage extensions`
- Click on `Disable` and confirm
- Go back to `Manage extensions` > `Imgur` > `Delete data` and confirm

### Upgrade

- Uninstall the extension
- Delete all the files inside `{PHPBB_ROOT}/alfredoramos/imgur/`
- Download the new version
- Install the extension
