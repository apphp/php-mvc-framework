<p align="center"><img src="http://apphpframework.com/images/light-logo.png" width="400"></p>
<br>

<p align="center">
<a href="https://github.com/apphp/php-mvc-framework"><img src="https://github.com/apphp/php-mvc-framework/workflows/build/badge.svg" alt="Build Status"></a>
<a href="https://opensource.org/licenses/lgpl-3.0.html"><img src="http://apphpframework.com/images/badges/license.svg" alt="License LGPL"></a>
<a href="http://apphpframework.com/"><img src="http://apphpframework.com/images/badges/stable.svg" alt="Stable Version"></a>
</p>

## About ApPHP Framework

Thank you for choosing ApPHP - a high-performance PHP MVC framework.

ApPHP MVC Framework is designed to provide modern and rapid development of websites, web applications and web services.

It implements the the Model-View-Controller (MVC) design pattern and principles, including separation of display, logic, 
and data layers. It provides an architecture, components and tools for developers to build a complex web applications 
faster and safer.

## Installing ApPHP Framework 

#### via Git

``` bash
git clone https://github.com/apphp/php-mvc-framework.git 
```

#### via Composer

You can install ApPHP into your project using [Composer](https://getcomposer.org).

If you're starting a new project, we recommend using the [Directy CMF](https://github.com/apphp/directy-cmf) as
a starting point. For installing new version in existing applications you can run the following:

``` bash
composer require apphp/php-mvc-framework
```

#### Manual installation

Please make sure the release file is unpacked under a web-accessible directory.
You will see the following files and directories:

    demos/              demo applications
    docs/               documentation
    framework/          framework source files
    tests/              PHPUnit tests
    utils/              some utilities
        requirements/   requirements checker
        tests/          tests
        generators/     code generators
    CHANGELOG           describing changes in every ApPHP release
    LICENSE             license of ApPHP Framework
    README              this file
    UPDATE              updating instructions

## Running Tests

Assuming you have PHPUnit installed system wide using one of the methods stated
[here](https://phpunit.de/manual/current/en/installation.html), you can run the
tests for ApPHP Framework by doing the following:

1. Install composer on your server.
2. After Composer is installed, install PHPUnit by
``` bash
composer require phpunit/phpunit --dev
```
2. Make sure you added following to <strong>composer.json</strong> file:
``` bash
"scripts": {
    "tests-result": "phpunit --colors=always --log-junit test-results.xml",
    "tests": "phpunit --colors=always",
    "test": "phpunit --colors=always --filter"
}
```
4. Run `phpunit` by:
``` bash
composer tests
```

## Requirements

The minimum requirement by ApPHP is that your Web server supports PHP 5.4.0 or
above. ApPHP has been tested with Apache HTTP server on Windows and Linux
operating systems.

## License

The Laravel framework is open-sourced software licensed under the [LGPL3 license](https://opensource.org/licenses/lgpl-3.0.html).

## What's next

Please visit the project website for tutorials, class reference and join discussions with other ApPHP users.

## The ApPHP Developer Team

- [Official Website](http://www.apphpframework.com)
- [Website](https://www.apphp.com/php-framework/)
- [GitHub Repository](https://github.com/apphp/php-mvc-framework)