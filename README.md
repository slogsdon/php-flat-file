# flat-file

## Getting Started

> Example `composer.json`

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

Don't forget to pull down your dependencies:

```
composer install
```

Next, your individual pages need to be included in a `pages` directory at the root of your project (i.e. next to your `composer.json` file). Pages can be written in Markdown (with the `.md` or `.markdown` file extension), plain HTML, or PHP (with the `.php` file extension). PHP files have the option of outputing the content as normal (echo, print, content outside of `<?php ?>` tags, etc.) or returning the content as a string (`<?php return 'hello world';`).

> `pages/index.php`

```php
<?php /* hello */ ?>
<h1>Hello World</h1>
```

Ready to test? Spin up the development server:

```
composer start
```

This will start a PHP development server listening on `http://localhost:3000`. Pressing `Ctrl-C` will stop the server, freeing the way for building your project to plain HTML files for later deployment:

```
composer build
```

Ignoring Composer's `vendor` directory, you should see something similar to below in your project root:

```
.
├── composer.json
├── composer.lock
├── dist
│   └── index.html
├── pages
└── vendor
```

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.
