<?php

namespace Critiq\LaravelHeadManifest\Util;

class ManifestPath extends ManifestElement {

    public $title;
    public $path;
    public $pathSplits;

    /** @var Manifest */
    private $manifest;
    private $vars = [];
    private $meta = [];

    public function __construct($path, $data, Manifest $manifest) {
        $this->title = array_key_exists('title', $data) ? $data['title'] : null;
        $this->path = $path;
        $this->pathSplits = preg_split('@/@', $this->path, 0, PREG_SPLIT_NO_EMPTY);
        $this->manifest = $manifest;
        $this->meta = array_key_exists('meta', $data) ? $data['meta'] : null; 
    }

    /**
     * See if a path matches this path element
     */
    public function matchesPath($requestPathSplits) {
        for($i = 0; $i < max(count($requestPathSplits), count($this->pathSplits)); $i++) {

            $path = $i < count($requestPathSplits) ? $requestPathSplits[$i] : null;
            $match = $i < count($this->pathSplits) ? $this->pathSplits[$i] : null;

            if(isset($match) && $match == '*') {
                // If match is wildcard, this passes
                return true;
            } else if(!isset($match) || !isset($path)) {
                // If match is null, this is a fail
                return false;
            } else if($match[0] == ':') {
                // If match has a prefix of `:`, skip this iteration as it's a variable
                $this->vars[$match] = $path;
                continue;
            } else if($path != $match) {
                // Check if the pattern matches. If not, this is a fail
                return false;
            }

        }

        return true;
    }

    /**
     * Get the title for this path (or the default title
     * if none is provided)
     */
    public function getTitle() {
        return isset($this->title) ? $this->replaceVars($this->title) : $this->manifest->getDefaultTitle();
    }

    /**
     * Builds the attributes we want to convert into HTML
     */
    private function buildMetadata() {

        // Merge all the metadata
        $allMeta = array_merge(
            isset($this->meta) ? $this->meta : $this->manifest->getDefaultMeta(),
            $this->manifest->getGlobalMeta()
        );

        // Map the manifest meta objects to html
        $metadata = [];
        foreach($allMeta as $data) {
            $metadata[] = new ManifestMeta($data, $this->vars);
        }
        
        return $metadata;
    }

    /**
     * Build the title of this path as well as the metadata
     */
    public function toHTML() {
        $values = [];

        // Get this title, or use the default title
        $title = $this->getTitle();

        // If a title is specified, build the HTML string
        if(isset($title)) {
            $varTitle = $this->replaceVars($title);
            $values[] = "<title>$varTitle</title>";
        }

        $html = array_map(function($e) {
            return $e->toHTML();
        }, $this->buildMetadata());

        // Merge the html arrays
        $values = array_merge($values, $html);

        return implode("\n", $values);
    }

    /**
     * Array representation of the path data
     */
    public function toArray() {
        return [
            'title' => $this->getTitle(),
            'meta' => array_map(function($e) { $e->toArray(); }, $this->buildMetadata()),
        ];
    }
    
}