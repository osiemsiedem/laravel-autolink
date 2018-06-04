<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink;

use OsiemSiedem\Autolink\Link;
use OsiemSiedem\Autolink\Autolink;
use OsiemSiedem\Tests\Autolink\TestCase;
use OsiemSiedem\Autolink\Parsers\UrlParser;
use OsiemSiedem\Autolink\Parsers\WwwParser;
use OsiemSiedem\Autolink\Parsers\EmailParser;
use OsiemSiedem\Autolink\Filters\LimitLengthFilter;

final class AutolinkTest extends TestCase
{
    private $autolink;

    public function setUp(): void
    {
        $this->autolink = new Autolink;

        $this->autolink->addParser(new UrlParser);
        $this->autolink->addParser(new WwwParser);
        $this->autolink->addParser(new EmailParser);
    }

    public function tearDown(): void
    {
        $this->autolink = null;
    }

    private function generateLink(string $title, string $href = null): string
    {
        if (is_null($href)) {
            $href = $title;
        }

        if ( ! starts_with($href, ['http', 'https', 'mailto'])) {
            $href = 'http://'.$href;
        }

        return '<a href="'.e($href).'">'.e($title).'</a>';
    }

    public function testEscapesQuotes(): void
    {
        $this->assertEquals('<a href="http://example.com/&quot;onmouseover=document.body.style.backgroundColor=&quot;pink&quot;;//">http://example.com/&quot;onmouseover=document.body.style.backgroundColor=&quot;pink&quot;;//</a>', $this->autolink->convert('http://example.com/"onmouseover=document.body.style.backgroundColor="pink";//'));
    }

    public function testAutolinkWithSingleTrailingPunctuationAndSpace(): void
    {
        $url = 'http://example.com';
        $link = $this->generateLink('http://example.com');

        $this->assertEquals($link, $this->autolink->convert('http://example.com'));

        foreach (['?', '!', '.', ',', ':'] as $punc) {
            $this->assertEquals("link: {$link}{$punc} foo?", $this->autolink->convert("link: {$url}{$punc} foo?"));
        }
    }

    public function testTerminatesOnAmpersand(): void
    {
        $url = 'http://example.com';

        $this->assertEquals("hello &#39;<a href=\"{$url}\">{$url}</a>&#39; hello", $this->autolink->convert("hello &#39;{$url}&#39; hello"));
    }

    public function testAutolinkWithBrackets(): void
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

