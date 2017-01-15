<?php

namespace Marcop93\Webisan;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class WebisanServiceProvider extends ServiceProvider
{
    public function boot() {
        $nameSpace = $this->app->getNamespace();
        AliasLoader::getInstance()->alias('AppController', $nameSpace);

        if (!config('webisan.customRoutes') || is_null(config('webisan.customRoutes'))) {
            $this->app->router->group(['middleware' => ['web'], 'namespace' => $nameSpace], function () {
                require __DIR__ . '/routes.php';
            });
        }

        $this->loadViewsFrom(__DIR__ . '/views', 'Webisan');
        $this->publishes([
            __DIR__ . '/config/webisan.php' => config_path('webisan.php'),
        ], 'webisan:webisan');
    }

    public function register() {
        $this->mergeConfigFrom(__DIR__ . "/config/webisan.php", 'webisan');
    }
}
