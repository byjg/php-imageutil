<?php

namespace ByJG\ImageUtil\Image;

use SVG\SVG;

class SVGImage implements ImageInterface
{
    /**
     * @inheritDoc
     */
    public static function mimeType()
    {
        return "image/svg+xml";
    }

    /**
     * @inheritDoc
     */
    public static function extension()
    {
        return ["svg"];
    }

    /**
     * @inheritDoc
     */
    public function load($filename)
    {
        return SVG::fromFile($filename);
    }

    /**
     * @inheritDoc
     */
    public function save($resource, $filename = null, $params = [])
    {
        imagejpeg($resource, $filename, $params['quality']);
    }

    /**
     * @inheritDoc
     */
    public function output($resource)
    {
        if ($resource instanceof SVG) {
            echo $resource->toXMLString();
        } else {
            throw new \InvalidArgumentException("The resource is not a SVG object");
        }
    }
}