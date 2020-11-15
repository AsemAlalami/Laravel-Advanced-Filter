<?php


namespace AsemAlalami\LaravelAdvancedFilter;


use Illuminate\Support\ServiceProvider;

class AdvancedFilterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/advanced_filter.php' => config_path('advanced_filter.php'),
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/advanced_filter.php', 'advanced_filter'
        );
    }
}
