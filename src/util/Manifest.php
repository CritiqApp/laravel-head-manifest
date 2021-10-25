<?php

namespace Critiq\LaravelHeadManifest\Util;

class Manifest extends ManifestElement {

    public $defaultTitle;
    public $defaultMeta;
    public $globalMeta;
    public $paths = [];

    /** @var float start time of the manifest */
    private $startTime;

    /** @var ManifestPath */
    public $path = null;

    public function __construct($data, $reqestPathSplits) {

        // Set some of the default attribute data
        $this->defaultTitle = array_key_exists('defaultTitle', $data) ? $data['defaultTitle'] : null;
        $this->defaultMeta = is_array($data['defaultMeta']) ? $data['defaultMeta'] : [];
        $this->globalMeta = is_array($data['globalMeta']) ? $data['globalMeta'] : [];

        // Make sure the 'paths' field is specified, and is an array
        if(!is_array($data['paths'])) {
            throw new InvalidHeadManifestException("Root 'paths' field must be specified as an array");
        }

        // Generate the paths
        foreach($data['paths'] as $key => $data) {
            $this->paths[] = new ManifestPath($key, $data, $this);
        }

        // Find the first matching path
        /** @var ManifestPath */
        foreach($this->paths as $path) {
            if($path->matchesPath($reqestPathSplits)) {
                $this->path = $path;
                break;
            }
        }

        $this->startTime = microtime(true);
        
    }

    public function toHTML() {
        
        $values = [];

        // Set the specified title
        if(isset($this->path->title)) {
            $title = $this->path->title;
            $values[] = "<title>$title</title>";
        } else if(isset($this->defaultTitle)) {
            $values[] = "<title>$this->defaultTitle</title>";
        }

        if(isset($this->path)) {
            $values = array_merge($values, $this->path->toHTML());
        } else {
            $values = array_merge($values, array_map(function($e) {
                return (new ManifestMeta($e))->toHTML();
            }, $this->defaultMeta));
        }

        $values = array_merge($values, array_map(function($e) {
            return (new ManifestMeta($e))->toHTML();
        }, $this->globalMeta));

        if(env('APP_DEBUG')) {
            $duration = microtime(true) - $this->startTime;
            $values[] = '<meta name="laravel-head-manifest timer" content="' . $duration . '" />';
        }

        return $values;
    }

    public function toHTMLString() {
        return implode($this->toHTML(), "\n");
    }

}