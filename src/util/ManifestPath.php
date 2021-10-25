<?php

namespace Critiq\LaravelHeadManifest\Util;

class ManifestPath extends ManifestElement {

    public $title;
    public $path;
    public $pathSplits;

    private $meta = [];

    public function __construct($path, $data, Manifest $manifest) {
        $this->title = array_key_exists('title', $data) ? $data['title'] : $manifest->defautTitle;
        $this->path = $path;
        $this->pathSplits = preg_split('@/@', $this->path, 0, PREG_SPLIT_NO_EMPTY);
        $this->meta = $data['meta']; 
    }

    /**
     * See if a path matches this path element
     */
    public function matchesPath($requestPathSplits) {
        for($i = 0; $i < max(count($requestPathSplits), count($this->pathSplits)); $i++) {

            $path = $i < count($requestPathSplits) ? $requestPathSplits[$i] : null;
            $match = $i < count($this->pathSplits) ? $this->pathSplits[$i] : null;

            if(!isset($match) || !isset($path)) {
                // If match is null, this is a fail
                return false;
            } else if($match[0] == ':') {
                // If match has a prefix of `:`, skip this iteration as it's a variable
                continue;
            } else if($match == '*') {
                // If match is a wildcard, this is a success since we don't care
                // about subsequent path variables
                return true;
            } else if($path != $match) {
                // Check if the pattern matches. If not, this is a fail
                return false;
            }

        }

        return true;
    }

    /**
     * Builds the attributes we want to convert into HTML
     */
    private function buildMetadataHTML() {

        // Make sure the meta is an array
        if(!is_array($this->meta)) {
            throw new InvalidHeadManifestException('"meta" field must be an array of meta objects');
        }

        // Map the manifest meta objects to html
        $html = [];
        foreach($this->meta as $data) {
            $html = array_merge($html, (new ManifestMeta($data))->toHTML());
        }
        
        return $html;
    }

    /**
     * Build the title of this path as well as the metadata
     */
    public function toHTML() {
        $values = [];
        $values = array_merge($values, $this->buildMetadataHTML());
        return $values;
    }

    
}