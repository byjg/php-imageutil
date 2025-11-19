<?php

namespace ByJG\ImageUtil\Image;

use ByJG\ImageUtil\Exception\ImageUtilException;
use GdImage;
use InvalidArgumentException;
use SVG\SVG;

class SvgImage implements ImageInterface
{
    /**
     * @inheritDoc
     */
    #[\Override]
    public static function mimeType(): string|array
    {
        return "image/svg+xml";
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function extension(): string|array
    {
        return ["svg"];
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function load(string $filename): GdImage|SVG
    {
        $image = SVG::fromFile($filename);
        if (is_null($image)) {
            throw new ImageUtilException("The file is not a valid SVG file");
        }
        return $image;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function save(GdImage|SVG $resource, ?string $filename = null, array $params = []): void
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
    #[\Override]
    public function output(GdImage|SVG $resource): void
    {
        if ($resource instanceof SVG) {
            echo $resource->toXMLString();
        } else {
            throw new InvalidArgumentException("The resource is not a SVG object");
        }
    }
}