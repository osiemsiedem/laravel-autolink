<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Elements;

use OsiemSiedem\Autolink\Contracts\Element;
use Spatie\Html\Attributes;

class BaseElement implements Element
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $start;

    /**
     * @var int
     */
    protected $end;

    /**
     * @var \Spatie\Html\Attributes
     */
    protected $attributes;

    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(string $title, string $url, int $start, int $end, array $attributes = [])
    {
        $this->title = $title;
        $this->url = $url;
        $this->start = $start;
        $this->end = $end;
        $this->attributes = (new Attributes)->setAttributes($attributes);
    }

    /**
     * Get the title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title.
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the url.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set the url.
     *
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the start position.
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * Get the end position.
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * Get the attributes.
     */
    public function getAttributes(): Attributes
    {
        return $this->attributes;
    }
}
