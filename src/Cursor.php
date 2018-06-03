<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink;

use Iterator;

class Cursor implements Iterator
{
    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $encoding;

    /**
     * @var int
     */
    protected $length = 0;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * Create a new instance.
     *
     * @param  string  $text
     * @param  string  $encoding
     * @return void
     */
    public function __construct(string $text, string $encoding = 'UTF-8')
    {
        $this->text = $text;
        $this->encoding = $encoding;
        $this->length = mb_strlen($text, $encoding);
    }

    /**
     * Get the current character.
     *
     * @return string|null
     */
    public function current(): ?string
    {
        return $this->getCharacter();
    }

    /**
     * Get the key of the current element.
     *
     * @return int
     */
    public function key(): int
    {
        return $this->getPosition();
    }

    /**
     * Move forward to the next character.
     *
     * @param  int  $offset
     * @return void
     */
    public function next(int $offset = 1): void
    {
        $this->position = $this->position + $offset;
    }

    /**
     * Move backward to the previous character.
     *
     * @param  int  $offset
     * @return void
     */
    public function prev(int $offset = 1): void
    {
        $this->position = $this->position - $offset;
    }

    /**
     * Rewind to the first character.
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Check if the current position is valid.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->position >= 0 && $this->position < $this->length;
    }

    /**
     * Get the state.
     *
     * @return array
     */
    public function getState(): array
    {
        return ['position' => $this->position];
    }

    /**
     * Save the state.
     *
     * @param  array  $state
     * @return void
     */
    public function setState(array $state): void
    {
        $this->position = array_get($state, 'position', 0);
    }

    /**
     * Check if the given pattern is matched.
     *
     * @param  string  $pattern
     * @return bool
     */
    public function match(string $pattern): bool
    {
        $text = $this->getText($this->getPosition());

        return preg_match($pattern, $text) > 0;
    }

    /**
     * Get the character.
     *
     * @param  int  $position
     * @return string|null
     */
    public function getCharacter(int $position = null): ?string
    {
        if ($position === null) {
            $position = $this->getPosition();
        }

        if (isset($this->cache[$position])) {
            return $this->cache[$position];
        }

        if ($position < 0 || $position >= $this->getLength()) {
            return null;
        }

        return $this->cache[$position] = $this->getText($position, 1);
    }

    /**
     * Get the text.
     *
     * @param  int|null  $start
     * @param  int|null  $length
     * @return string
     */
    public function getText(?int $start = null, ?int $length = null): string
    {
        if (is_null($start)) {
            $start = 0;
        }

        return mb_substr($this->text, $start, $length, $this->getEncoding());
    }

    /**
     * Get the encoding.
     *
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Get the length.
     *
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Get the position.
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}
