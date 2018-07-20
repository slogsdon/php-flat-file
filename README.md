# flat-file

> Fast static-site generator / flat-file CMS

[View an example project.](https://github.com/slogsdon/php-flat-file-example)

### Features

- Needs zero configuration
- Uses Markdown, HTML, or PHP
- Routes based on file system
- Exports static sites, or runs via PHP powered web servers
- Includes development environment

### Reasoning

PHP is easy to install, if not already present on you computer. PHP runs pretty much everywhere. PHP is flexible.

This project also scratches an itch to see how much PHP can handle in this problem domain.

## Requirements

- [PHP 7+](http://www.php.net/)
- [Composer](https://getcomposer.org/)

## Getting Started

```json
{
    "name": "user/site",
    "description": "",
    "require": {
        "slogsdon/flat-file": "dev-master"
    },
    "scripts": {
        "build": "flat-file build",
        "start": "flat-file serve"
    },
    "config": {
      "process-timeout": 0
    }
}
```

The `scripts` and `config` sections are not required, but they do help simplify the development process. If using the `start` script, the `process-timeout` configuration option allows Composer to run a script for longer than the default timeout (300 seconds).

If not using Composer script configurations, you'll need to reference the `flat-file` script as `vendor/bin/flat-file`, e.g.:

```
vendor/bin/flat-file build
vendor/bin/flat-file serve
```

After setting up your `composer.json` file, don't forget to pull down your dependencies:

```
composer install
```

### Adding Content

Next, your individual pages need to be included in a `pages` directory at the root of your project (i.e. next to your `composer.json` file). Pages can be written in Markdown (with the `.md` or `.markdown` file extension), plain HTML, or PHP (with the `.php` file extension). PHP files have the option of outputing the content as normal (echo, print, content outside of `<?php ?>` tags, etc.) or returning the content as a string (`<?php return 'hello world';`).


```php
<?php /* pages/index.php */ ?>
<h1>Hello World</h1>
```

Add more pages with new files under `pages`, e.g.:

```md
<!-- pages/about.md -->
# About
```

### Running the Development Server

Ready to test? Spin up the development server:

```
composer start
```

This will start a PHP development server listening on `http://localhost:3000`. Pressing `Ctrl-C` will stop the server, freeing the way for building your project to plain HTML files for later deployment.

### Building Static Files

Kick off a build of the site:

```
composer build
```

Ignoring Composer's `vendor` directory, you should see something similar to below in your project root once all is said and done:

```
.
├── composer.json
├── composer.lock
├── dist
│   ├── about.html
│   └── index.html
├── pages
└── vendor
```

## F.A.Q.

<dl>
  <dt>What about LiveReload like functionality?</dt>
  <dd>That's being investigated, but use <code>F5</code>/<code>Cmd-R</code> for the time being.</dd>
</dl>

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.
