<?php

namespace Critiq\LaravelHeadManifest;

use App\Http\Controllers\Controller;

class LaravelHeadManifestController extends Controller {

    public function index() {
        
        // Get the path to query the head with
        $path = request()->input('path', '/');

    }

}