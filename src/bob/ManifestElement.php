<?php

namespace Critiq\LaravelHeadManifest\Util;

abstract class ManifestElement {

    /**
     * Replace variables in a string by its definition
     */
    protected function replaceVars($value) {
        
        // Make sure the vars field is defined
        if(!isset($this->vars)) {
            return $value;
        }

        // Replace the string
        foreach($this->vars as $varKey => $varValue) {
            $value = str_replace($varKey, $varValue, $value);
        }

        return $value;
    }

}