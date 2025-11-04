<?php

namespace App\Providers;

use App\Models\Article;
use App\Observers\ArticleObserver;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use GuzzleHttp\Client as GuzzleClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Article::observe(ArticleObserver::class);
        // $this->app->bind(ClientInterface::class, function ($app) {
        //     return new GuzzleClient([
        //         // you can configure default options here
        //     ]);
        // });
    }
}
