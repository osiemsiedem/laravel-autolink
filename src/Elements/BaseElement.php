<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink\Elements;

use Spatie\Html\Attributes;
use OsiemSiedem\Autolink\Contracts\Element;

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
     * @param  string  $title
     * @param  string  $url
     * @param  int  $start
     * @param  int  $end
     * @param  array  $attributes
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
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title.
     *
     * @param  string  $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set the url.
     *
     * @param  string  $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the start position.
     *
     * @return int
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * Get the end position.
     *
     * @return int
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * Get the attributes.
     *
     * @return \Spatie\Html\Attributes
     */
    public function getAttributes(): Attributes
    {
        return $this->attributes;
    }
}
