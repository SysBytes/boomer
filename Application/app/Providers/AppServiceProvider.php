<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind('path.public', function () {
            return base_path('../');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Usamos una ruta relativa simple. 
        // Como el punto de entrada es index.php en la raíz, 
        // solo debe buscar la carpeta build que está ahí mismo.
        Vite::useBuildDirectory('build');
    }
}
