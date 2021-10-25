<?php

namespace Critiq\LaravelHeadManifest;

use Critiq\LaravelHeadManifest\Util\Manifest;

class ManifestConverter {

    public static function convert() {
        // Load the manifest
        $configKey = env('LARAVEL_HEAD_MANIFEST_CONFIG_KEY', 'laravel_head_manifest');

        // Get the request path
        $requestPath = request()->getPathInfo();
        $requestPathSplit = preg_split('@/@', $requestPath, 0, PREG_SPLIT_NO_EMPTY);

        $manifest = new Manifest(config()->get($configKey), $requestPathSplit);

        return $manifest->toHTMLString();
    }

}