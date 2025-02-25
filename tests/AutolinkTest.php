<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink;

use Illuminate\Support\Str;
use OsiemSiedem\Autolink\Autolink;
use OsiemSiedem\Autolink\Contracts\Element;
use OsiemSiedem\Autolink\HtmlRenderer;
use OsiemSiedem\Autolink\Parser;
use OsiemSiedem\Autolink\Parsers\EmailParser;
use OsiemSiedem\Autolink\Parsers\UrlParser;
use OsiemSiedem\Autolink\Parsers\WwwParser;

final class AutolinkTest extends TestCase
{
    private $autolink;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $parser = new Parser;
        $parser->addElementParser(new UrlParser);
        $parser->addElementParser(new WwwParser);
        $parser->addElementParser(new EmailParser);

        $renderer = new HtmlRenderer;

        $this->autolink = new Autolink($parser, $renderer);
    }

    protected function tearDown(): void
    {
        $this->autolink = null;
    }

    private function e(string $text): string
    {
        return htmlentities($text, ENT_QUOTES, 'UTF-8', false);
    }

    private function generateLink(string $title, ?string $href = null): string
    {
        if (is_null($href)) {
            $href = $title;
        }

        if (! Str::startsWith($href, ['http', 'https', 'mailto'])) {
            $href = 'http://'.$href;
        }

        return '<a href="'.$this->e($href).'">'.$this->e($title).'</a>';
    }

    public function test_escapes_quotes(): void
    {
        $this->assertEquals('<a href="http://example.com/&quot;onmouseover=document.body.style.backgroundColor=&quot;pink&quot;;//">http://example.com/&quot;onmouseover=document.body.style.backgroundColor=&quot;pink&quot;;//</a>', $this->autolink->convert('http://example.com/"onmouseover=document.body.style.backgroundColor="pink";//'));
    }

    public function test_autolink_with_single_trailing_punctuation_and_space(): void
    {
        $url = 'http://example.com';
        $link = $this->generateLink('http://example.com');

        $this->assertEquals($link, $this->autolink->convert('http://example.com'));

        foreach (['?', '!', '.', ',', ':'] as $punc) {
            $this->assertEquals("link: {$link}{$punc} foo?", $this->autolink->convert("link: {$url}{$punc} foo?"));
        }
    }

    public function test_terminates_on_ampersand(): void
    {
        $url = 'http://example.com';

        $this->assertEquals("hello &#39;<a href=\"{$url}\">{$url}</a>&#39; hello", $this->autolink->convert("hello &#39;{$url}&#39; hello"));
    }

    public function test_autolink_with_brackets(): void
    {
        $url1 = 'http://en.wikipedia.org/wiki/Sprite_(computer_graphics)';
        $link1 = $this->generateLink($url1);

        $this->assertEquals($link1, $this->autolink->convert($url1));
        $this->assertEquals("(link: {$link1})", $this->autolink->convert("(link: {$url1})"));

        $url2 = 'http://en.wikipedia.org/wiki/Sprite_[computer_graphics]';
        $link2 = $this->generateLink($url2);

        $this->assertEquals($link2, $this->autolink->convert($url2));
        $this->assertEquals("[link: {$link2}]", $this->autolink->convert("[link: {$url2}]"));

        $url3 = 'http://en.wikipedia.org/wiki/Sprite_{computer_graphics}';
        $link3 = $this->generateLink($url3);

        $this->assertEquals($link3, $this->autolink->convert($url3));
        $this->assertEquals("{link: {$link3}}", $this->autolink->convert("{link: {$url3}}"));
    }

    public function test_autolink_with_multiple_links(): void
    {
        $url = 'http://example.com<br>http://example.com<br>http://example.com';
        $link = $this->generateLink($url);

        $this->assertEquals('<a href="http://example.com">http://example.com</a><br><a href="http://example.com">http://example.com</a><br><a href="http://example.com">http://example.com</a>', $this->autolink->convert($url));
    }

    public function test_autolink_with_multiple_trailing_punctuations(): void
    {
        $url = 'http://example.com';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));
        $this->assertEquals("(link: {$link}).", $this->autolink->convert("(link: {$url})."));
    }

    public function test_autolink_with_already_linked(): void
    {
        $link1 = $this->generateLink('Example', 'http://www.example.com');
        $link2 = '<a href="http://www.example.com">www.example.com</a>';
        $link3 = '<a href="http://www.example.com" rel="nofollow">www.example.com</a>';
        $link4 = '<a href="http://www.example.com"><b>www.example.com</b></a>';
        $link5 = '<a href="#close">close</a> <a href="http://www.example.com"><b>www.example.com</b></a>';

        $this->assertEquals($link1, $this->autolink->convert($link1));
        $this->assertEquals($link2, $this->autolink->convert($link2));
        $this->assertEquals($link3, $this->autolink->convert($link3));
        $this->assertEquals($link4, $this->autolink->convert($link4));
        $this->assertEquals($link5, $this->autolink->convert($link5));

        $email = '<a href="mailto:example@example.com">Mail me</a>';
        $this->assertEquals($email, $this->autolink->convert($email));
    }

    public function test_autolink_at_eol(): void
    {
        $url1 = 'http://example.com/foo.html';
        $url2 = 'http://example.com/bar.html';

        $this->assertEquals("<p><a href=\"{$url1}\">{$url1}</a><br /><a href=\"{$url2}\">{$url2}</a><br /></p>", $this->autolink->convert("<p>{$url1}<br />{$url2}<br /></p>"));
    }

    public function test_callback(): void
    {
        $link = (string) $this->autolink->convert('Find ur favorite pokeman @ http://www.example.com', function ($element) {
            $this->assertInstanceOf(Element::class, $element);

            $this->assertEquals('http://www.example.com', $element->getTitle());
            $this->assertEquals('http://www.example.com', $element->getUrl());

            $element->setTitle('POKEMAN WEBSITE');

            return $element;
        });

        $this->assertEquals('Find ur favorite pokeman @ <a href="http://www.example.com">POKEMAN WEBSITE</a>', $link);
    }

    public function test_autolink_works(): void
    {
        $url = 'http://example.com/';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));
    }

    public function test_not_autolink_www(): void
    {
        $this->assertEquals('Awww... man', $this->autolink->convert('Awww... man'));
    }

    public function test_does_not_terminate_on_dash(): void
    {
        $url = 'http://example.com/Notification_Center-GitHub-20101108-140050.jpg';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));
    }

    public function test_does_not_include_trailing_gt(): void
    {
        $url = 'http://example.com';
        $link = $this->generateLink($url);

        $this->assertEquals('&lt;'.$link.'&gt;', $this->autolink->convert('&lt;'.$url.'&gt;'));
    }

    public function test_links_with_anchors(): void
    {
        $url = 'https://github.com/github/hubot/blob/master/scripts/cream.js#L20-20';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));
    }

    public function test_polish_wikipedia_haha(): void
    {
        $url = 'https://pl.wikipedia.org/wiki/Komisja_śledcza_do_zbadania_sprawy_zarzutu_nielegalnego_wywierania_wpływu_na_funkcjonariuszy_policji,_służb_specjalnych,_prokuratorów_i_osoby_pełniące_funkcje_w_organach_wymiaru_sprawiedliwości';

        $input = "A wikipedia link ({$url})";

        $expected = "A wikipedia link (<a href=\"{$this->e($url)}\">{$this->e($url)}</a>)";

        $this->assertEquals($expected, (string) $this->autolink->convert($input));
    }

    public function test_the_famous_nbsp(): void
    {
        $url = 'http://google.com/';

        $input = "at {$url}\u{00A0};";

        $expected = "at <a href=\"{$url}\">{$url}</a>\u{00A0};";

        $this->assertEquals($expected, $this->autolink->convert($input));
    }

    public function test_does_not_include_trailing_nonbreaking_spaces(): void
    {
        $url = 'http://example.com/';
        $link = $this->generateLink($url);

        $this->assertEquals("{$link}\xC2\xA0and", $this->autolink->convert("{$url}\xC2\xA0and"));
    }

    public function test_identifies_preceeding_nonbreaking_spaces(): void
    {
        $url = 'http://example.com/';
        $link = $this->generateLink($url);

        $this->assertEquals("\xC2\xA0{$link} and", $this->autolink->convert("\xC2\xA0{$url} and"));
    }

    public function test_urls_with2_wide_ut_f8_characters(): void
    {
        $url = 'http://example.com/?foo=¥&bar=1';
        $link = $this->generateLink($url);

        $this->assertEquals("{$link} and", (string) $this->autolink->convert("{$url} and"));
    }

    public function test_urls_with4_wide_ut_f8_characters(): void
    {
        $url = 'http://example.com/?foo=&bar=1';
        $link = $this->generateLink($url);

        $this->assertEquals("{$link} and", $this->autolink->convert("{$url} and"));
    }

    public function test_handles_urls_with_emoji_properly(): void
    {
        $url = 'http://foo.com/💖a';
        $link = $this->generateLink($url);

        $this->assertEquals("{$link} and", $this->autolink->convert("{$url} and"));
    }

    public function test_identifies_nonbreaking_spaces_preceeding_emails(): void
    {
        $email = 'foo@example.com';
        $link = $this->generateLink($email, "mailto:{$email}");

        $this->assertEquals("email\xC2\xA0{$link}", $this->autolink->convert("email\xC2\xA0{$email}"));
    }

    public function test_identifies_unicode_spaces(): void
    {
        $this->assertEquals(
            "This is just a test. <a href=\"http://www.example.com\">http://www.example.com</a>\u{202F}\u{2028}\u{2001}",
            $this->autolink->convert("This is just a test. http://www.example.com\u{202F}\u{2028}\u{2001}")
        );
    }

    public function test_www_is_case_insensitive(): void
    {
        $url = 'www.reddit.com';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));

        $url = 'WWW.REDDIT.COM';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));

        $url = 'Www.reddit.Com';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));

        $url = 'WwW.reddit.CoM';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));
    }

    public function test_non_emails_ending_in_periods(): void
    {
        $this->assertEquals('abc/def@ghi.', $this->autolink->convert('abc/def@ghi.'));
        $this->assertEquals('abc/def@ghi. ', $this->autolink->convert('abc/def@ghi. '));
        $this->assertEquals('abc/def@ghi. x', $this->autolink->convert('abc/def@ghi. x'));
        $this->assertEquals('abc/def@ghi.< x', $this->autolink->convert('abc/def@ghi.< x'));
        $this->assertEquals('abc/<a href="mailto:def@ghi.x">def@ghi.x</a>', $this->autolink->convert('abc/def@ghi.x'));
        $this->assertEquals('abc/<a href="mailto:def@ghi.x">def@ghi.x</a>. a', $this->autolink->convert('abc/def@ghi.x. a'));
    }

    public function test_urls_with_entities_and_parens(): void
    {
        $this->assertEquals('&lt;<a href="http://www.google.com">http://www.google.com</a>&gt;', $this->autolink->convert('&lt;http://www.google.com&gt;'));
        $this->assertEquals('&lt;<a href="http://www.google.com">http://www.google.com</a>&gt;)', $this->autolink->convert('&lt;http://www.google.com&gt;)'));

        $url = 'http://example.com/bulbasaur';
        $link = $this->generateLink($url);

        $this->assertEquals("URL is {$link}.", $this->autolink->convert("URL is {$url}."));
        $this->assertEquals("(URL is {$link}.)", $this->autolink->convert("(URL is {$url}.)"));

        $url = 'www.example.com/bulbasaur';
        $link = $this->generateLink($url);

        $this->assertEquals("URL is {$link}.", $this->autolink->convert("URL is {$url}."));
        $this->assertEquals("(URL is {$link}.)", $this->autolink->convert("(URL is {$url}.)"));

        $url = 'abc@xyz.com';
        $link = $this->generateLink($url, "mailto:{$url}");

        $this->assertEquals("URL is {$link}.", $this->autolink->convert("URL is {$url}."));
        $this->assertEquals("(URL is {$link}.)", $this->autolink->convert("(URL is {$url}.)"));
    }

    public function test_urls_with_parens(): void
    {
        $this->assertEquals('(<a href="http://example.com">http://example.com</a>)', $this->autolink->convert('(http://example.com)'));
        $this->assertEquals('((<a href="http://example.com/()">http://example.com/()</a>))', $this->autolink->convert('((http://example.com/()))'));
        $this->assertEquals('[<a href="http://example.com/()">http://example.com/()</a>]', $this->autolink->convert('[http://example.com/()]'));

        $this->assertEquals('（<a href="http://example.com/">http://example.com/</a>）', $this->autolink->convert('（http://example.com/）'));
        $this->assertEquals('【<a href="http://example.com/">http://example.com/</a>】', $this->autolink->convert('【http://example.com/】'));
        $this->assertEquals('『<a href="http://example.com/">http://example.com/</a>』', $this->autolink->convert('『http://example.com/』'));
        $this->assertEquals('「<a href="http://example.com/">http://example.com/</a>」', $this->autolink->convert('「http://example.com/」'));
        $this->assertEquals('《<a href="http://example.com/">http://example.com/</a>》', $this->autolink->convert('《http://example.com/》'));
        $this->assertEquals('〈<a href="http://example.com/">http://example.com/</a>〉', $this->autolink->convert('〈http://example.com/〉'));
    }

    public function test_urls_with_quotes(): void
    {
        $this->assertEquals("'<a href=\"http://example.com\">http://example.com</a>'", $this->autolink->convert("'http://example.com'"));
        $this->assertEquals('"<a href="http://example.com">http://example.com</a>"', $this->autolink->convert('"http://example.com"'));
    }
}
