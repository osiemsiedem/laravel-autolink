<?php

declare(strict_types=1);

namespace OsiemSiedem\Tests\Autolink;

use OsiemSiedem\Autolink\Cursor;
use OsiemSiedem\Tests\Autolink\TestCase;

final class CursorTest extends TestCase
{
    public function testMatch(): void
    {
        $cursor = new Cursor('some random text http://example.com lorem ipsum');

        $this->assertTrue($cursor->match('#^some#i'));
        $this->assertFalse($cursor->match('#^http://#i'));
        $this->assertEquals(0, $cursor->getPosition());

        $cursor->next(17);

        $this->assertFalse($cursor->match('#^some#i'));
        $this->assertTrue($cursor->match('#http://#i'));
        $this->assertEquals(17, $cursor->getPosition());
    }

    public function testGettersAndSetters(): void
    {
        $cursor = new Cursor('Lorem ipsum dolor sit amet 😅');

        $this->assertEquals('UTF-8', $cursor->getEncoding());
        $this->assertEquals(28, $cursor->getLength());

        $this->assertEquals('Lorem ipsum dolor sit amet 😅', $cursor->getText());
        $this->assertEquals('ipsum dolor sit amet 😅', $cursor->getText(6));
        $this->assertEquals('ipsum', $cursor->getText(6, 5));

        $state = $cursor->getState();

        $this->assertArrayHasKey('position', $state);
        $this->assertEquals(0, $state['position']);
        $this->assertEquals('L', $cursor->getCharacter());

        $state['position'] = 10;
        $cursor->setState($state);

        $state = $cursor->getState($state);

        $this->assertArrayHasKey('position', $state);
        $this->assertEquals(10, $state['position']);
        $this->assertEquals('m', $cursor->getCharacter());
    }

    public function testIterator(): void
    {
        $cursor = new Cursor('Lorem ipsum dolor sit amet, consectetur adipiscing elit');

        $this->assertInstanceOf('Iterator', $cursor);

        $this->assertTrue($cursor->valid());
        $this->assertEquals(0, $cursor->key());
        $this->assertEquals(0, $cursor->getPosition());
        $this->assertEquals('L', $cursor->current());
        $this->assertEquals('L', $cursor->getCharacter());

        $cursor->next();

        $this->assertTrue($cursor->valid());
        $this->assertEquals(1, $cursor->key());
        $this->assertEquals(1, $cursor->getPosition());
        $this->assertEquals('o', $cursor->current());
        $this->assertEquals('o', $cursor->getCharacter());

        $cursor->next(2);

        $this->assertTrue($cursor->valid());
        $this->assertEquals(3, $cursor->key());
        $this->assertEquals(3, $cursor->getPosition());
        $this->assertEquals('e', $cursor->current());
        $this->assertEquals('e', $cursor->getCharacter());

        $cursor->prev();

        $this->assertTrue($cursor->valid());
        $this->assertEquals(2, $cursor->key());
        $this->assertEquals(2, $cursor->getPosition());
        $this->assertEquals('r', $cursor->current());
        $this->assertEquals('r', $cursor->getCharacter());

        $cursor->rewind();

        $this->assertTrue($cursor->valid());
        $this->assertEquals(0, $cursor->key());
        $this->assertEquals(0, $cursor->getPosition());
        $this->assertEquals('L', $cursor->current());
        $this->assertEquals('L', $cursor->getCharacter());

        $cursor->next(1000);

        $this->assertFalse($cursor->valid());
        $this->assertEquals(1000, $cursor->key());
        $this->assertEquals(1000, $cursor->getPosition());
        $this->assertEquals(null, $cursor->current());
        $this->assertEquals(null, $cursor->getCharacter());
    }
}
