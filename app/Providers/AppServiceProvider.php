<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
        Model::preventLazyLoading(!app()->isProduction());

        if (env('DUMP_QUERY')) {
            \DB::listen(function ($query) {
                $sql = \Illuminate\Support\Str::replaceArray('?', $query->bindings, $query->sql);
                logger('sql: ' . var_export($sql, true));
                // $query->sql;
                // $query->bindings;
                // $query->time;
            });
        }
    }
}
