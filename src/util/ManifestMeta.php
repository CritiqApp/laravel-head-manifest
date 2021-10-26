<?php

namespace Critiq\LaravelHeadManifest\Util;

class ManifestMeta extends ManifestElement {

    private $attributes;
    private $vars;

    public function __construct($data, $vars = []) {
        $this->attributes = $data;
        $this->vars = $vars;
    }

    public function toHTML() {
        $join = '<meta ';
        foreach($this->attributes as $key => $value) {
            $value = $this->replaceVars($value);
            $join .= "$key=\"$value\" ";
        }
        return $join . '/>';
    }

}