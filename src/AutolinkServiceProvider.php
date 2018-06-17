<?php

declare(strict_types=1);

namespace OsiemSiedem\Autolink;

use Illuminate\Support\ServiceProvider;

class AutolinkServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/autolink.php', 'autolink'
        );

        $this->publishes([
            __DIR__.'/../config/autolink.php' => config_path('autolink.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('osiemsiedem.autolink.parser', function ($app) {
            $config = $app['config']->get('autolink');

            $parser = new Parser;

            $parser->setIgnoredTags($config['ignored_tags']);

            foreach ($config['parsers'] as $elementParser) {
                $parser->addElementParser(new $elementParser);
            }

            return $parser;
        });

        $this->app->singleton('osiemsiedem.autolink.renderer', function ($app) {
            $renderer = new HtmlRenderer;

            foreach ($app['config']->get('autolink.filters') as $filter) {
                $renderer->addFilter(new $filter);
            }

            return $renderer;
        });

        $this->app->singleton('osiemsiedem.autolink', function ($app) {
            return new Autolink($app['osiemsiedem.autolink.parser'], $app['osiemsiedem.autolink.renderer']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'osiemsiedem.autolink.parser',
            'osiemsiedem.autolink.renderer',
            'osiemsiedem.autolink',
        ];
    }
}
