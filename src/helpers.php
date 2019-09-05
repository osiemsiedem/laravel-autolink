<?php

declare(strict_types=1);

use Illuminate\Support\HtmlString;
use OsiemSiedem\Autolink\Facades\Autolink;

if (! function_exists('autolink')) {
    /**
     * Convert URLs in the string into clickable links.
     *
     * @param  string  $text
     * @param  callable|null  $callback
     * @return \Illuminate\Support\HtmlString
     */
    function autolink(string $text, callable $callback = null): HtmlString
    {
        return Autolink::convert($text, $callback);
    }
}
