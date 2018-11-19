### About

Imgur extension for phpBB.

[![Build Status](https://img.shields.io/travis/AlfredoRamos/phpbb-ext-imgur.svg?style=flat-square)](https://travis-ci.org/AlfredoRamos/phpbb-ext-imgur) [![Latest Stable Version](https://img.shields.io/github/tag/AlfredoRamos/phpbb-ext-imgur.svg?label=stable&style=flat-square)](https://github.com/AlfredoRamos/phpbb-ext-imgur/releases) [![Code Quality](https://img.shields.io/codacy/grade/96ac8d6766cb481483284c89cca8b347.svg?style=flat-square)](https://app.codacy.com/app/AlfredoRamos/phpbb-ext-imgur) [![License](https://img.shields.io/github/license/AlfredoRamos/phpbb-ext-imgur.svg?style=flat-square)](https://raw.githubusercontent.com/AlfredoRamos/phpbb-ext-imgur/master/license.txt)

### Dependencies

- PHP 5.6 or greater
- phpBB 3.2 or greater
- Composer (for development only)
- Imgur API data (`client_id` and `client_secret`)

### Installation

- Download the [latest release](https://github.com/AlfredoRamos/phpbb-ext-imgur/releases)
- Decompress the `*.zip` or `*.tar.gz` file
- Copy the files and directories inside `{PHPBB_ROOT}/ext/alfredoramos/imgur/`
- Run `composer install --prefer-dist --no-dev` inside `{PHPBB_ROOT}/ext/alfredoramos/imgur/`
- Go to your `Administration Control Panel` > `Customize` > `Manage extensions`
- Click on `Enable` and confirm

### Usage

Click on the `Imgur` posting button and select the image(s) you want to upload.

If the upload is successful it will add the image in the topic, private message or signature content. You can choose in the `Administration Control Panel` to show the uploaded image as plan text or with BBCode as URL, image (default) or thumbnail.

### Preview

[![ACP settings](https://i.imgur.com/FDKbWoqt.png)](https://i.imgur.com/FDKbWoq.png) [![ACP settings API](https://i.imgur.com/xxCEse7t.png)](https://i.imgur.com/xxCEse7.png) [![ACP output settings](https://i.imgur.com/CKcYnY2t.png)](https://i.imgur.com/CKcYnY2.png) [![Topic](https://i.imgur.com/8C7sMR2t.png)](https://i.imgur.com/8C7sMR2.png)

*(Click to view in full size)*

### Configuration

- Go to your `Administration Control Panel` > `Extensions` > `Imgur settings`
- Set the `Client ID`, `Client Secret` and optionally an `Album`
- Click on `Submit`
- Once you have the required API data. Click on the authorization link shown above
- A new window will popup to authorize the application
- Login to your Imgur account to allow account access
- The window will close itself when it's done, or show an error message

To customize the look and feel:

- Move into `{PHPBB_ROOT}/ext/alfredoramos/imgur/`
- Copy the `styles/prosilver/` directory to `styles/{STYLE}/`
- Edit the file `styles/{STYLE}/theme/css/imgur.css` as needed

**Note:** If your style doesn't inherit from `prosilver`, you should follow the steps above even if you don't want to change any file.

### Uninstallation

- Go to your `Administration Control Panel` > `Customize` > `Manage extensions`
- Click on `Disable` and confirm
- Go back to `Manage extensions` > `Imgur` > `Delete data` and confirm

### Upgrade

- Uninstall the extension
- Delete all the files inside `{PHPBB_ROOT}/ext/alfredoramos/imgur/`
- Download the new version
- Install the extension
