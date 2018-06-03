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
        $this->app->singleton('osiemsiedem.autolink', function ($app) {
            $config = $app['config']->get('autolink');

            $autolink = new Autolink;

            $autolink->ignore($config['ignored']);

            foreach ($config['filters'] as $filter) {
                $autolink->addFilter(new $filter);
            }

            foreach ($config['parsers'] as $parser) {
                $autolink->addParser(new $parser);
            }

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
