<?php

namespace ByJG\ImageUtil\Image;

use GdImage;
use InvalidArgumentException;
use SVG\SVG;

class JpgImage implements ImageInterface
{
    /**
     * @inheritDoc
     */
    public static function mimeType(): string|array
    {
        return "image/jpeg";
    }

    /**
     * @inheritDoc
     */
    public static function extension(): string|array
    {
        return ["jpg", "jpeg"];
    }

    /**
     * @inheritDoc
     */
    public function load(string $filename): GdImage|SVG
    {
        return imagecreatefromjpeg($filename);
    }

    /**
     * @inheritDoc
     */
    public function save(GdImage|SVG $resource, string $filename = null, array $params = []): void
    {
        if ($resource instanceof SVG) {
            if (!isset($params['width']) || !isset($params['height'])) {
                throw new InvalidArgumentException("The width and height are required to convert SVG to JPG");
            }
            $resource = $resource->toRasterImage($params['width'], $params['height']);
        }
        imagejpeg($resource, $filename, $params['quality']);
    }

    /**
     * @inheritDoc
     */
    public function output(GdImage|SVG $resource): void
    {
        imagejpeg($resource);
    }
}