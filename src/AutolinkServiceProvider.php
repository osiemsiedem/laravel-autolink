<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink;

use Illuminate\Support\ServiceProvider;

use OsiemSiedem\Autolink\Parsers\UrlParser;
use OsiemSiedem\Autolink\Parsers\WwwParser;
use OsiemSiedem\Autolink\Filters\TrimFilter;
use OsiemSiedem\Autolink\Parsers\EmailParser;
use OsiemSiedem\Autolink\Filters\LimitLengthFilter;

class AutolinkServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('osiemsiedem.autolink', function ($app) {
            $autolink = new Autolink;

            $autolink->addFilter(new TrimFilter);
            $autolink->addFilter(new LimitLengthFilter);

            $autolink->addParser(new UrlParser);
            $autolink->addParser(new WwwParser);
            $autolink->addParser(new EmailParser);

            return $autolink;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['osiemsiedem.autolink'];
    }
}
