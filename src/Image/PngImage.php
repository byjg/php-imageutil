<?php

namespace ByJG\ImageUtil\Image;

use GdImage;
use InvalidArgumentException;
use SVG\SVG;

class PngImage implements ImageInterface
{

    /**
     * @inheritDoc
     */
    public static function mimeType(): string|array
    {
        return "image/png";
    }

    /**
     * @inheritDoc
     */
    public static function extension(): string|array
    {
        return "png";
    }

    /**
     * @inheritDoc
     */
    public function load(string $filename): GdImage|SVG
    {
        return imagecreatefrompng($filename);
    }

    /**
     * @inheritDoc
     */
    public function save(GdImage|SVG $resource, ?string $filename = null, array $params = []): void
    {
        if ($resource instanceof SVG) {
            if (!isset($params['width']) || !isset($params['height'])) {
                throw new InvalidArgumentException("The width and height are required to convert SVG to PNG");
            }
            $resource = $resource->toRasterImage($params['width'], $params['height']);
        }
        $pngQuality = intval(round((9 * $params['quality']) / 100));
        imagepng($resource, $filename, $pngQuality);
    }

    /**
     * @inheritDoc
     */
    public function output(GdImage|SVG $resource): void
    {
        imagepng($resource);
    }
}