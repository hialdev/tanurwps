<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $primary_color = setting('theme.primary-color') ?? '#926e38';
        $primary_rgba = setting('theme.primary-rgba') ?? '163, 118, 78';
        $bg_subtle = setting('theme.bg-subtle') ?? '#926e380c';
        $btn_bg = setting('theme.btn-bg') ?? '#926e38';
        $btn_border = setting('theme.btn-border') ?? '#926e38';
        $btn_hover_bg = setting('theme.btn-hover-bg') ?? '#a47d42';
        $btn_hover_border = setting('theme.btn-hover-border') ?? '#a47d42';
        
        //dd($primary_color, $primary_rgba, $btn_bg);
        Config::set('al.theme.primary_color', $primary_color);
        Config::set('al.theme.primary_rgba', $primary_rgba);
        Config::set('al.theme.bg_subtle', $bg_subtle);
        Config::set('al.theme.btn_bg', $btn_bg);
        Config::set('al.theme.btn_border', $btn_border);
        Config::set('al.theme.btn_hover_bg', $btn_hover_bg);
        Config::set('al.theme.btn_hover_border', $btn_hover_border);
        //dd(config('al.theme.btn_bg'));
    }
}
