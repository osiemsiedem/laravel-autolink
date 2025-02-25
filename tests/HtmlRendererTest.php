<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink;

use OsiemSiedem\Autolink\Elements\BaseElement;
use OsiemSiedem\Autolink\Filters\LimitLengthFilter;
use OsiemSiedem\Autolink\HtmlRenderer;

final class HtmlRendererTest extends TestCase
{
    public function test_render(): void
    {
        $element = new BaseElement('http://example.com/some/very/long/link?foo=bar', 'http://example.com/some/very/long/link?foo=bar', 0, 46, ['class' => 'autolink']);

        $renderer = new HtmlRenderer;

        $html = (string) $renderer->render('http://example.com/some/very/long/link?foo=bar', [$element]);

        $this->assertEquals('<a class="autolink" href="http://example.com/some/very/long/link?foo=bar">http://example.com/some/very/long/link?foo=bar</a>', $html);
    }

    public function test_add_filter(): void
    {
        $element = new BaseElement('http://example.com/', 'http://example.com/', 0, 19);

        $renderer = new HtmlRenderer;

        $html = (string) $renderer->render('http://example.com/', [$element]);

        $this->assertEquals('<a href="http://example.com/">http://example.com/</a>', $html);

        $renderer->addFilter(new LimitLengthFilter(12));

        $html = (string) $renderer->render('http://example.com/', [$element]);

        $this->assertEquals('<a href="http://example.com/">http://examp...</a>', $html);
    }
}
