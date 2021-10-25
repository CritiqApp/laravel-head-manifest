<?php

namespace Critiq\LaravelHeadManifest\Util;

use Exception;

class InvalidHeadManifestException extends Exception {

    public function __construct($message) {
        parent::__construct($message);
    }

}