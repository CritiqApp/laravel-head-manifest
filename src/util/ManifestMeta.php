<?php

namespace Critiq\LaravelHeadManifest\Util;

class ManifestMeta extends ManifestElement {

    private $attributes;

    public function __construct($data) {
        $this->attributes = $data;
    }

    public function toHTML() {
        $join = '<meta ';
        foreach($this->attributes as $key => $value) {
            $join .= "$key=\"$value\" ";
        }
        return [$join . '/>'];
    }

}