<?php

namespace Critiq\LaravelHeadManifest;

use Critiq\LaravelHeadManifest\Util\Manifest;
use Critiq\LaravelHeadManifest\Util\ManifestPath;

abstract class PathResolver {

    /** @var Manifest */
    protected $manifest;

    /** @var ManifestPath */
    protected $path;

    public function __construct(Manifest $manifest, ManifestPath $path) {
        $this->manifest = $manifest;
        $this->path = $path;
    }

    /**
     * Build the meta
     * 
     * @return ManifestMeta[]
     */
    public function buildMetadata() {
        return $this->path->buildMetadata();
    }

    /**
     * Get the resolved title for this path.
     * @return string
     */
    public function buildTitle() {
        return $this->path->getTitle();
    }

    /**
     * Helper function to get a variable by name
     */
    public function getVar($name) {
        $vars = $this->path->getVars();
        return array_key_exists(":$name", $vars) ? $vars[":$name"] : null;
    }

    /**
     * Build the HTML for this resolver
     */
    public function toHTML() {

        // Get the title (if any)
        $title = $this->buildTitle();

        // Build the metadata from this resolver
        $metas = $this->buildMetadata();

        $values = [];

        // Add the title (if any)
        if(isset($title)) {
            $values[] = "<title>$title</title>";
        }

        // Add the new meta
        foreach($metas as $meta) {
            $values[] = $meta->toHTML();
        }

        return $values;
    }

}