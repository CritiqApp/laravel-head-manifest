<?php

namespace Critiq\LaravelHeadManifest;

use Critiq\LaravelHeadManifest\Util\Manifest;
use Illuminate\Support\ServiceProvider;

class LaravelHeadManifestServiceProvider extends ServiceProvider {

    /**
     * Load the head manifest
     *
     * @return void
     */
    public function register() {
        
        // Register the manifest as a singleton as it does not 
        $this->app->singleton(Manifest::class, static function($app) {

            // Load the manifest. If this fails, treat it as an empty manifest
            try {
                $manifest = file_get_contents(base_path() . env('LARAVEL_HEAD_MANIFEST_PATH', '/public/head-manifest.json'));
                $json = json_decode($manifest, true);
            } catch(\Exception $e) {
                // Log a warning?
            }

            // If JSON isn't set, make it an empty array
            if(!isset($json)) {
                $json = [];
            }

            // Construct the manifest singleton
            return new Manifest($json);
        });
        
    }

    /**
     * Register the view composer so it includes the manifest data
     */
    public function boot() {
        view()->composer(env('LARAVEL_HEAD_MANIFEST_VIEWS', '*'), function($view) {
            /** @var Manifest */
            $manifest = app()->make(Manifest::class);
            $path = $manifest->resolvePath(request()->path());
            $view->with(env('LARAVEL_HEAD_MANIFEST_DATA_VARIABLE', 'metadata'), $path->toHTML());
        });
    }

}