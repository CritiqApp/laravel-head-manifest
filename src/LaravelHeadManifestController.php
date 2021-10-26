<?php

namespace Critiq\LaravelHeadManifest;

use Critiq\LaravelHeadManifest\Util\Manifest;
use Illuminate\Routing\Controller as BaseController;

class LaravelHeadManifestController extends BaseController {

    public function index() {
        
        // Get the path to query the head with
        $path = request()->input('path', '/');

        /** @var Manifest */
        $manifest = app()->make(Manifest::class);

        // Build the response
        return response()->json($manifest->resolvePath($path)->toArray());
    }

}