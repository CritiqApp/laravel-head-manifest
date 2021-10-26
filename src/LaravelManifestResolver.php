<?php

namespace Critiq\LaravelHeadManifest;

use Critiq\LaravelHeadManifest\Util\Manifest;
use Critiq\LaravelHeadManifest\Util\ManifestPath;

abstract class LaravelManifestResolver {

    /** @var Manifest */
    private $manifest;

    /** @var ManifestPath */

    public function __construct(Manifest $manifest, ManifestPath $path) {
        $this->manifest = $manifest;
        $this->path = $path;
    }

    /**
     * Build the meta
     * 
     * @return ManifestMeta[]
     */
    public abstract function buildMeta();

    /**
     * Get the resolved title for this path.
     */
    public function buildTitle() {
        return $this->path->getTitle();
    }

}