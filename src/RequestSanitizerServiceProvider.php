<?php

namespace ArondeParon\RequestSanitizer;

use Illuminate\Support\ServiceProvider;

class RequestSanitizerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/sanitizer.php' => config_path('sanitizer.php'),
        ], 'config');

    }
}



