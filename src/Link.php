<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink;

use Illuminate\Contracts\Support\Htmlable;

class Link implements Htmlable
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
     * @var array
     */
    protected $attributes;

    /**
     * @var int
     */
    protected $start;

    /**
     * @var int
     */
    protected $end;

    /**
     * Create a new instance.
     *
     * @param  string  $title
     * @param  string  $url
     * @param  array  $attributes
     * @return void
     */
    public function __construct(string $title, string $url, array $attributes = [], int $start, int $end)
    {
        $this->title = $title;
        $this->url = $url;
        $this->attributes = $attributes;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml(): string
    {
        $title = e($this->title);

        $attributes = collect($this->attributes)
            ->merge([
                'href' => $this->url,
            ])
            ->map(function ($value, $key) {
                if (is_null($value)) {
                    return $key;
                }

                return $key.'="'.e($value).'"';
            })
            ->implode(' ');

        return "<a {$attributes}>{$title}</a>";
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
     * Get the attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set the attributes.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

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
}