    public function testAutolinkWithMultipleTrailingPunctuations(): void
    {
        $url = 'http://example.com';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));
        $this->assertEquals("(link: {$link}).", $this->autolink->convert("(link: {$url})."));
    }

    public function testAutolinkWithAlreadyLinked(): void
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

    public function testAutolinkAtEol(): void
    {
        $url1 = 'http://example.com/foo.html';
        $url2 = 'http://example.com/bar.html';

        $this->assertEquals("<p><a href=\"{$url1}\">{$url1}</a><br /><a href=\"{$url2}\">{$url2}</a><br /></p>", $this->autolink->convert("<p>{$url1}<br />{$url2}<br /></p>"));
    }

    public function testCallback(): void
    {
        $link = $this->autolink->convert('Find ur favorite pokeman @ http://www.example.com', function ($link) {
            $this->assertInstanceOf(Link::class, $link);

            $this->assertEquals('http://www.example.com', $link->getTitle());
            $this->assertEquals('http://www.example.com', $link->getUrl());

            $link->setTitle('POKEMAN WEBSITE');

            return $link;
        });

        $this->assertEquals('Find ur favorite pokeman @ <a href="http://www.example.com">POKEMAN WEBSITE</a>', $link);
    }

    public function testAutolinkWorks(): void
    {
        $url = 'http://example.com/';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));
    }

    public function testNotAutolinkWww(): void
    {
        $this->assertEquals('Awww... man', $this->autolink->convert('Awww... man'));
    }

    public function testDoesNotTerminateOnDash(): void
    {
        $url = 'http://example.com/Notification_Center-GitHub-20101108-140050.jpg';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));
    }

    public function testDoesNotIncludeTrailingGt(): void
    {
        $url = 'http://example.com';
        $link = $this->generateLink($url);

        $this->assertEquals('&lt;'.$link.'&gt;', $this->autolink->convert('&lt;'.$url.'&gt;'));
    }

    public function testLinksWithAnchors(): void
    {
        $url = 'https://github.com/github/hubot/blob/master/scripts/cream.js#L20-20';
        $link = $this->generateLink($url);

        $this->assertEquals($link, $this->autolink->convert($url));
    }

    public function testPolishWikipediaHaha(): void
    {
        $url = 'https://pl.wikipedia.org/wiki/Komisja_śledcza_do_zbadania_sprawy_zarzutu_nielegalnego_wywierania_wpływu_na_funkcjonariuszy_policji,_służb_specjalnych,_prokuratorów_i_osoby_pełniące_funkcje_w_organach_wymiaru_sprawiedliwości';

        $input = "A wikipedia link ({$url})";

        $expected = "A wikipedia link (<a href=\"{$url}\">{$url}</a>)";

        $this->assertEquals($expected, $this->autolink->convert($input));
    }

    public function testTheFamousNbsp(): void
    {
        $url = 'http://google.com/';

        $input = "at {$url}\u{00A0};";

        $expected = "at <a href=\"{$url}\">{$url}</a>\u{00A0};";

        $this->assertEquals($expected, $this->autolink->convert($input));
    }

    public function testDoesNotIncludeTrailingNonbreakingSpaces(): void
    {
        $url = 'http://example.com/';
        $link = $this->generateLink($url);

        $this->assertEquals("{$link}\xC2\xA0and", $this->autolink->convert("{$url}\xC2\xA0and"));
    }

    public function testIdentifiesPreceedingNonbreakingSpaces(): void
    {
        $url = 'http://example.com/';
        $link = $this->generateLink($url);

        $this->assertEquals("\xC2\xA0{$link} and", $this->autolink->convert("\xC2\xA0{$url} and"));
    }

    public function testUrlsWith2WideUTF8Characters(): void
    {
        $url = 'http://example.com/?foo=¥&bar=1';
        $link = $this->generateLink($url);

        $this->assertEquals("{$link} and", $this->autolink->convert("{$url} and"));
    }

    public function testUrlsWith4WideUTF8Characters(): void
    {
        $url = 'http://example.com/?foo=&bar=1';
        $link = $this->generateLink($url);

        $this->assertEquals("{$link} and", $this->autolink->convert("{$url} and"));
    }

    public function testHandlesUrlsWithEmojiProperly(): void
    {
        $url = 'http://foo.com/💖a';
        $link = $this->generateLink($url);

        $this->assertEquals("{$link} and", $this->autolink->convert("{$url} and"));
    }

    public function testIdentifiesNonbreakingSpacesPreceedingEmails(): void
    {
        $email = 'foo@example.com';
        $link = $this->generateLink($email, "mailto:{$email}");

        $this->assertEquals("email\xC2\xA0{$link}", $this->autolink->convert("email\xC2\xA0{$email}"));
    }

    public function testIdentifiesUnicodeSpaces(): void
    {
        $this->assertEquals(
            "This is just a test. <a href=\"http://www.example.com\">http://www.example.com</a>\u{202F}\u{2028}\u{2001}",
            $this->autolink->convert("This is just a test. http://www.example.com\u{202F}\u{2028}\u{2001}")
        );
    }

    public function testWwwIsCaseInsensitive(): void
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

    public function testNonEmailsEndingInPeriods(): void
    {
        $this->assertEquals('abc/def@ghi.', $this->autolink->convert('abc/def@ghi.'));
        $this->assertEquals('abc/def@ghi. ', $this->autolink->convert('abc/def@ghi. '));
        $this->assertEquals('abc/def@ghi. x', $this->autolink->convert('abc/def@ghi. x'));
        $this->assertEquals('abc/def@ghi.< x', $this->autolink->convert('abc/def@ghi.< x'));
        $this->assertEquals('abc/<a href="mailto:def@ghi.x">def@ghi.x</a>', $this->autolink->convert('abc/def@ghi.x'));
        $this->assertEquals('abc/<a href="mailto:def@ghi.x">def@ghi.x</a>. a', $this->autolink->convert('abc/def@ghi.x. a'));
    }

    public function testUrlsWithEntitiesAndParens(): void
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

    public function testUrlsWithParens(): void
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

    public function testUrlsWithQuotes(): void
    {
        $this->assertEquals("'<a href=\"http://example.com\">http://example.com</a>'", $this->autolink->convert("'http://example.com'"));
        $this->assertEquals('"<a href="http://example.com">http://example.com</a>"', $this->autolink->convert('"http://example.com"'));
    }

    public function testAddFilter(): void
    {
        $url = 'http://example.com/some/very/long/link?foo=bar';

        $autolink = new Autolink;
        $autolink->addParser(new UrlParser);

        $autolink->convert($url, function ($link) {
            $this->assertEquals('http://example.com/some/very/long/link?foo=bar', $link->getTitle());

            return $link;
        });

        $autolink->addFilter(new LimitLengthFilter);

        $autolink->convert($url, function ($link) {
            $this->assertEquals('http://example.com/some/very/l...', $link->getTitle());

            return $link;
        });
    }

    public function testIgnore(): void
    {
        $this->autolink->ignore(['a']);

        $this->assertEquals('<a>http://example.com', $this->autolink->convert('<a>http://example.com'));
        $this->assertEquals('<b><a href="http://example.com">http://example.com</a></b> <a>http://example.com</a>', $this->autolink->convert('<b>http://example.com</b> <a>http://example.com</a>'));
    }
}
