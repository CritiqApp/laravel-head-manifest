<?php

namespace Critiq\LaravelHeadManifest;

use Illuminate\Support\ServiceProvider;

class LaravelHeadManifestServiceProvider extends ServiceProvider
{

    /**
     * Load the head manifest
     *
     * @return void
     */
    public function boot()
    {
        // Specify the config key for the manifest once it's loaded
        $configKey = env('LARAVEL_HEAD_MANIFEST_CONFIG_KEY', 'laravel_head_manifest');

        // Load the manifest. If this fails, treat it as an empty manifest
        try {
            $manifest = file_get_contents(base_path() . env('LARAVEL_HEAD_MANIFEST_PATH', '/public/head-manifest.json'));
            $json = json_decode($manifest, true);
        } catch(\Exception $e) {
            config($configKey, []);
            return; // Do nothing, perhaps log a warning?
        }
        
        // Set the manifest as a config
        config()->set($configKey, $json);
    }
}
