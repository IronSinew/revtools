<?php

namespace App\Providers;

use App\Enums\BannerTypeEnum;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Model::preventLazyLoading(! app()->isProduction());
        Model::preventAccessingMissingAttributes(! app()->isProduction());

        // @codeCoverageIgnoreStart
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }
        // @codeCoverageIgnoreEnd

        // @codeCoverageIgnoreStart
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }
        // @codeCoverageIgnoreEnd

        RedirectResponse::macro('withBanner', function (string $message, ?BannerTypeEnum $bannerType = BannerTypeEnum::success) {
            return $this->with('flash', [
                'bannerStyle' => $bannerType,
                'banner' => $message,
            ]);
        });

        \Vite::prefetch(concurrency: 3, event: 'vite:prefetch');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
