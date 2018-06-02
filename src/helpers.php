<?php

declare(strict_types=1);

use Illuminate\Support\HtmlString;

if ( ! function_exists('autolink')) {
    /**
     * Convert URLs in the string into clickable links.
     *
     * @param  string  $text
     * @param  callable  $callback
     * @return \Illuminate\Support\HtmlString
     */
    function autolink(string $text, callable $callback = null): HtmlString
    {
        return app('osiemsiedem.autolink')->convert($text, $callback);
    }
}
