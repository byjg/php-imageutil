<?php

namespace ByJG\ImageUtil\Image;

use GdImage;
use InvalidArgumentException;
use SVG\SVG;

class SvgImage implements ImageInterface
{
    /**
     * @inheritDoc
     */
    public static function mimeType(): string|array
    {
        return "image/svg+xml";
    }

    /**
     * @inheritDoc
     */
    public static function extension(): string|array
    {
        return ["svg"];
    }

    /**
     * @inheritDoc
     */
    public function load(string $filename): GdImage|SVG
    {
        return SVG::fromFile($filename);
    }

    /**
     * @inheritDoc
     */
    public function save(GdImage|SVG $resource, string $filename = null, array $params = []): void
    {
        if ($resource instanceof SVG) {
            file_put_contents($filename, $resource->toXMLString());
        } else {
            throw new InvalidArgumentException("Cannot convert a GdImage to SVG.");
        }
    }

    /**
     * @inheritDoc
     */
    public function output(GdImage|SVG $resource): void
    {
        if ($resource instanceof SVG) {
            echo $resource->toXMLString();
        } else {
            throw new InvalidArgumentException("The resource is not a SVG object");
        }
    }
}