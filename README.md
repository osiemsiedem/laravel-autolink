# Laravel Autolink

[![Build Status](https://travis-ci.org/osiemsiedem/laravel-autolink.svg?branch=master)](https://travis-ci.org/osiemsiedem/laravel-autolink) [![codecov](https://codecov.io/gh/osiemsiedem/laravel-autolink/branch/master/graph/badge.svg)](https://codecov.io/gh/osiemsiedem/laravel-autolink) [![Latest Stable Version](https://poser.pugx.org/osiemsiedem/laravel-autolink/v/stable)](https://packagist.org/packages/osiemsiedem/laravel-autolink) [![License](https://poser.pugx.org/osiemsiedem/laravel-autolink/license)](https://packagist.org/packages/osiemsiedem/laravel-autolink)

A Laravel package for converting URLs in a given string of text into clickable links.

## Requirements

- PHP >= 8.1
- Laravel >= 10.0

## Installation

```
composer require osiemsiedem/laravel-autolink
```

## Usage

```php
use OsiemSiedem\Autolink\Facades\Autolink;

echo Autolink::convert('Check this out - www.example.com. This will be ignored - <a href="http://example.com">My awesome website</a>.');

// Check this out - <a href="http://www.example.com">example.com</a>. This will be ignored - <a href="http://example.com">My awesome website</a>.
```

## Testing

```
./vendor/bin/phpunit
```

## Credits

This package is based on the [Rinku](https://github.com/vmg/rinku) library.

- [Marek Szymczuk](https://github.com/bonzai)
- [All Contributors](../../contributors)

## License

Please see the [LICENSE.md](LICENSE.md) file.
