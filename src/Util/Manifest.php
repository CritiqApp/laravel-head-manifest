<?php

namespace Critiq\LaravelHeadManifest\Util;

class Manifest extends ManifestElement {

    private $defaultTitle;
    private $defaultMeta;
    private $globalMeta;
    private $startTime;

    /** @var ManifestPath[] registry of paths **/
    public $paths = [];

    /** @var ManifestPath */
    public $defaultPath = null;

    public function __construct($data) {

        // For debugging purposes
        $this->startTime = microtime(true);

        // Set some of the default attribute data
        $this->defaultTitle = array_key_exists('defaultTitle', $data) ? $data['defaultTitle'] : null;
        $this->defaultMeta = array_key_exists('defaultMeta', $data) && is_array($data['defaultMeta']) ? $data['defaultMeta'] : [];
        $this->globalMeta = array_key_exists('globalMeta', $data) && is_array($data['globalMeta']) ? $data['globalMeta'] : [];

        // Make sure the 'paths' field is specified, and is an array
        if(array_key_exists('paths', $data)){
            
            if(!is_array($data['paths'])) {
                throw new InvalidHeadManifestException("Root 'paths' field must be specified as an array");
            }

            // Generate the paths
            foreach($data['paths'] as $key => $data) {
                $this->paths[] = new ManifestPath($key, $data, $this);
            }

        }
        
        // Build the default path is no path is resolveable
        $this->defaultPath = new ManifestPath('', [
            'meta' => $this->defaultMeta,
        ], $this);
        
    }

    /**
     * Find the matched manifest path. If the path
     * can't be resolved, return the default path
     * information
     * 
     * @return ManifestPath
     */
    public function resolvePath($path) {

        $requestPathSplits = preg_split('@/@', $path, 0, PREG_SPLIT_NO_EMPTY);

        // Find the first matching path
        /** @var ManifestPath */
        $resolvedPath = $this->defaultPath;
        foreach($this->paths as $path) {
            if($path->matchesPath($requestPathSplits)) {
                $resolvedPath = $path;
                break;
            }
        }

        // Initialize the resolver (if any)
        $resolver = $resolvedPath->getResolver();
        if(isset($resolver)) {
            $resolver->initialize();
        }

        return $resolvedPath;

    }

    /**
     * Get the default title
     */
    public function getDefaultTitle() {
        return $this->defaultTitle;
    }

    /**
     * Get the default meta
     */
    public function getDefaultMeta() {
        return $this->defaultMeta;
    }

    /**
     * Get the array of global metadata
     */
    public function getGlobalMeta() {
        return $this->globalMeta;
    }

}