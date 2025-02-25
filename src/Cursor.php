<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink;

use Illuminate\Support\Arr;
use Iterator;

class Cursor implements Iterator
{
    protected string $text;

    protected string $encoding;

    protected int $length = 0;

    protected int $position = 0;

    /**
     * @var string[]
     */
    protected array $cache = [];

    /**
     * Create a new instance.
     */
    public function __construct(string $text, string $encoding = 'UTF-8')
    {
        $this->text = $text;
        $this->encoding = $encoding;
        $this->length = mb_strlen($text, $encoding);
    }

    /**
     * Get the current character.
     */
    public function current(): ?string
    {
        return $this->getCharacter();
    }

    /**
     * Get the key of the current element.
     */
    public function key(): int
    {
        return $this->getPosition();
    }

    /**
     * Move forward to the next character.
     */
    public function next(int $offset = 1): void
    {
        $this->position = $this->position + $offset;
    }

    /**
     * Move backward to the previous character.
     */
    public function prev(int $offset = 1): void
    {
        $this->position = $this->position - $offset;
    }

    /**
     * Rewind to the first character.
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Check if the current position is valid.
     */
    public function valid(): bool
    {
        return $this->position >= 0 && $this->position < $this->length;
    }

    /**
     * Get the state.
     */
    public function getState(): array
    {
        return ['position' => $this->position];
    }

    /**
     * Save the state.
     */
    public function setState(array $state): void
    {
        $this->position = Arr::get($state, 'position', 0);
    }

    /**
     * Check if the given pattern is matched.
     */
    public function match(string $pattern): bool
    {
        $text = $this->getText($this->getPosition());

        return preg_match($pattern, $text) > 0;
    }

    /**
     * Get the character.
     */
    public function getCharacter(?int $position = null): ?string
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
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Get the length.
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Get the position.
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}
