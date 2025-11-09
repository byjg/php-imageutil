<?php

namespace ByJG\ImageUtil\Image;

use GdImage;
use InvalidArgumentException;
use SVG\SVG;

class BmpImage implements ImageInterface
{

    /**
     * @inheritDoc
     */
    public static function mimeType(): string|array
    {
        return [ 'image/bmp', 'image/x-ms-bmp' ];
    }

    /**
     * @inheritDoc
     */
    public static function extension(): string|array
    {
        return "bmp";
    }

    /**
     * @inheritDoc
     */
    public function load(string $filename): GdImage|SVG
    {
        return imagecreatefrombmp($filename);
    }

    /**
     * @inheritDoc
     */
    public function save(GdImage|SVG $resource, ?string $filename = null, array $params = []): void
    {
        if ($resource instanceof SVG) {
            if (!isset($params['width']) || !isset($params['height'])) {
                throw new InvalidArgumentException("The width and height are required to convert SVG to BMP");
            }
            $resource = $resource->toRasterImage($params['width'], $params['height']);
        }
        imagebmp($resource, $filename);
    }

    /**
     * @inheritDoc
     */
    public function output(GdImage|SVG $resource): void
    {
        imagebmp($resource);
    }
}