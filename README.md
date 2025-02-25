# Autolink for Laravel

<p>
<a href="https://github.com/osiemsiedem/laravel-autolink/actions"><img src="https://github.com/osiemsiedem/laravel-autolink/workflows/Tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/osiemsiedem/laravel-autolink"><img src="https://img.shields.io/packagist/v/osiemsiedem/laravel-autolink" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/osiemsiedem/laravel-autolink"><img src="https://img.shields.io/packagist/l/osiemsiedem/laravel-autolink" alt="License"></a>
</p>

A Laravel package for converting URLs in a given string of text into clickable links.

## Requirements

- PHP >= 8.2
- Laravel >= 12.0

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
